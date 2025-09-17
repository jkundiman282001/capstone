<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\BasicInfo;
use App\Models\Address;
use App\Models\Document;
use App\Models\Ethno;
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

        return view('staff.dashboard', compact(
            'name', 'assignedBarangay', 'provinces', 'municipalities', 'barangays', 'ethnicities',
            'totalScholars', 'newApplicants', 'activeScholars', 'inactiveScholars',
            'alerts', 'barChartData', 'pieChartData', 'performanceChartData',
            'pendingRequirements', 'feedbacks', 'notifications',
            'selectedProvince', 'selectedMunicipality', 'selectedBarangay', 'selectedEthno'
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
        
        // Documents data
        $documents = $user->documents ?? collect();
        
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

        return view('staff.application-view', compact(
            'user', 'basicInfo', 'ethno', 'mailing', 'permanent', 'origin',
            'education', 'familyFather', 'familyMother', 'siblings', 'schoolPref',
            'documents', 'requiredTypes', 'totalRequired', 'approvedCount', 'progressPercent'
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

        // Build query for applicants
        $applicantsQuery = User::with(['basicInfo.fullAddress.address', 'ethno', 'documents'])
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

        $applicants = $applicantsQuery->paginate(20);

        // Get geographic data for filters
        $provinces = Address::select('province')->distinct()->where('province', '!=', '')->orderBy('province')->pluck('province');
        $municipalities = Address::select('municipality')->distinct()->where('municipality', '!=', '')->orderBy('municipality')->pluck('municipality');
        $barangays = Address::select('barangay')->distinct()->where('barangay', '!=', '')->orderBy('barangay')->pluck('barangay');
        $ethnicities = Ethno::orderBy('ethnicity')->get();

        return view('staff.applicants-list', compact(
            'applicants', 'provinces', 'municipalities', 'barangays', 'ethnicities',
            'selectedProvince', 'selectedMunicipality', 'selectedBarangay', 'selectedEthno', 'selectedStatus'
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
}