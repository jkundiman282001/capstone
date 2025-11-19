<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\BasicInfo;
use App\Models\Address;
use App\Models\Document;
use App\Models\Ethno;
use App\Models\ApplicantScore;
use App\Services\ApplicantScoringService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StaffDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = \Auth::guard('staff')->user();
        $name = $user->name;
        $assignedBarangay = $user->assigned_barangay ?? 'All';

        // Get geographic filters from request
        $selectedProvince = $request->get('province');
        $selectedMunicipality = $request->get('municipality');
        $selectedBarangay = $request->get('barangay');
        $selectedEthno = $request->get('ethno');

        // Build query for users with geographic filtering
        $usersQuery = User::with(['basicInfo.fullAddress.address', 'ethno'])
            ->whereHas('basicInfo', function($query) use ($selectedProvince, $selectedMunicipality, $selectedBarangay) {
                if ($selectedProvince) {
                    $query->whereHas('fullAddress.address', function($addrQuery) use ($selectedProvince) {
                        $addrQuery->where('province', $selectedProvince);
                    });
                }
                if ($selectedMunicipality) {
                    $query->whereHas('fullAddress.address', function($addrQuery) use ($selectedMunicipality) {
                        $addrQuery->where('municipality', $selectedMunicipality);
                    });
                }
                if ($selectedBarangay) {
                    $query->whereHas('fullAddress.address', function($addrQuery) use ($selectedBarangay) {
                        $addrQuery->where('barangay', $selectedBarangay);
                    });
                }
            });

        if ($selectedEthno) {
            $usersQuery->where('ethno_id', $selectedEthno);
        }

        $users = $usersQuery->get();

        // Get geographic data for filters
        $provinces = Address::select('province')->distinct()->where('province', '!=', '')->orderBy('province')->pluck('province');
        $municipalities = Address::select('municipality')->distinct()->where('municipality', '!=', '')->orderBy('municipality')->pluck('municipality');
        $barangays = Address::select('barangay')->distinct()->where('barangay', '!=', '')->orderBy('barangay')->pluck('barangay');
        $ethnicities = Ethno::orderBy('ethnicity')->get();

        // Calculate real metrics
        $totalScholars = $users->count();
        $newApplicants = $users->where('created_at', '>=', now()->subDays(30))->count();
        $activeScholars = $users->where('basicInfo.type_assist', '!=', null)->count();
        $inactiveScholars = $totalScholars - $activeScholars;

        // Get scholars per barangay for chart
        $scholarsPerBarangay = $users->groupBy(function($user) {
            return $user->basicInfo->fullAddress->address->barangay ?? 'Unknown';
        })->map->count();

        $barChartData = [
            'labels' => $scholarsPerBarangay->keys()->toArray(),
            'datasets' => [[
                'label' => 'Scholars',
                'backgroundColor' => ['#3b82f6', '#f59e42', '#10b981', '#ef4444', '#8b5cf6', '#06b6d4', '#f97316', '#84cc16'],
                'data' => $scholarsPerBarangay->values()->toArray()
            ]]
        ];

        // Get application status breakdown
        $statusBreakdown = $users->groupBy(function($user) {
            return $user->basicInfo->type_assist ? 'Applied' : 'Not Applied';
        })->map->count();

        $pieChartData = [
            'labels' => $statusBreakdown->keys()->toArray(),
            'datasets' => [[
                'backgroundColor' => ['#10b981', '#f59e42', '#ef4444', '#3b82f6'],
                'data' => $statusBreakdown->values()->toArray()
            ]]
        ];

        // Get academic performance data (mock for now)
        $performanceChartData = [
            'labels' => ['1st Sem 2024', '2nd Sem 2024', '1st Sem 2025'],
            'datasets' => [[
                'label' => 'Average GWA',
                'borderColor' => '#6366f1',
                'backgroundColor' => 'rgba(99,102,241,0.2)',
                'data' => [1.75, 1.85, 1.80]
            ]]
        ];

        // Get real pending requirements
        $pendingRequirements = $this->getPendingRequirements($users);

        // Get real alerts
        $alerts = $this->getAlerts($users);

        // Demo feedbacks (in a real app, fetch from DB)
        $feedbacks = session('feedbacks', []);

        // Fetch unread notifications for staff
        $notifications = $user->unreadNotifications()->take(10)->get();

        // Get prioritized documents (First Come, First Serve)
        $priorityService = new \App\Services\DocumentPriorityService();
        
        // Initialize submitted_at and priorities for existing documents that don't have them
        $uninitializedDocs = \App\Models\Document::where('status', 'pending')
            ->where(function($query) {
                $query->whereNull('submitted_at')
                      ->orWhereNull('priority_score');
            })
            ->whereNotNull('created_at')
            ->get();
        
        foreach ($uninitializedDocs as $doc) {
            if (!$doc->submitted_at) {
                $doc->submitted_at = $doc->created_at;
            }
            $priorityService->calculateDocumentPriority($doc);
        }
        
        // Recalculate ranks if needed
        if ($uninitializedDocs->count() > 0) {
            $priorityService->recalculateAllPriorities();
        }
        
        $prioritizedDocuments = $priorityService->getPrioritizedDocuments('pending', 20);
        $priorityStatistics = $priorityService->getPriorityStatistics();

        // Get overall course prioritization
        $coursePriorityService = new \App\Services\CoursePriorityService();
        $overallCoursePrioritization = $coursePriorityService->getOverallCoursePrioritization();
        $courseStatistics = $coursePriorityService->getCourseStatistics();

        // Get prioritized applicants (FCFS → IP Group → Course)
        $applicantPriorityService = new \App\Services\ApplicantPriorityService();
        $prioritizedApplicants = $applicantPriorityService->getTopPriorityApplicants(50);
        $applicantPriorityStatistics = $applicantPriorityService->getPriorityStatistics();

        return view('staff.dashboard', compact(
            'name', 'assignedBarangay', 'provinces', 'municipalities', 'barangays', 'ethnicities',
            'totalScholars', 'newApplicants', 'activeScholars', 'inactiveScholars',
            'alerts', 'barChartData', 'pieChartData', 'performanceChartData',
            'pendingRequirements', 'feedbacks', 'notifications',
            'selectedProvince', 'selectedMunicipality', 'selectedBarangay', 'selectedEthno',
            'prioritizedDocuments', 'priorityStatistics',
            'overallCoursePrioritization', 'courseStatistics',
            'prioritizedApplicants', 'applicantPriorityStatistics'
        ));
    }

    private function getPendingRequirements($users)
    {
        $pendingRequirements = [];
        
        foreach ($users as $user) {
            $documents = Document::where('user_id', $user->id)->get();
            $requiredTypes = ['birth_certificate', 'income_document', 'tribal_certificate', 'endorsement', 'good_moral', 'grades'];
            
            foreach ($requiredTypes as $type) {
                $document = $documents->where('type', $type)->first();
                
                if (!$document || $document->status === 'pending') {
                    $priority = ($type === 'income_document') ? 1 : 2;
                    $isOverdue = $document ? $document->created_at->diffInDays(now()) > 30 : false;
                    
                    $pendingRequirements[] = (object)[
                        'scholar_name' => $user->first_name . ' ' . $user->last_name,
                        'missing_document' => $this->getDocumentTypeName($type),
                        'is_overdue' => $isOverdue,
                        'priority' => $priority,
                        'submitted_documents' => $documents->where('status', 'approved')->pluck('type')->toArray()
                    ];
                }
            }
        }
        
        return collect($pendingRequirements)->sortBy('priority');
    }

    private function getDocumentTypeName($type)
    {
        $typeNames = [
            'birth_certificate' => 'Certified Birth Certificate',
            'income_document' => 'Certificate of Low Income',
            'tribal_certificate' => 'Tribal Certificate',
            'endorsement' => 'Endorsement Letter',
            'good_moral' => 'Good Moral Certificate',
            'grades' => 'Grade Slip'
        ];
        
        return $typeNames[$type] ?? $type;
    }

    private function getAlerts($users)
    {
        $alerts = [];
        
        foreach ($users as $user) {
            $basicInfo = $user->basicInfo;
            if ($basicInfo && $basicInfo->type_assist) {
                // Check for upcoming deadlines
                $alerts[] = (object)[
                    'scholar_name' => $user->first_name . ' ' . $user->last_name,
                    'message' => 'Application renewal deadline approaching',
                    'due_date' => now()->addDays(30)->format('Y-m-d'),
                    'status' => 'Pending'
                ];
            }
        }
        
        return $alerts;
    }

    public function downloadReport(Request $request)
    {
        // Implement report generation and download logic here
        return back()->with('success', 'Report download feature coming soon!');
    }

    public function submitFeedback(Request $request)
    {
        $validated = $request->validate([
            'feedback_text' => 'required|string|max:1000',
        ]);
        $feedbacks = session('feedbacks', []);
        $feedbacks[] = [
            'text' => $validated['feedback_text'],
            'submitted_at' => now()->format('M d, Y g:i A')
        ];
        session(['feedbacks' => $feedbacks]);
        return back()->with('success', 'Feedback submitted successfully!');
    }

    public function markNotificationsRead(Request $request)
    {
        $user = \Auth::guard('staff')->user();
        if ($user) {
            $user->unreadNotifications->markAsRead();
        }
        return response()->json(['success' => true]);
    }

    public function viewApplication($user)
    {
        $user = User::with([
            'basicInfo.fullAddress.address', 
            'ethno', 
            'documents',
            'applicantScore',
            'basicInfo.education',
            'basicInfo.family.ethno',
            'basicInfo.siblings',
            'basicInfo.schoolPref',
            'basicInfo.fullAddress.mailingAddress.address',
            'basicInfo.fullAddress.permanentAddress.address',
            'basicInfo.fullAddress.origin.address'
        ])->findOrFail($user);

        // Extract the related data
        $basicInfo = $user->basicInfo;
        $ethno = $user->ethno;
        
        // Address data
        $mailing = $basicInfo->fullAddress->mailingAddress ?? null;
        $permanent = $basicInfo->fullAddress->permanentAddress ?? null;
        $origin = $basicInfo->fullAddress->origin ?? null;
        
        // Education data
        $education = $basicInfo->education ?? collect();
        
        // Family data
        $familyFather = $basicInfo->family->where('fam_type', 'father')->first() ?? null;
        $familyMother = $basicInfo->family->where('fam_type', 'mother')->first() ?? null;
        
        // Siblings data
        $siblings = $basicInfo->siblings ?? collect();
        
        // School preference data
        $schoolPref = $basicInfo->schoolPref ?? null;
        
        // Documents data - ordered by priority (First Come, First Serve)
        // Oldest submissions = Highest priority = Rank #1
        $documents = $user->documents()
            ->orderByRaw('CASE WHEN submitted_at IS NOT NULL THEN 0 ELSE 1 END') // NULLs last
            ->orderBy('submitted_at', 'asc') // Earliest first = Highest priority
            ->orderBy('created_at', 'asc') // Tiebreaker
            ->get();
        
        // Required document types
        $requiredTypes = [
            'birth_certificate' => 'Certified Birth Certificate',
            'income_document' => 'Certificate of Low Income',
            'tribal_certificate' => 'Tribal Certificate',
            'endorsement' => 'Endorsement Letter',
            'good_moral' => 'Good Moral Certificate',
            'grades' => 'Grade Slip'
        ];

        // Calculate progress variables
        $totalRequired = count($requiredTypes);
        $approvedCount = $documents->whereIn('type', array_keys($requiredTypes))->where('status', 'approved')->count();
        $progressPercent = $totalRequired > 0 ? round(($approvedCount / $totalRequired) * 100) : 0;

        // Get course prioritization for this specific applicant
        $coursePriorityService = new \App\Services\CoursePriorityService();
        $coursePrioritization = $coursePriorityService->getApplicantCoursePrioritization($user);

        return view('staff.application-view', compact(
            'user', 'basicInfo', 'ethno', 'mailing', 'permanent', 'origin',
            'education', 'familyFather', 'familyMother', 'siblings', 'schoolPref',
            'documents', 'requiredTypes', 'totalRequired', 'approvedCount', 'progressPercent',
            'coursePrioritization'
        ));
    }

    public function applicantsList(Request $request)
    {
        $user = \Auth::guard('staff')->user();

        // Get filters from request
        $selectedProvince = $request->get('province');
        $selectedMunicipality = $request->get('municipality');
        $selectedBarangay = $request->get('barangay');
        $selectedEthno = $request->get('ethno');
        $selectedStatus = $request->get('status');
        $selectedPriority = $request->get('priority');

        // Build query for applicants
        $applicantsQuery = User::with(['basicInfo.fullAddress.address', 'ethno', 'documents', 'applicantScore'])
            ->whereHas('basicInfo', function($query) use ($selectedProvince, $selectedMunicipality, $selectedBarangay) {
                if ($selectedProvince) {
                    $query->whereHas('fullAddress.address', function($addrQuery) use ($selectedProvince) {
                        $addrQuery->where('province', $selectedProvince);
                    });
                }
                if ($selectedMunicipality) {
                    $query->whereHas('fullAddress.address', function($addrQuery) use ($selectedMunicipality) {
                        $addrQuery->where('municipality', $selectedMunicipality);
                    });
                }
                if ($selectedBarangay) {
                    $query->whereHas('fullAddress.address', function($addrQuery) use ($selectedBarangay) {
                        $addrQuery->where('barangay', $selectedBarangay);
                    });
                }
            });

        if ($selectedEthno) {
            $applicantsQuery->where('ethno_id', $selectedEthno);
        }

        if ($selectedStatus) {
            if ($selectedStatus === 'applied') {
                $applicantsQuery->whereHas('basicInfo', function($query) {
                    $query->whereNotNull('type_assist');
                });
            } elseif ($selectedStatus === 'not_applied') {
                $applicantsQuery->whereHas('basicInfo', function($query) {
                    $query->whereNull('type_assist');
                });
            }
        }

        // Apply priority filtering
        if ($selectedPriority) {
            $applicantsQuery->whereHas('applicantScore', function($query) use ($selectedPriority) {
                switch ($selectedPriority) {
                    case 'high':
                        $query->where('total_score', '>=', 80);
                        break;
                    case 'medium':
                        $query->whereBetween('total_score', [60, 79]);
                        break;
                    case 'low':
                        $query->whereBetween('total_score', [40, 59]);
                        break;
                    case 'very_low':
                        $query->where('total_score', '<', 40);
                        break;
                }
            });
        }

        $applicants = $applicantsQuery->paginate(20);

        // Get geographic data for filters
        $provinces = Address::select('province')->distinct()->where('province', '!=', '')->orderBy('province')->pluck('province');
        $municipalities = Address::select('municipality')->distinct()->where('municipality', '!=', '')->orderBy('municipality')->pluck('municipality');
        $barangays = Address::select('barangay')->distinct()->where('barangay', '!=', '')->orderBy('barangay')->pluck('barangay');
        $ethnicities = Ethno::orderBy('ethnicity')->get();

        return view('staff.applicants-list', compact(
            'applicants', 'provinces', 'municipalities', 'barangays', 'ethnicities',
            'selectedProvince', 'selectedMunicipality', 'selectedBarangay', 'selectedEthno', 'selectedStatus', 'selectedPriority'
        ));
    }

    public function applicantPriority()
    {
        $user = \Auth::guard('staff')->user();
        $name = $user->name;
        $assignedBarangay = $user->assigned_barangay ?? 'All';

        $applicantPriorityService = new \App\Services\ApplicantPriorityService();
        $prioritizedApplicants = $applicantPriorityService->getTopPriorityApplicants(50);
        $applicantPriorityStatistics = $applicantPriorityService->getPriorityStatistics();

        $notifications = $user->unreadNotifications()->take(10)->get();

        return view('staff.priorities.applicants', compact(
            'name',
            'assignedBarangay',
            'prioritizedApplicants',
            'applicantPriorityStatistics',
            'notifications'
        ));
    }

    public function documentPriority()
    {
        $user = \Auth::guard('staff')->user();
        $name = $user->name;
        $assignedBarangay = $user->assigned_barangay ?? 'All';

        $priorityService = new \App\Services\DocumentPriorityService();

        $uninitializedDocs = \App\Models\Document::where('status', 'pending')
            ->where(function($query) {
                $query->whereNull('submitted_at')
                      ->orWhereNull('priority_score');
            })
            ->whereNotNull('created_at')
            ->get();

        foreach ($uninitializedDocs as $doc) {
            if (!$doc->submitted_at) {
                $doc->submitted_at = $doc->created_at;
            }
            $priorityService->calculateDocumentPriority($doc);
        }

        if ($uninitializedDocs->count() > 0) {
            $priorityService->recalculateAllPriorities();
        }

        $prioritizedDocuments = $priorityService->getPrioritizedDocuments('pending', 50);
        $priorityStatistics = $priorityService->getPriorityStatistics();

        $notifications = $user->unreadNotifications()->take(10)->get();

        return view('staff.priorities.documents', compact(
            'name',
            'assignedBarangay',
            'prioritizedDocuments',
            'priorityStatistics',
            'notifications'
        ));
    }

    public function ipPriority()
    {
        $user = \Auth::guard('staff')->user();
        $name = $user->name;
        $assignedBarangay = $user->assigned_barangay ?? 'All';

        $priorityService = new \App\Services\DocumentPriorityService();

        $uninitializedDocs = \App\Models\Document::where('status', 'pending')
            ->where(function($query) {
                $query->whereNull('submitted_at')
                      ->orWhereNull('priority_score');
            })
            ->whereNotNull('created_at')
            ->get();

        foreach ($uninitializedDocs as $doc) {
            if (!$doc->submitted_at) {
                $doc->submitted_at = $doc->created_at;
            }
            $priorityService->calculateDocumentPriority($doc);
        }

        if ($uninitializedDocs->count() > 0) {
            $priorityService->recalculateAllPriorities();
        }

        $prioritizedDocuments = $priorityService->getPrioritizedDocuments('pending', 100);

        $priorityGroupsSet = ["b'laan", 'bagobo', 'kalagan', 'kaulo'];
        $priorityIpDocs = $prioritizedDocuments->filter(function($doc) use ($priorityGroupsSet) {
            $eth = optional(optional($doc->user)->ethno)->ethnicity;
            return $eth && in_array(strtolower(trim($eth)), $priorityGroupsSet, true);
        });

        $notifications = $user->unreadNotifications()->take(10)->get();

        return view('staff.priorities.ip', compact(
            'name',
            'assignedBarangay',
            'priorityIpDocs',
            'notifications'
        ));
    }

    public function coursePriority()
    {
        $user = \Auth::guard('staff')->user();
        $name = $user->name;
        $assignedBarangay = $user->assigned_barangay ?? 'All';

        $coursePriorityService = new \App\Services\CoursePriorityService();
        $overallCoursePrioritization = $coursePriorityService->getOverallCoursePrioritization();
        $courseStatistics = $coursePriorityService->getCourseStatistics();

        $notifications = $user->unreadNotifications()->take(10)->get();

        return view('staff.priorities.courses', compact(
            'name',
            'assignedBarangay',
            'overallCoursePrioritization',
            'courseStatistics',
            'notifications'
        ));
    }

    public function tribalCertificatePriority()
    {
        $user = \Auth::guard('staff')->user();
        $name = $user->name;
        $assignedBarangay = $user->assigned_barangay ?? 'All';

        // Get all users who have approved tribal certificates
        $usersWithApprovedTribalCert = User::whereHas('documents', function($query) {
            $query->where('type', 'tribal_certificate')
                  ->where('status', 'approved');
        })
        ->with(['documents' => function($query) {
            $query->where('type', 'tribal_certificate')
                  ->where('status', 'approved')
                  ->orderBy('created_at', 'desc');
        }, 'ethno', 'basicInfo'])
        ->get();

        // Sort by when the certificate was approved (most recently approved first)
        $prioritizedUsers = $usersWithApprovedTribalCert->sortByDesc(function($user) {
            $approvedCert = $user->documents->where('type', 'tribal_certificate')
                                          ->where('status', 'approved')
                                          ->first();
            return $approvedCert ? $approvedCert->updated_at : null;
        })->values();

        // Get statistics
        $totalApproved = $prioritizedUsers->count();
        $recentlyApproved = $prioritizedUsers->filter(function($user) {
            $approvedCert = $user->documents->where('type', 'tribal_certificate')
                                          ->where('status', 'approved')
                                          ->first();
            if (!$approvedCert) return false;
            return $approvedCert->updated_at->isAfter(now()->subDays(7));
        })->count();

        $notifications = $user->unreadNotifications()->take(10)->get();

        return view('staff.priorities.tribal-certificate', compact(
            'name',
            'assignedBarangay',
            'prioritizedUsers',
            'totalApproved',
            'recentlyApproved',
            'notifications'
        ));
    }

    public function updateDocumentStatus(Request $request, $document)
    {
        $document = Document::findOrFail($document);
        
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected,pending'
        ]);
        
        $document->update([
            'status' => $validated['status']
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Document status updated successfully'
        ]);
    }

    public function extractGrades($userId)
    {
        $user = \App\Models\User::with('documents')->findOrFail($userId);
        $gradesDoc = $user->documents->where('type', 'grades')->first();
        if ($gradesDoc) {
            $pdfPath = storage_path('app/' . $gradesDoc->filepath);
            $gemini = new \App\Services\GeminiService();
            if (!file_exists($pdfPath)) {
                return response()->json(['success' => false, 'error' => 'Grades document file not found.'], 404);
            }
            $text = $gemini->extractGradesTextFromPdf($pdfPath);
            return response()->json(['success' => true, 'text' => $text]);
        } else {
            return response()->json(['success' => false, 'error' => 'No grades document found.'], 404);
        }
    }

    /**
     * Calculate scores for all applicants
     */
    public function calculateAllScores()
    {
        try {
            $scoringService = new ApplicantScoringService();
            $results = $scoringService->calculateAllApplicantScores();
            
            return response()->json([
                'success' => true,
                'message' => 'Scores calculated successfully for ' . count($results) . ' applicants',
                'results' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error calculating scores: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get top priority applicants
     */
    public function getTopPriorityApplicants(Request $request)
    {
        $limit = $request->get('limit', 10);
        $scoringService = new ApplicantScoringService();
        $topApplicants = $scoringService->getTopPriorityApplicants($limit);
        
        return response()->json([
            'success' => true,
            'applicants' => $topApplicants
        ]);
    }

    /**
     * Get scoring statistics
     */
    public function getScoringStatistics()
    {
        $scoringService = new ApplicantScoringService();
        $statistics = $scoringService->getScoringStatistics();
        
        return response()->json([
            'success' => true,
            'statistics' => $statistics
        ]);
    }

    /**
     * Calculate score for a specific applicant
     */
    public function calculateApplicantScore($userId)
    {
        try {
            $user = User::with(['basicInfo', 'documents', 'ethno', 'basicInfo.family', 'basicInfo.siblings', 'basicInfo.education'])
                ->findOrFail($userId);
            
            $scoringService = new ApplicantScoringService();
            $score = $scoringService->calculateApplicantScore($user);
            
            return response()->json([
                'success' => true,
                'score' => $score,
                'priority_level' => $score->priority_level,
                'priority_color' => $score->priority_color
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error calculating score: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Recalculate document priorities (First Come, First Serve)
     */
    public function recalculateDocumentPriorities()
    {
        try {
            $priorityService = new \App\Services\DocumentPriorityService();
            $results = $priorityService->recalculateAllPriorities();
            
            return response()->json([
                'success' => true,
                'message' => 'Document priorities recalculated successfully',
                'total_documents' => $results['total_documents'],
                'documents' => $results['documents']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error recalculating priorities: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get prioritized documents for review
     */
    public function getPrioritizedDocuments(Request $request)
    {
        $status = $request->get('status', 'pending');
        $limit = $request->get('limit', 20);
        
        $priorityService = new \App\Services\DocumentPriorityService();
        $documents = $priorityService->getPrioritizedDocuments($status, $limit);
        
        return response()->json([
            'success' => true,
            'documents' => $documents->map(function($doc) {
                return [
                    'id' => $doc->id,
                    'type' => $doc->type,
                    'filename' => $doc->filename,
                    'applicant_name' => $doc->user->first_name . ' ' . $doc->user->last_name,
                    'applicant_id' => $doc->user_id,
                    'priority_rank' => $doc->priority_rank,
                    'priority_score' => $doc->priority_score,
                    'priority_level' => $doc->priority_level,
                    'waiting_hours' => $doc->waiting_hours,
                    'submitted_at' => $doc->submitted_at ? $doc->submitted_at->format('Y-m-d H:i:s') : null,
                    'status' => $doc->status,
                ];
            })
        ]);
    }

    /**
     * Get document priority statistics
     */
    public function getDocumentPriorityStatistics()
    {
        $priorityService = new \App\Services\DocumentPriorityService();
        $statistics = $priorityService->getPriorityStatistics();
        
        return response()->json([
            'success' => true,
            'statistics' => $statistics
        ]);
    }
}