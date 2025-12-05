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
            return optional(optional(optional($user->basicInfo)->fullAddress)->address)->barangay ?? 'Unknown';
        })->map->count()->sortDesc()->take(10);

        $barChartData = [
            'labels' => $scholarsPerBarangay->keys()->toArray(),
            'datasets' => [[
                'label' => 'Scholars per Barangay',
                'backgroundColor' => 'rgba(99, 102, 241, 0.8)',
                'borderColor' => 'rgba(99, 102, 241, 1)',
                'borderWidth' => 2,
                'borderRadius' => 8,
                'data' => $scholarsPerBarangay->values()->toArray()
            ]]
        ];

        // Get application status breakdown
        $statusBreakdown = $users->filter(function($user) {
            return $user->basicInfo !== null;
        })->groupBy(function($user) {
            $status = optional($user->basicInfo)->application_status ?? 'pending';
            if ($status === 'validated') {
                return 'Validated';
            } elseif ($user->basicInfo->type_assist) {
                return 'Applied - Pending';
            } else {
                return 'Not Applied';
            }
        })->map->count();

        $pieChartData = [
            'labels' => $statusBreakdown->keys()->toArray(),
            'datasets' => [[
                'backgroundColor' => [
                    'rgba(16, 185, 129, 0.9)',  // Green for Validated
                    'rgba(245, 158, 66, 0.9)',  // Orange for Applied
                    'rgba(239, 68, 68, 0.9)',   // Red for Not Applied
                    'rgba(59, 130, 246, 0.9)'   // Blue
                ],
                'borderColor' => ['#ffffff', '#ffffff', '#ffffff', '#ffffff'],
                'borderWidth' => 3,
                'data' => $statusBreakdown->values()->toArray()
            ]]
        ];

        // Get IP Group distribution for chart
        $ipGroupDistribution = $users->groupBy(function($user) {
            return optional($user->ethno)->ethnicity ?? 'Not Specified';
        })->map->count()->sortDesc();

        $ipChartData = [
            'labels' => $ipGroupDistribution->keys()->toArray(),
            'datasets' => [[
                'label' => 'Applicants per IP Group',
                'backgroundColor' => [
                    'rgba(139, 92, 246, 0.8)',  // Purple
                    'rgba(236, 72, 153, 0.8)',  // Pink
                    'rgba(59, 130, 246, 0.8)',  // Blue
                    'rgba(16, 185, 129, 0.8)',  // Green
                    'rgba(245, 158, 66, 0.8)',  // Orange
                    'rgba(239, 68, 68, 0.8)',   // Red
                    'rgba(6, 182, 212, 0.8)',   // Cyan
                    'rgba(132, 204, 22, 0.8)',  // Lime
                    'rgba(168, 85, 247, 0.8)',  // Violet
                    'rgba(234, 179, 8, 0.8)'    // Yellow
                ],
                'borderColor' => [
                    'rgba(139, 92, 246, 1)',
                    'rgba(236, 72, 153, 1)',
                    'rgba(59, 130, 246, 1)',
                    'rgba(16, 185, 129, 1)',
                    'rgba(245, 158, 66, 1)',
                    'rgba(239, 68, 68, 1)',
                    'rgba(6, 182, 212, 1)',
                    'rgba(132, 204, 22, 1)',
                    'rgba(168, 85, 247, 1)',
                    'rgba(234, 179, 8, 1)'
                ],
                'borderWidth' => 2,
                'borderRadius' => 8,
                'data' => $ipGroupDistribution->values()->toArray()
            ]]
        ];

        // Get real pending requirements (still needed for notification bar)
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
            'alerts', 'barChartData', 'pieChartData', 'ipChartData',
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
            'birth_certificate' => 'Original or Certified True Copy of Birth Certificate',
            'income_document' => 'Income Tax Return of the parents/guardians or Certificate of Tax Exemption from BIR or Certificate of Indigency signed by the barangay captain',
            'tribal_certificate' => 'Certificate of Tribal Membership/Certificate of Confirmation COC',
            'endorsement' => 'Endorsement of the IPS/IP Traditional Leaders',
            'good_moral' => 'Certificate of Good Moral from the Guidance Counselor',
            'grades' => 'Incoming First Year College (Senior High School Grades), Ongoing college students latest copy of grades',
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
            'birth_certificate' => 'Original or Certified True Copy of Birth Certificate',
            'income_document' => 'Income Tax Return of the parents/guardians or Certificate of Tax Exemption from BIR or Certificate of Indigency signed by the barangay captain',
            'tribal_certificate' => 'Certificate of Tribal Membership/Certificate of Confirmation COC',
            'endorsement' => 'Endorsement of the IPS/IP Traditional Leaders',
            'good_moral' => 'Certificate of Good Moral from the Guidance Counselor',
            'grades' => 'Incoming First Year College (Senior High School Grades), Ongoing college students latest copy of grades',
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

        // Build query for applicants - Exclude validated applicants (they appear in masterlist)
        $applicantsQuery = User::with(['basicInfo.fullAddress.address', 'ethno', 'documents', 'applicantScore'])
            ->whereHas('basicInfo', function($query) use ($selectedProvince, $selectedMunicipality, $selectedBarangay) {
                // Exclude validated applicants - they should only appear in masterlist
                $query->where(function($q) {
                    $q->where('application_status', '!=', 'validated')
                      ->orWhereNull('application_status');
                });
                
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

    public function incomeTaxPriority()
    {
        $user = \Auth::guard('staff')->user();
        $name = $user->name;
        $assignedBarangay = $user->assigned_barangay ?? 'All';

        // Get all users who have approved income tax documents
        $usersWithApprovedIncomeTax = User::whereHas('documents', function($query) {
            $query->where('type', 'income_document')
                  ->where('status', 'approved');
        })
        ->with(['documents' => function($query) {
            $query->where('type', 'income_document')
                  ->where('status', 'approved')
                  ->orderBy('created_at', 'desc');
        }, 'ethno', 'basicInfo'])
        ->get();

        // Sort by when the income tax document was approved (most recently approved first)
        $prioritizedUsers = $usersWithApprovedIncomeTax->sortByDesc(function($user) {
            $approvedDoc = $user->documents->where('type', 'income_document')
                                          ->where('status', 'approved')
                                          ->first();
            return $approvedDoc ? $approvedDoc->updated_at : null;
        })->values();

        // Get statistics
        $totalApproved = $prioritizedUsers->count();
        $recentlyApproved = $prioritizedUsers->filter(function($user) {
            $approvedDoc = $user->documents->where('type', 'income_document')
                                          ->where('status', 'approved')
                                          ->first();
            if (!$approvedDoc) return false;
            return $approvedDoc->updated_at->isAfter(now()->subDays(7));
        })->count();

        $notifications = $user->unreadNotifications()->take(10)->get();

        return view('staff.priorities.income-tax', compact(
            'name',
            'assignedBarangay',
            'prioritizedUsers',
            'totalApproved',
            'recentlyApproved',
            'notifications'
        ));
    }

    public function academicPerformancePriority()
    {
        $user = \Auth::guard('staff')->user();
        $name = $user->name;
        $assignedBarangay = $user->assigned_barangay ?? 'All';

        // Get all users who have approved grades documents
        $usersWithApprovedGrades = User::whereHas('documents', function($query) {
            $query->where('type', 'grades')
                  ->where('status', 'approved');
        })
        ->with(['documents' => function($query) {
            $query->where('type', 'grades')
                  ->where('status', 'approved')
                  ->orderBy('created_at', 'desc');
        }, 'ethno', 'basicInfo'])
        ->get();

        // Sort by when the grades document was approved (most recently approved first)
        $prioritizedUsers = $usersWithApprovedGrades->sortByDesc(function($user) {
            $approvedDoc = $user->documents->where('type', 'grades')
                                          ->where('status', 'approved')
                                          ->first();
            return $approvedDoc ? $approvedDoc->updated_at : null;
        })->values();

        // Get statistics
        $totalApproved = $prioritizedUsers->count();
        $recentlyApproved = $prioritizedUsers->filter(function($user) {
            $approvedDoc = $user->documents->where('type', 'grades')
                                          ->where('status', 'approved')
                                          ->first();
            if (!$approvedDoc) return false;
            return $approvedDoc->updated_at->isAfter(now()->subDays(7));
        })->count();

        $notifications = $user->unreadNotifications()->take(10)->get();

        return view('staff.priorities.academic-performance', compact(
            'name',
            'assignedBarangay',
            'prioritizedUsers',
            'totalApproved',
            'recentlyApproved',
            'notifications'
        ));
    }

    public function otherRequirementsPriority()
    {
        $user = \Auth::guard('staff')->user();
        $name = $user->name;
        $assignedBarangay = $user->assigned_barangay ?? 'All';

        // Other required documents: birth_certificate, endorsement, good_moral
        $otherRequiredTypes = ['birth_certificate', 'endorsement', 'good_moral'];

        // Get all users who have ALL other required documents approved
        $usersWithAllOtherDocs = User::whereHas('documents', function($query) use ($otherRequiredTypes) {
            $query->whereIn('type', $otherRequiredTypes)
                  ->where('status', 'approved');
        })
        ->with(['documents' => function($query) use ($otherRequiredTypes) {
            $query->whereIn('type', $otherRequiredTypes)
                  ->where('status', 'approved')
                  ->orderBy('created_at', 'desc');
        }, 'ethno', 'basicInfo'])
        ->get();

        // Filter to only include users who have ALL three documents approved
        $prioritizedUsers = $usersWithAllOtherDocs->filter(function($user) use ($otherRequiredTypes) {
            $approvedDocs = $user->documents
                ->whereIn('type', $otherRequiredTypes)
                ->where('status', 'approved');
            
            // Check if user has all three required documents approved
            $hasBirthCert = $approvedDocs->where('type', 'birth_certificate')->isNotEmpty();
            $hasEndorsement = $approvedDocs->where('type', 'endorsement')->isNotEmpty();
            $hasGoodMoral = $approvedDocs->where('type', 'good_moral')->isNotEmpty();
            
            return $hasBirthCert && $hasEndorsement && $hasGoodMoral;
        });

        // Sort by when the last document was approved (most recently completed first)
        $prioritizedUsers = $prioritizedUsers->sortByDesc(function($user) use ($otherRequiredTypes) {
            $approvedDocs = $user->documents
                ->whereIn('type', $otherRequiredTypes)
                ->where('status', 'approved');
            
            // Get the most recent approval date among all three documents
            $latestApproval = $approvedDocs->max('updated_at');
            return $latestApproval;
        })->values();

        // Get statistics
        $totalApproved = $prioritizedUsers->count();
        $recentlyApproved = $prioritizedUsers->filter(function($user) use ($otherRequiredTypes) {
            $approvedDocs = $user->documents
                ->whereIn('type', $otherRequiredTypes)
                ->where('status', 'approved');
            
            $latestApproval = $approvedDocs->max('updated_at');
            if (!$latestApproval) return false;
            return $latestApproval->isAfter(now()->subDays(7));
        })->count();

        $notifications = $user->unreadNotifications()->take(10)->get();

        return view('staff.priorities.other-requirements', compact(
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
        
        // Validate based on status
        $status = $request->input('status');
        
        if ($status === 'rejected') {
            $validated = $request->validate([
                'status' => 'required|in:approved,rejected,pending',
                'rejection_reason' => 'required|string|min:10|max:1000'
            ], [
                'rejection_reason.required' => 'Please provide a reason for rejection.',
                'rejection_reason.min' => 'Rejection reason must be at least 10 characters long.'
            ]);
        } else {
            $validated = $request->validate([
                'status' => 'required|in:approved,rejected,pending',
                'rejection_reason' => 'nullable|string|max:1000'
            ]);
        }
        
        $updateData = [
            'status' => $validated['status']
        ];
        
        // Handle rejection_reason based on status
        if ($validated['status'] === 'rejected') {
            // If status is rejected, save the rejection_reason (trimmed)
            $updateData['rejection_reason'] = trim($validated['rejection_reason']);
        } else {
            // Clear rejection_reason if status is changed to approved or pending
            $updateData['rejection_reason'] = null;
        }
        
        $document->update($updateData);
        
        return response()->json([
            'success' => true,
            'message' => 'Document status updated successfully',
            'rejection_reason' => $updateData['rejection_reason'] ?? null
        ]);
    }

    public function updateApplicationStatus(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        $basicInfo = \App\Models\BasicInfo::where('user_id', $user->id)->firstOrFail();
        
        $validated = $request->validate([
            'status' => 'required|in:pending,validated'
        ]);
        
        $basicInfo->update([
            'application_status' => $validated['status']
        ]);
        
        // Send notification to user
        $user->notify(new \App\Notifications\ApplicationStatusUpdated($validated['status']));
        
        return response()->json([
            'success' => true,
            'message' => 'Application status updated successfully'
        ]);
    }

    public function moveToPamana(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        $basicInfo = \App\Models\BasicInfo::where('user_id', $user->id)->firstOrFail();
        
        // Check if application is validated
        if ($basicInfo->application_status !== 'validated') {
            return response()->json([
                'success' => false,
                'message' => 'Application must be validated before moving to Pamana'
            ], 400);
        }
        
        // Update type_assist to Pamana
        $basicInfo->update([
            'type_assist' => 'Pamana'
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Application moved to Pamana successfully'
        ]);
    }

    public function extractGrades($userId)
    {
        $user = \App\Models\User::with('documents')->findOrFail($userId);
        $gradesDoc = $user->documents->where('type', 'grades')->first();
        if ($gradesDoc) {
            // Use public storage path
            $filePath = storage_path('app/public/' . $gradesDoc->filepath);
            
            // Fallback to app storage if not found in public
            if (!file_exists($filePath)) {
                $filePath = storage_path('app/' . $gradesDoc->filepath);
            }
            
            if (!file_exists($filePath)) {
                return response()->json(['success' => false, 'error' => 'Grades document file not found.'], 404);
            }
            
            // Get file type from database (more reliable than detection)
            $storedFileType = $gradesDoc->filetype;
            $isImage = $storedFileType && strpos($storedFileType, 'image/') === 0;
            
            // Try non-AI extraction service first (OCR + regex parsing)
            $extractionService = new \App\Services\GradeExtractionService();
            $gpa = $extractionService->extractGPA($filePath);
            $extractionMethod = 'ocr';
            
            // If OCR extraction fails, fallback to AI service (Gemini)
            if ($gpa === null) {
                \Log::info('OCR extraction failed, trying AI fallback', [
                    'file_path' => $filePath,
                    'stored_filetype' => $storedFileType
                ]);
                
                try {
                    $geminiService = new \App\Services\GeminiService();
                    $gpa = $geminiService->extractGPA($filePath);
                    $extractionMethod = 'ai';
                } catch (\Exception $e) {
                    \Log::error('AI extraction also failed', [
                        'file_path' => $filePath,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            if ($gpa === null) {
                // Use stored file type for more accurate error message
                $errorMsg = $isImage 
                    ? 'Failed to extract GPA from image. Please ensure Tesseract OCR is installed and the document contains a visible GPA value. Alternatively, the AI extraction may have failed - please check your GEMINI_API_KEY in .env file.'
                    : 'Failed to extract GPA from PDF. Please ensure pdftotext is available or the PDF has extractable text. Alternatively, the AI extraction may have failed - please check your GEMINI_API_KEY in .env file.';
                
                return response()->json([
                    'success' => false, 
                    'error' => $errorMsg,
                    'file_type' => $storedFileType ?? mime_content_type($filePath)
                ], 500);
            }
            
            return response()->json([
                'success' => true, 
                'gpa' => $gpa,
                'file_type' => $storedFileType ?? mime_content_type($filePath),
                'method' => $extractionMethod
            ]);
        } else {
            return response()->json(['success' => false, 'error' => 'No grades document found.'], 404);
        }
    }

    /**
     * Update GPA manually for a user
     */
    public function updateGPA(Request $request, $userId)
    {
        $user = User::with('basicInfo.education')->findOrFail($userId);
        
        $validated = $request->validate([
            'gpa' => 'required|numeric|min:1.0|max:5.0'
        ], [
            'gpa.required' => 'GPA value is required.',
            'gpa.numeric' => 'GPA must be a number.',
            'gpa.min' => 'GPA must be at least 1.0.',
            'gpa.max' => 'GPA cannot exceed 5.0.',
        ]);
        
        // Get the most recent education record (usually college level)
        $education = $user->basicInfo->education ?? collect();
        
        if ($education->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No education records found for this student. Please ensure the student has completed their application.'
            ], 404);
        }
        
        // Update the most recent education record (latest by year_grad or created_at)
        $latestEducation = $education->sortByDesc('year_grad')->sortByDesc('created_at')->first();
        
        if ($latestEducation) {
            $latestEducation->grade_ave = $validated['gpa'];
            $latestEducation->save();
            
            return response()->json([
                'success' => true,
                'message' => 'GPA updated successfully.',
                'gpa' => $validated['gpa'],
                'education_id' => $latestEducation->id
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Could not find education record to update.'
        ], 404);
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

    /**
     * Masterlist - Regular Scholarship
     */
    public function masterlistRegular(Request $request)
    {
        $user = \Auth::guard('staff')->user();

        // Get filters from request
        $selectedProvince = $request->get('province');
        $selectedMunicipality = $request->get('municipality');
        $selectedBarangay = $request->get('barangay');
        $selectedEthno = $request->get('ethno');

        // Build query for Regular scholarship applicants - Only show approved applications
        $applicantsQuery = User::with(['basicInfo.fullAddress.address', 'ethno', 'documents', 'applicantScore'])
            ->whereHas('basicInfo', function($query) use ($selectedProvince, $selectedMunicipality, $selectedBarangay) {
                $query->where('type_assist', 'Regular')
                      ->where('application_status', 'validated'); // Only show validated/approved applications
                
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

        $applicants = $applicantsQuery->orderBy('created_at', 'desc')->paginate(20);

        // Get geographic data for filters
        $provinces = Address::select('province')->distinct()->where('province', '!=', '')->orderBy('province')->pluck('province');
        $municipalities = Address::select('municipality')->distinct()->where('municipality', '!=', '')->orderBy('municipality')->pluck('municipality');
        $barangays = Address::select('barangay')->distinct()->where('barangay', '!=', '')->orderBy('barangay')->pluck('barangay');
        $ethnicities = Ethno::orderBy('ethnicity')->get();

        return view('staff.applicants-list', compact(
            'applicants', 'provinces', 'municipalities', 'barangays', 'ethnicities',
            'selectedProvince', 'selectedMunicipality', 'selectedBarangay', 'selectedEthno'
        ))->with('masterlistType', 'Regular');
    }

    /**
     * Masterlist - Pamana Scholarship
     */
    public function masterlistPamana(Request $request)
    {
        $user = \Auth::guard('staff')->user();

        // Get filters from request
        $selectedProvince = $request->get('province');
        $selectedMunicipality = $request->get('municipality');
        $selectedBarangay = $request->get('barangay');
        $selectedEthno = $request->get('ethno');

        // Build query for Pamana scholarship applicants - Only show approved applications
        $applicantsQuery = User::with(['basicInfo.fullAddress.address', 'ethno', 'documents', 'applicantScore'])
            ->whereHas('basicInfo', function($query) use ($selectedProvince, $selectedMunicipality, $selectedBarangay) {
                $query->where('type_assist', 'Pamana')
                      ->where('application_status', 'validated'); // Only show validated/approved applications
                
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

        $applicants = $applicantsQuery->orderBy('created_at', 'desc')->paginate(20);

        // Get geographic data for filters
        $provinces = Address::select('province')->distinct()->where('province', '!=', '')->orderBy('province')->pluck('province');
        $municipalities = Address::select('municipality')->distinct()->where('municipality', '!=', '')->orderBy('municipality')->pluck('municipality');
        $barangays = Address::select('barangay')->distinct()->where('barangay', '!=', '')->orderBy('barangay')->pluck('barangay');
        $ethnicities = Ethno::orderBy('ethnicity')->get();

        return view('staff.applicants-list', compact(
            'applicants', 'provinces', 'municipalities', 'barangays', 'ethnicities',
            'selectedProvince', 'selectedMunicipality', 'selectedBarangay', 'selectedEthno'
        ))->with('masterlistType', 'Pamana');
    }
}