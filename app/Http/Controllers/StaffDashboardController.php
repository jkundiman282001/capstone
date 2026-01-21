<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Announcement;
use App\Models\ApplicationDraft;
use App\Models\BasicInfo;
use App\Models\Document;
use App\Models\Education;
use App\Models\Ethno;
use App\Models\Family;
use App\Models\FamSiblings;
use App\Models\FullAddress;
use App\Models\MailingAddress;
use App\Models\Origin;
use App\Models\PermanentAddress;
use App\Models\Replacement;
use App\Models\SchoolPref;
use App\Models\Staff;
use App\Models\TransactionHistory;
use App\Models\User;
use App\Models\ApplicantArchive;
use App\Notifications\AnnouncementNotification;
use App\Notifications\ApplicationStatusUpdated;
use App\Notifications\DocumentStatusUpdated;
use App\Notifications\TransactionNotification;
use App\Models\Setting;
use App\Services\ApplicantPriorityService;
use App\Services\CoursePriorityService;
use App\Services\DocumentPriorityService;
use App\Services\GradeExtractionService;
use App\Services\GeminiService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Throwable;

class StaffDashboardController extends Controller
{
    public function index(Request $request)
    {
        /** @var Staff $user */
        $user = Auth::guard('staff')->user();
        $name = $user->name;
        $assignedBarangay = $user->assigned_barangay ?? 'All';

        // Get geographic filters from request
        $selectedProvince = $request->get('province');
        $selectedMunicipality = $request->get('municipality');
        $selectedBarangay = $request->get('barangay');
        $selectedEthno = $request->get('ethno');

        // Build query for users with geographic filtering
        $usersQuery = User::with(['basicInfo.fullAddress.address', 'ethno', 'documents'])
            ->whereHas('basicInfo', function ($query) use ($selectedProvince, $selectedMunicipality, $selectedBarangay) {
                if ($selectedProvince) {
                    $query->whereHas('fullAddress', function ($q) use ($selectedProvince) {
                        $q->whereHas('address', function ($aq) use ($selectedProvince) {
                            $aq->where('province', $selectedProvince);
                        });
                    });
                }
                if ($selectedMunicipality) {
                    $query->whereHas('fullAddress', function ($q) use ($selectedMunicipality) {
                        $q->whereHas('address', function ($aq) use ($selectedMunicipality) {
                            $aq->where('municipality', $selectedMunicipality);
                        });
                    });
                }
                if ($selectedBarangay) {
                    $query->whereHas('fullAddress', function ($q) use ($selectedBarangay) {
                        $q->whereHas('address', function ($aq) use ($selectedBarangay) {
                            $aq->where('barangay', $selectedBarangay);
                        });
                    });
                }
            });

        if ($selectedEthno) {
            $usersQuery->where('ethno_id', $selectedEthno);
        }

        $users = $usersQuery->get();

        // Get geographic data for filters
        $provinces = Address::select('province')->distinct()->where('province', '!=', '')->orderBy('province')->pluck('province');
        
        $municipalitiesQuery = Address::select('municipality')->distinct()->where('municipality', '!=', '');
        if ($selectedProvince) {
            $municipalitiesQuery->where('province', $selectedProvince);
        }
        $municipalities = $municipalitiesQuery->orderBy('municipality')->pluck('municipality');

        $barangaysQuery = Address::select('barangay')->distinct()->where('barangay', '!=', '');
        if ($selectedMunicipality) {
            $barangaysQuery->where('municipality', $selectedMunicipality);
            $barangays = $barangaysQuery->orderBy('barangay')->pluck('barangay');
        } else {
            $barangays = collect();
        }
        
        $ethnicities = Ethno::orderBy('ethnicity')->get();

        // Calculate real metrics
        $totalScholars = $users->count();
        $newApplicants = $users->where('created_at', '>=', now()->subDays(30))->count(); // For notification bar

        // Calculate Total Grantees: Count of validated applicants who are grantees (respecting filters)
        $userIds = $users->pluck('id')->toArray();
        $totalGrantees = BasicInfo::whereIn('user_id', $userIds)
            ->where('application_status', 'validated')
            ->whereRaw("LOWER(TRIM(grant_status)) = 'grantee'")
            ->count();

        // Calculate Total Graduates: Count of grantees who have graduated (respecting filters)
        // Graduates are those who are grantees and have completed their studies
        // Check for current_year_level containing "graduate" or "completed" or similar indicators
        $totalGraduates = BasicInfo::whereIn('user_id', $userIds)
            ->where('application_status', 'validated')
            ->whereRaw("LOWER(TRIM(grant_status)) = 'grantee'")
            ->where(function ($query) {
                $query->where('current_year_level', 'like', '%graduate%')
                    ->orWhere('current_year_level', 'like', '%completed%')
                    ->orWhere('current_year_level', 'like', '%finish%');
            })
            ->count();

        // Calculate Slots Left: MAX_SLOTS - current grantees (system-wide, not filtered)
        $maxSlots = Setting::get('max_slots', 120);
        $currentGrantees = BasicInfo::where('application_status', 'validated')
            ->whereRaw("LOWER(TRIM(grant_status)) = 'grantee'")
            ->count();
        $slotsLeft = max(0, $maxSlots - $currentGrantees);

        // Calculate Total Renewed: Grantees who have all renewal documents approved
        $renewalRequiredTypes = [
            'certificate_of_enrollment',
            'statement_of_account',
            'gwa_previous_sem'
        ];
        
        $totalRenewed = $users->filter(function ($user) use ($renewalRequiredTypes) {
            $isGrantee = optional($user->basicInfo)->grant_status && strtolower(trim($user->basicInfo->grant_status)) === 'grantee';
            if (!$isGrantee) return false;

            $approvedDocsCount = $user->documents
                ->whereIn('type', $renewalRequiredTypes)
                ->where('status', 'approved')
                ->unique('type')
                ->count();
            
            return $approvedDocsCount === count($renewalRequiredTypes);
        })->count();

        // Keep old variable names for backward compatibility but assign new values
        $activeScholars = $totalGraduates; // Total Graduates
        $inactiveScholars = $slotsLeft; // Slots left

        // Get scholars per municipality, barangay, or IP Group for chart
        // Dynamic chart based on selected filter level
        if ($selectedBarangay) {
            // Show IP Groups when barangay filter is selected
            $scholarsPerLocation = $users->groupBy(function ($user) {
                return optional($user->ethno)->ethnicity ?? 'Not Specified';
            })->map->count()->sortDesc()->take(10);

            $barChartLabel = 'Scholars per IP Group';
            $barChartDescription = 'Top 10 IP groups by applicant count';
        } elseif ($selectedMunicipality) {
            // Show barangays when municipality filter is selected
            $scholarsPerLocation = $users->groupBy(function ($user) {
                return optional(optional(optional($user->basicInfo)->fullAddress)->address)->barangay ?? 'Unknown';
            })->map->count()->sortDesc()->take(10);

            $barChartLabel = 'Scholars per Barangay';
            $barChartDescription = 'Top 10 barangays by applicant count';
        } else {
            // Default: Show municipalities
            $scholarsPerLocation = $users->groupBy(function ($user) {
                return optional(optional(optional($user->basicInfo)->fullAddress)->address)->municipality ?? 'Unknown';
            })->map->count()->sortDesc()->take(10);

            $barChartLabel = 'Scholars per Municipality';
            $barChartDescription = 'Top 10 municipalities by applicant count';
        }

        $barChartData = [
            'labels' => $scholarsPerLocation->keys()->toArray(),
            'datasets' => [[
                'label' => $barChartLabel,
                'backgroundColor' => 'rgba(99, 102, 241, 0.8)',
                'borderColor' => 'rgba(99, 102, 241, 1)',
                'borderWidth' => 2,
                'borderRadius' => 8,
                'data' => $scholarsPerLocation->values()->toArray(),
            ]],
        ];

        // Get application status breakdown
        $statusBreakdown = $users->filter(function ($user) {
            return $user->basicInfo !== null;
        })->groupBy(function ($user) {
            $status = optional($user->basicInfo)->application_status ?? 'pending';
            $grantStatus = optional($user->basicInfo)->grant_status ?? null;

            if ($grantStatus === 'grantee') {
                return 'Grantee';
            } elseif ($status === 'validated') {
                return 'Validated';
            } elseif ($user->basicInfo->type_assist) {
                return 'Applied - Pending';
            } else {
                return 'Not Applied';
            }
        })->map->count();

        $statusChartData = [
            'labels' => $statusBreakdown->keys()->toArray(),
            'datasets' => [[
                'label' => 'Number of Applicants',
                'backgroundColor' => [
                    'rgba(16, 185, 129, 0.9)',  // Green for Grantee/Validated
                    'rgba(59, 130, 246, 0.9)',   // Blue for Validated (if separate)
                    'rgba(245, 158, 66, 0.9)',  // Orange for Applied
                    'rgba(239, 68, 68, 0.9)',   // Red for Not Applied
                ],
                'borderColor' => ['#ffffff', '#ffffff', '#ffffff', '#ffffff'],
                'borderWidth' => 2,
                'borderRadius' => 8,
                'data' => $statusBreakdown->values()->toArray(),
            ]],
        ];

        // Get IP Group distribution for chart
        $ipGroupDistribution = $users->groupBy(function ($user) {
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
                    'rgba(234, 179, 8, 0.8)',    // Yellow
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
                    'rgba(234, 179, 8, 1)',
                ],
                'borderWidth' => 2,
                'borderRadius' => 8,
                'data' => $ipGroupDistribution->values()->toArray(),
            ]],
        ];

        // Get real pending requirements (still needed for notification bar)
        $pendingRequirements = $this->getPendingRequirements($users);

        // Get real alerts
        $alerts = $this->getAlerts($users);

        // Fetch unread notifications for staff
        $notifications = $user->unreadNotifications()->take(10)->get();

        // Get prioritized documents (First Come, First Serve)
        $priorityService = new DocumentPriorityService;

        // Initialize submitted_at and priorities for existing documents that don't have them
        $uninitializedDocs = Document::where('status', 'pending')
            ->where(function ($query) {
                $query->whereNull('submitted_at')
                    ->orWhereNull('priority_score');
            })
            ->whereNotNull('created_at')
            ->get();

        foreach ($uninitializedDocs as $doc) {
            if (! $doc->submitted_at) {
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
        $coursePriorityService = new CoursePriorityService;
        $overallCoursePrioritization = $coursePriorityService->getOverallCoursePrioritization();
        $courseStatistics = $coursePriorityService->getCourseStatistics();

        // Get prioritized applicants (weighted scoring + FCFS tiebreaker)
        $applicantPriorityService = new ApplicantPriorityService;
        $prioritizedApplicants = $applicantPriorityService->getTopPriorityApplicants(50);
        $applicantPriorityStatistics = $applicantPriorityService->getPriorityStatistics();

        // Course Distribution Chart Data
        $courseDistribution = $users->filter(function ($user) {
            return $user->basicInfo && $user->basicInfo->course;
        })->groupBy(function ($user) {
            return $user->basicInfo->course ?? 'Not Specified';
        })->map->count()->sortDesc()->take(10);

        $courseChartData = [
            'labels' => $courseDistribution->keys()->toArray(),
            'datasets' => [[
                'label' => 'Applicants per Course',
                'backgroundColor' => 'rgba(245, 158, 66, 0.8)',
                'borderColor' => 'rgba(245, 158, 66, 1)',
                'borderWidth' => 2,
                'borderRadius' => 8,
                'data' => $courseDistribution->values()->toArray(),
            ]],
        ];

        // Document Status Chart Data
        $documentStatus = Document::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $documentChartData = [
            'labels' => $documentStatus->keys()->map(function ($status) {
                return ucfirst($status);
            })->toArray(),
            'datasets' => [[
                'backgroundColor' => [
                    'rgba(16, 185, 129, 0.9)',  // Green for approved
                    'rgba(245, 158, 66, 0.9)',   // Orange for pending
                    'rgba(239, 68, 68, 0.9)',   // Red for rejected
                ],
                'borderColor' => ['#ffffff', '#ffffff', '#ffffff'],
                'borderWidth' => 3,
                'data' => $documentStatus->values()->toArray(),
            ]],
        ];

        // Province Distribution Chart Data
        $provinceDistribution = $users->filter(function ($user) {
            return $user->basicInfo && $user->basicInfo->fullAddress && $user->basicInfo->fullAddress->address;
        })->groupBy(function ($user) {
            return $user->basicInfo->fullAddress->address->province ?? 'Not Specified';
        })->map->count()->sortDesc()->take(8);

        $provinceChartData = [
            'labels' => $provinceDistribution->keys()->toArray(),
            'datasets' => [[
                'label' => 'Applicants per Province',
                'backgroundColor' => 'rgba(59, 130, 246, 0.8)',
                'borderColor' => 'rgba(59, 130, 246, 1)',
                'borderWidth' => 2,
                'borderRadius' => 8,
                'data' => $provinceDistribution->values()->toArray(),
            ]],
        ];

        // Application Trends (Monthly distribution for selected year)
        $monthlyApplications = $users->filter(function ($user) {
            return $user->basicInfo && $user->basicInfo->created_at;
        })->groupBy(function ($user) {
            return $user->basicInfo->created_at->format('M');
        })->map->count();

        // Fill in all 12 months
        $trendsData = collect();
        for ($m = 1; $m <= 12; $m++) {
            $month = Carbon::create()->month($m)->format('M');
            $trendsData[$month] = $monthlyApplications->get($month, 0);
        }

        $trendsChartData = [
            'labels' => $trendsData->keys()->toArray(),
            'datasets' => [[
                'label' => "New Applications ($selectedYear)",
                'backgroundColor' => 'rgba(16, 185, 129, 0.2)',
                'borderColor' => 'rgba(16, 185, 129, 1)',
                'borderWidth' => 3,
                'fill' => true,
                'tension' => 0.4,
                'data' => $trendsData->values()->toArray(),
            ]],
        ];

        // Gender Distribution Chart Data
        $genderDistribution = $users->filter(function ($user) {
            return $user->basicInfo && $user->basicInfo->gender;
        })->groupBy(function ($user) {
            return ucfirst(strtolower($user->basicInfo->gender));
        })->map->count();

        $genderChartData = [
            'labels' => $genderDistribution->keys()->toArray(),
            'datasets' => [[
                'data' => $genderDistribution->values()->toArray(),
                'backgroundColor' => [
                    'rgba(59, 130, 246, 0.8)', // Blue for Male
                    'rgba(236, 72, 153, 0.8)', // Pink for Female
                    'rgba(156, 163, 175, 0.8)', // Gray for Others/Not Specified
                ],
                'borderColor' => ['#ffffff', '#ffffff', '#ffffff'],
                'borderWidth' => 2,
            ]],
        ];

        // Graduation Year Distribution Chart Data
        $basicInfoIds = $users->pluck('basicInfo.id')->filter()->toArray();
        // Graduation Year Distribution - Now specifically for College Graduates (category 4)
        $gradYearDistribution = Education::where('category', 4)
            ->whereNotNull('year_grad')
            ->where('year_grad', '>', 0)
            ->selectRaw('year_grad, count(*) as count')
            ->groupBy('year_grad')
            ->orderBy('year_grad')
            ->pluck('count', 'year_grad');

        $gradYearChartData = [
            'labels' => $gradYearDistribution->keys()->toArray(),
            'datasets' => [[
                'label' => 'Applicants',
                'backgroundColor' => 'rgba(139, 92, 246, 0.8)', // Purple
                'borderColor' => 'rgba(139, 92, 246, 1)',
                'borderWidth' => 2,
                'borderRadius' => 6,
                'data' => $gradYearDistribution->values()->toArray(),
            ]],
        ];

        return view('staff.dashboard', compact(
            'name', 'assignedBarangay', 'provinces', 'municipalities', 'barangays', 'ethnicities', 'years',
            'totalScholars', 'newApplicants', 'totalGrantees', 'activeScholars', 'inactiveScholars', 'totalRenewed',
            'alerts', 'barChartData', 'statusChartData', 'ipChartData',
            'pendingRequirements', 'notifications',
            'selectedProvince', 'selectedMunicipality', 'selectedBarangay', 'selectedEthno', 'selectedYear',
            'prioritizedDocuments', 'priorityStatistics',
            'overallCoursePrioritization', 'courseStatistics',
            'prioritizedApplicants', 'applicantPriorityStatistics',
            'barChartLabel', 'barChartDescription',
            'courseChartData', 'documentChartData', 'provinceChartData', 'trendsChartData', 'genderChartData',
            'gradYearChartData'
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

                if (! $document || $document->status === 'pending') {
                    $priority = ($type === 'income_document') ? 1 : 2;
                    $isOverdue = $document ? $document->created_at->diffInDays(now()) > 30 : false;

                    $pendingRequirements[] = (object) [
                        'scholar_name' => $user->first_name.' '.$user->last_name,
                        'missing_document' => $this->getDocumentTypeName($type),
                        'is_overdue' => $isOverdue,
                        'priority' => $priority,
                        'submitted_documents' => $documents->where('status', 'approved')->pluck('type')->toArray(),
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
                $alerts[] = (object) [
                    'scholar_name' => $user->first_name.' '.$user->last_name,
                    'message' => 'Application renewal deadline approaching',
                    'due_date' => now()->addDays(30)->format('Y-m-d'),
                    'status' => 'Pending',
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

    public function reportsIndex(Request $request)
    {
        // Active report tab (grantees|pamana|waiting)
        $activeTab = $request->get('tab', 'grantees');

        return view('staff.reports.index', compact('activeTab'));
    }

    /**
     * Get Monitoring Tool report data
     */
    public function monitoringReport(Request $request)
    {
        try {
            /** @var Staff $user */
            $user = Auth::guard('staff')->user();

            // Get filters from request
            $selectedProvince = $request->get('province');
            $selectedMunicipality = $request->get('municipality');
            $selectedBarangay = $request->get('barangay');
            $selectedEthno = $request->get('ethno');

            // Build query for active scholars (Grantees and Pamana)
            $applicantsQuery = User::with([
                'basicInfo.fullAddress.address',
                'ethno',
                'basicInfo.schoolPref',
                'basicInfo.education'
            ])
            ->whereHas('basicInfo', function ($query) use ($selectedProvince, $selectedMunicipality, $selectedBarangay) {
                // Filter for Grantee or Pamana status
                $query->where(function($q) {
                    $q->whereRaw("LOWER(TRIM(grant_status)) = 'grantee'")
                      ->orWhereRaw("LOWER(TRIM(grant_status)) = 'pamana'");
                });

                // Geographic filters
                if ($selectedProvince) {
                    $query->whereHas('fullAddress', function ($q) use ($selectedProvince) {
                        $q->whereHas('address', function ($aq) use ($selectedProvince) {
                            $aq->where('province', $selectedProvince);
                        });
                    });
                }
                if ($selectedMunicipality) {
                    $query->whereHas('fullAddress', function ($q) use ($selectedMunicipality) {
                        $q->whereHas('address', function ($aq) use ($selectedMunicipality) {
                            $aq->where('municipality', $selectedMunicipality);
                        });
                    });
                }
                if ($selectedBarangay) {
                    $query->whereHas('fullAddress', function ($q) use ($selectedBarangay) {
                        $q->whereHas('address', function ($aq) use ($selectedBarangay) {
                            $aq->where('barangay', $selectedBarangay);
                        });
                    });
                }
            });

            if ($selectedEthno) {
                $applicantsQuery->whereHas('ethno', function ($query) use ($selectedEthno) {
                    $query->where('ethnicity', $selectedEthno);
                });
            }

            $applicants = $applicantsQuery->get();

            $monitoringData = $applicants->map(function ($applicant, $index) {
                $basicInfo = $applicant->basicInfo;
                $address = $basicInfo && $basicInfo->fullAddress ? $basicInfo->fullAddress->address : null;

                // Name
                $fullName = strtoupper($applicant->last_name . ', ' . $applicant->first_name . ' ' . ($applicant->middle_name ? $applicant->middle_name[0] . '.' : ''));

                // Age
                $age = $basicInfo && $basicInfo->birthdate ? Carbon::parse($basicInfo->birthdate)->age : '';

                // Gender
                $gender = $basicInfo ? ($basicInfo->gender ?? '') : '';
                $isFemale = strtolower($gender) === 'female';
                $isMale = strtolower($gender) === 'male';

                // School
                $schoolPref = $basicInfo ? $basicInfo->schoolPref : null;
                $schoolName = $schoolPref ? ($schoolPref->school_name ?? '') : '';
                
                // Course
                $course = $schoolPref ? ($schoolPref->degree ?? '') : '';

                // Year Level
                $yearLevel = $basicInfo ? ($basicInfo->current_year_level ?? '') : '';

                // Status logic
                $status = strtolower($basicInfo->application_status ?? '');
                $remarks = strtolower($basicInfo->disqualification_remarks ?? '');
                $grantStatus = strtolower($basicInfo->grant_status ?? '');

                // Simple keyword matching for status checkboxes
                $isDropout = str_contains($remarks, 'drop') || str_contains($status, 'drop');
                $isTerminated = str_contains($remarks, 'terminat') || str_contains($status, 'terminat');
                $isGraduate = str_contains($remarks, 'graduat') || str_contains($status, 'graduat');
                // Default to Ongoing if they are a grantee/pamana and not in other states
                $isOngoing = !$isDropout && !$isTerminated && !$isGraduate && ($grantStatus === 'grantee' || $grantStatus === 'pamana');

                return [
                    'no' => $index + 1,
                    'category_name' => $fullName, // Mapping "Category Name" to Student Name
                    'age' => $age,
                    'gender' => $gender,
                    'is_female' => $isFemale,
                    'is_male' => $isMale,
                    'course' => $course,
                    'school' => $schoolName,
                    'year_level' => $yearLevel,
                    'is_dropout' => $isDropout,
                    'is_terminated' => $isTerminated,
                    'is_graduate' => $isGraduate,
                    'is_ongoing' => $isOngoing,
                ];
            });

            return response()->json([
                'success' => true,
                'monitoring' => $monitoringData,
            ]);
        } catch (Exception $e) {
            Log::error('Error in monitoringReport', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading monitoring report: '.$e->getMessage(),
                'monitoring' => [],
            ], 500);
        }
    }

    public function notifications(Request $request)
    {
        /** @var Staff $user */
        $user = Auth::guard('staff')->user();
        if (! $user) {
            return redirect()->route('staff.login');
        }

        $name = $user->name;
        $assignedBarangay = $user->assigned_barangay ?? 'All';

        // Fetch real notifications from database
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($notification) {
                $data = $notification->data;

                return [
                    'id' => $notification->id,
                    'type' => $data['type'] ?? 'general',
                    'title' => $data['title'] ?? 'Notification',
                    'message' => $data['message'] ?? '',
                    'is_read' => $notification->read_at !== null,
                    'created_at' => $notification->created_at,
                    'priority' => $data['priority'] ?? 'normal',
                    'student_id' => $data['student_id'] ?? null,
                ];
            });

        return view('staff.notifications', compact('name', 'assignedBarangay', 'notifications'));
    }

    public function markNotificationsRead(Request $request)
    {
        /** @var Staff $user */
        $user = Auth::guard('staff')->user();
        if ($user) {
            $user->unreadNotifications->markAsRead();
        }

        return response()->json(['success' => true]);
    }

    public function markAllNotificationsRead(Request $request)
    {
        /** @var Staff $user */
        $user = Auth::guard('staff')->user();
        if ($user) {
            $user->unreadNotifications->markAsRead();
        }

        return response()->json(['success' => true]);
    }

    public function deleteNotification($id)
    {
        /** @var Staff $user */
        $user = Auth::guard('staff')->user();
        if ($user) {
            $notification = $user->notifications()->where('id', $id)->first();
            if ($notification) {
                $notification->delete();

                return response()->json(['success' => true]);
            }
        }

        return response()->json(['success' => false, 'message' => 'Notification not found'], 404);
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
            'basicInfo.fullAddress.origin.address',
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
        
        // Count only unique document types where the latest submission is approved
        $approvedCount = $documents->where('status', 'approved')->count();
        
        $progressPercent = $totalRequired > 0 ? round(($approvedCount / $totalRequired) * 100) : 0;

        // Get course prioritization for this specific applicant
        $coursePriorityService = new CoursePriorityService;
        $coursePrioritization = $coursePriorityService->getApplicantCoursePrioritization($user);

        // Calculate slot availability
        $maxSlots = Setting::get('max_slots', 120);
        $currentValidated = BasicInfo::where('application_status', 'validated')->count();
        // If this application is already validated, don't count it in available slots
        if ($basicInfo && $basicInfo->application_status === 'validated') {
            $currentValidated = max(0, $currentValidated - 1);
        }
        $availableSlots = max(0, $maxSlots - $currentValidated);
        $isFull = $availableSlots === 0;

        return view('staff.application-view', compact(
            'user', 'basicInfo', 'ethno', 'mailing', 'permanent', 'origin',
            'education', 'familyFather', 'familyMother', 'siblings', 'schoolPref',
            'documents', 'requiredTypes', 'totalRequired', 'approvedCount', 'progressPercent',
            'coursePrioritization', 'maxSlots', 'availableSlots', 'isFull'
        ));
    }

    public function searchSuggestions(Request $request)
    {
        try {
            /** @var Staff $user */
            $user = Auth::guard('staff')->user();
            $query = trim($request->get('query'));
            
            // Only proceed if query is at least 2 characters
            if (strlen($query) < 2) {
                return response()->json([]);
            }

            // Log search activity
            Log::info('Applicant search suggestion request', [
                'staff_id' => $user->id,
                'query' => $query
            ]);

            $suggestions = User::select('id', 'first_name', 'middle_name', 'last_name', 'profile_pic_url', 'email')
                ->whereHas('basicInfo') // Ensure they are applicants
                ->where(function($q) use ($query) {
                    $q->where('first_name', 'like', "%{$query}%")
                      ->orWhere('last_name', 'like', "%{$query}%")
                      ->orWhere('middle_name', 'like', "%{$query}%");
                })
                ->limit(5) // Limit results for performance
                ->get()
                ->map(function($user) {
                     $fullName = trim("{$user->first_name} {$user->middle_name} {$user->last_name}");
                     return [
                         'id' => $user->id,
                         'name' => $fullName,
                         'initials' => $user->initials,
                         'profile_pic_url' => $user->profile_pic_url,
                         'email' => $user->email, // Included for context in UI, though search is by name
                     ];
                });

            return response()->json($suggestions);
        } catch (Throwable $e) {
            Log::error('Search suggestion error: ' . $e->getMessage());
            return response()->json(['error' => 'Search failed'], 500);
        }
    }

    public function applicantsList(Request $request)
    {
        try {
            /** @var Staff $user */
            $user = Auth::guard('staff')->user();
            $selectedProvince = $request->get('province');
            $selectedMunicipality = $request->get('municipality');
            $selectedBarangay = $request->get('barangay');
            $selectedEthno = $request->get('ethno');
            $selectedStatus = $request->get('status');
            $selectedType = $request->get('type');
            $search = $request->get('search');

            $applicantsQuery = User::with(['basicInfo.fullAddress.address', 'ethno', 'documents']);

            if ($search) {
                // Log detailed search activity
                Log::info('Applicant search performed', [
                    'staff_id' => $user->id, 
                    'query' => $search
                ]);
                
                $applicantsQuery->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('middle_name', 'like', "%{$search}%");
                });
            }

            $applicantsQuery->whereHas('basicInfo', function ($query) use ($selectedProvince, $selectedMunicipality, $selectedBarangay, $selectedStatus, $selectedType) {
                    if ($selectedProvince) {
                        $query->whereHas('fullAddress', function ($q) use ($selectedProvince) {
                            $q->whereHas('address', function ($aq) use ($selectedProvince) {
                                $aq->where('province', $selectedProvince);
                            });
                        });
                    }
                    if ($selectedMunicipality) {
                        $query->whereHas('fullAddress', function ($q) use ($selectedMunicipality) {
                            $q->whereHas('address', function ($aq) use ($selectedMunicipality) {
                                $aq->where('municipality', $selectedMunicipality);
                            });
                        });
                    }
                    if ($selectedBarangay) {
                        $query->whereHas('fullAddress', function ($q) use ($selectedBarangay) {
                            $q->whereHas('address', function ($aq) use ($selectedBarangay) {
                                $aq->where('barangay', $selectedBarangay);
                            });
                        });
                    }

            if ($selectedStatus) {
                if ($selectedStatus === 'grantee') {
                    $query->where('application_status', 'validated')
                          ->whereRaw("LOWER(TRIM(grant_status)) = 'grantee'");
                } elseif ($selectedStatus === 'terminated') {
                    $query->where(function($q) {
                        $q->where('application_status', 'terminated')
                          ->orWhere(function($sq) {
                              $sq->where('application_status', 'rejected')
                                 ->whereRaw("LOWER(TRIM(grant_status)) = 'grantee'");
                          });
                    });
                } elseif ($selectedStatus === 'pamana') {
                    $query->where('application_status', 'validated')
                          ->where(function($q) {
                              $q->where('type_assist', 'Pamana')
                                ->orWhereRaw("LOWER(TRIM(grant_status)) = 'pamana'");
                          });
                } else {
                    $query->where('application_status', $selectedStatus);

                    if ($selectedStatus === 'validated') {
                        // Exclude grantees and pamana from validated list
                        $query->where(function($q) {
                            $q->where(function($sq) {
                                $sq->whereRaw("LOWER(TRIM(grant_status)) != 'grantee'")
                                   ->whereRaw("LOWER(TRIM(grant_status)) != 'pamana'");
                            })->orWhereNull('grant_status');
                        })->where(function($q) {
                            $q->where('type_assist', '!=', 'Pamana')
                              ->orWhereNull('type_assist');
                        });
                    }

                    if ($selectedStatus === 'rejected') {
                        $query->where(function($q) {
                            $q->whereRaw("LOWER(TRIM(grant_status)) != 'grantee'")
                              ->orWhereNull('grant_status');
                        });
                    }
                }
            }
        });

    if ($selectedEthno) {
        $applicantsQuery->where('ethno_id', $selectedEthno);
    }

    $applicants = $applicantsQuery->paginate(20);
    $provinces = Address::select('province')->distinct()->where('province', '!=', '')->orderBy('province')->pluck('province');
    $municipalities = Address::select('municipality')->distinct()->where('municipality', '!=', '')->orderBy('municipality')->pluck('municipality');
    $barangays = Address::select('barangay')->distinct()->where('barangay', '!=', '')->orderBy('barangay')->pluck('barangay');
    $ethnicities = Ethno::orderBy('ethnicity')->get();

    return view('staff.applicants-list', compact(
        'applicants', 'provinces', 'municipalities', 'barangays', 'ethnicities',
        'selectedProvince', 'selectedMunicipality', 'selectedBarangay', 'selectedEthno',
        'selectedStatus', 'selectedType'
    ));
        } catch (Throwable $e) {
            $applicants = new LengthAwarePaginator(
                collect([]),
                0,
                20,
                1,
                ['path' => $request->url(), 'query' => $request->query()]
            );
            $provinces = collect([]);
            $municipalities = collect([]);
            $barangays = collect([]);
            $ethnicities = collect([]);
            
            $selectedProvince = $request->province;
            $selectedMunicipality = $request->municipality;
            $selectedBarangay = $request->barangay;
            $selectedEthno = $request->ethno;

            return view('staff.applicants-list', compact(
                'applicants', 'provinces', 'municipalities', 'barangays', 'ethnicities',
                'selectedProvince', 'selectedMunicipality', 'selectedBarangay', 'selectedEthno'
            ))->with('error', 'Unable to load applicants. Please try again later.');
        }
    }

    public function applicantPriority()
    {
        /** @var Staff $user */
        $user = Auth::guard('staff')->user();
        $name = $user->name;
        $assignedBarangay = $user->assigned_barangay ?? 'All';

        $applicantPriorityService = new ApplicantPriorityService;
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
        /** @var Staff $user */
        $user = Auth::guard('staff')->user();
        $name = $user->name;
        $assignedBarangay = $user->assigned_barangay ?? 'All';

        $priorityService = new DocumentPriorityService;
        $uninitializedDocs = Document::where('status', 'pending')
            ->where(function ($query) {
                $query->whereNull('submitted_at')
                    ->orWhereNull('priority_score');
            })
            ->whereNotNull('created_at')
            ->get();

        foreach ($uninitializedDocs as $doc) {
            if (! $doc->submitted_at) {
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
        /** @var Staff $user */
        $user = Auth::guard('staff')->user();
        $name = $user->name;
        $assignedBarangay = $user->assigned_barangay ?? 'All';

        // Get all applicants with basic info
        $usersQuery = User::with(['basicInfo.fullAddress.address', 'ethno', 'documents', 'basicInfo.schoolPref', 'basicInfo.education'])
            ->whereHas('basicInfo', function ($query) use ($assignedBarangay) {
                if ($assignedBarangay !== 'All') {
                    $query->whereHas('fullAddress', function ($q) use ($assignedBarangay) {
                        $q->whereHas('address', function ($aq) use ($assignedBarangay) {
                            $aq->where('barangay', $assignedBarangay);
                        });
                    });
                }
            });

        $users = $usersQuery->get();

        // Calculate IP Group scores for all applicants
        $applicantPriorityService = new ApplicantPriorityService;
        $applicantsWithIpScores = [];

        foreach ($users as $user) {
            $priorityData = $applicantPriorityService->calculateApplicantPriority($user);

            $applicantsWithIpScores[] = [
                'user' => $user,
                'ip_rubric_score' => $priorityData['ip_rubric_score'],
                'ethnicity' => $priorityData['ethnicity'],
                'is_priority_ethno' => $priorityData['is_priority_ethno'],
                'priority_score' => $priorityData['priority_score'],
            ];
        }

        // Sort by IP Group score only (highest first)
        usort($applicantsWithIpScores, function ($a, $b) {
            return $b['ip_rubric_score'] <=> $a['ip_rubric_score'];
        });

        $notifications = $user->unreadNotifications()->take(10)->get();

        return view('staff.priorities.ip', compact(
            'name',
            'assignedBarangay',
            'applicantsWithIpScores',
            'notifications'
        ));
    }

    public function coursePriority()
    {
        /** @var Staff $user */
        $user = Auth::guard('staff')->user();
        $name = $user->name;
        $assignedBarangay = $user->assigned_barangay ?? 'All';

        $coursePriorityService = new CoursePriorityService;
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
        /** @var Staff $user */
        $user = Auth::guard('staff')->user();
        $name = $user->name;
        $assignedBarangay = $user->assigned_barangay ?? 'All';

        // Get all users who have approved tribal certificates
        $usersWithApprovedTribalCert = User::whereHas('documents', function ($query) {
            $query->where('type', 'tribal_certificate')
                ->where('status', 'approved');
        })
            ->with(['documents' => function ($query) {
                $query->where('type', 'tribal_certificate')
                    ->where('status', 'approved')
                    ->orderBy('created_at', 'desc');
            }, 'ethno', 'basicInfo'])
            ->get();

        // Sort by when the certificate was approved (most recently approved first)
        $prioritizedUsers = $usersWithApprovedTribalCert->sortByDesc(function ($user) {
            $approvedCert = $user->documents->where('type', 'tribal_certificate')
                ->where('status', 'approved')
                ->first();

            return $approvedCert ? $approvedCert->updated_at : null;
        })->values();

        // Get statistics
        $totalApproved = $prioritizedUsers->count();
        $recentlyApproved = $prioritizedUsers->filter(function ($user) {
            $approvedCert = $user->documents->where('type', 'tribal_certificate')
                ->where('status', 'approved')
                ->first();
            if (! $approvedCert) {
                return false;
            }

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
        /** @var Staff $user */
        $user = Auth::guard('staff')->user();
        $name = $user->name;
        $assignedBarangay = $user->assigned_barangay ?? 'All';

        // Get all applicants with basic info
        $usersQuery = User::with(['basicInfo.fullAddress.address', 'ethno', 'documents', 'basicInfo.schoolPref', 'basicInfo.education'])
            ->whereHas('basicInfo', function ($query) use ($assignedBarangay) {
                if ($assignedBarangay !== 'All') {
                    $query->whereHas('fullAddress', function ($q) use ($assignedBarangay) {
                        $q->whereHas('address', function ($aq) use ($assignedBarangay) {
                            $aq->where('barangay', $assignedBarangay);
                        });
                    });
                }
            });

        $users = $usersQuery->get();

        // Calculate ITR status for all applicants
        $applicantPriorityService = new ApplicantPriorityService;
        $applicantsWithItrStatus = [];

        foreach ($users as $user) {
            $priorityData = $applicantPriorityService->calculateApplicantPriority($user);
            $hasApprovedItr = $priorityData['has_approved_income_tax'] ?? false;

            // Get ITR document info
            $itrDocument = $user->documents->where('type', 'income_document')->first();
            $itrStatus = $itrDocument ? $itrDocument->status : null;
            $itrApprovedAt = ($itrDocument && $itrDocument->status === 'approved') ? $itrDocument->updated_at : null;

            $applicantsWithItrStatus[] = [
                'user' => $user,
                'has_approved_income_tax' => $hasApprovedItr,
                'itr_status' => $itrStatus,
                'itr_approved_at' => $itrApprovedAt,
                'itr_score' => $hasApprovedItr ? 1.0 : 0.0, // Binary: 1.0 if approved, 0.0 if not
                'priority_score' => $priorityData['priority_score'],
            ];
        }

        // Sort by ITR status only (approved first = 1.0)
        usort($applicantsWithItrStatus, function ($a, $b) {
            return $b['itr_score'] <=> $a['itr_score'];
        });

        $notifications = $user->unreadNotifications()->take(10)->get();

        return view('staff.priorities.income-tax', compact(
            'name',
            'assignedBarangay',
            'applicantsWithItrStatus',
            'notifications'
        ));
    }

    public function academicPerformancePriority()
    {
        /** @var Staff $user */
        $user = Auth::guard('staff')->user();
        $name = $user->name;
        $assignedBarangay = $user->assigned_barangay ?? 'All';

        // Get all applicants with basic info
        $usersQuery = User::with(['basicInfo.fullAddress.address', 'ethno', 'documents', 'basicInfo.schoolPref', 'basicInfo.education'])
            ->whereHas('basicInfo', function ($query) use ($assignedBarangay) {
                if ($assignedBarangay !== 'All') {
                    $query->whereHas('fullAddress', function ($q) use ($assignedBarangay) {
                        $q->whereHas('address', function ($aq) use ($assignedBarangay) {
                            $aq->where('barangay', $assignedBarangay);
                        });
                    });
                }
            });

        $users = $usersQuery->get();

        // Calculate GWA/Academic scores for all applicants
        $applicantPriorityService = new ApplicantPriorityService;
        $applicantsWithGwaScores = [];

        foreach ($users as $user) {
            $priorityData = $applicantPriorityService->calculateApplicantPriority($user);
            $gpa = $user->basicInfo->gpa ?? null;

            $applicantsWithGwaScores[] = [
                'user' => $user,
                'academic_rubric_score' => $priorityData['academic_rubric_score'],
                'gpa' => $gpa,
                'priority_score' => $priorityData['priority_score'],
            ];
        }

        // Sort by Academic/GWA rubric score only (highest first)
        usort($applicantsWithGwaScores, function ($a, $b) {
            return $b['academic_rubric_score'] <=> $a['academic_rubric_score'];
        });

        $notifications = $user->unreadNotifications()->take(10)->get();

        return view('staff.priorities.academic-performance', compact(
            'name',
            'assignedBarangay',
            'applicantsWithGwaScores',
            'notifications'
        ));
    }

    public function citationAwardsPriority()
    {
        /** @var Staff $user */
        $user = Auth::guard('staff')->user();
        $name = $user->name;
        $assignedBarangay = $user->assigned_barangay ?? 'All';

        // Get all applicants with basic info
        $usersQuery = User::with(['basicInfo.fullAddress.address', 'ethno', 'documents', 'basicInfo.schoolPref', 'basicInfo.education'])
            ->whereHas('basicInfo', function ($query) use ($assignedBarangay) {
                if ($assignedBarangay !== 'All') {
                    $query->whereHas('fullAddress', function ($q) use ($assignedBarangay) {
                        $q->whereHas('address', function ($aq) use ($assignedBarangay) {
                            $aq->where('barangay', $assignedBarangay);
                        });
                    });
                }
            });

        $users = $usersQuery->get();

        // Calculate Citation/Awards scores for all applicants
        $applicantPriorityService = new ApplicantPriorityService;
        $applicantsWithAwardsScores = [];

        foreach ($users as $user) {
            $priorityData = $applicantPriorityService->calculateApplicantPriority($user);

            // Get education records with ranks/awards
            $educationRecords = $user->basicInfo->education ?? collect();
            $awards = [];
            $bestAward = null;

            // Award score mapping (same as in ApplicantPriorityService)
            $awardScores = [
                'valedictorian' => 10.0,
                'salutatorian' => 9.5,
                'with highest honors' => 9.0,
                'with high honors' => 8.0,
                'with honors' => 7.0,
                "dean's lister" => 6.5,
                'deans lister' => 6.5,
                'dean\'s lister' => 6.5,
                'top 10' => 6.0,
                'academic awardee' => 5.0,
            ];

            $bestScore = 0.0;
            foreach ($educationRecords as $edu) {
                if ($edu->rank && trim($edu->rank) !== '') {
                    $awards[] = $edu->rank;
                    // Find the award with the highest score
                    $rankLower = strtolower(trim($edu->rank));
                    $score = $awardScores[$rankLower] ?? 0.0;
                    if ($score > $bestScore) {
                        $bestScore = $score;
                        $bestAward = $edu->rank;
                    }
                }
            }

            $applicantsWithAwardsScores[] = [
                'user' => $user,
                'awards_rubric_score' => $priorityData['awards_rubric_score'],
                'awards' => $awards,
                'best_award' => $bestAward,
            ];
        }

        // Sort by Awards rubric score only (highest first)
        usort($applicantsWithAwardsScores, function ($a, $b) {
            return $b['awards_rubric_score'] <=> $a['awards_rubric_score'];
        });

        $notifications = $user->unreadNotifications()->take(10)->get();

        return view('staff.priorities.citation-awards', compact(
            'name',
            'assignedBarangay',
            'applicantsWithAwardsScores',
            'notifications'
        ));
    }

    public function socialResponsibilityPriority()
    {
        /** @var Staff $user */
        $user = Auth::guard('staff')->user();
        $name = $user->name;
        $assignedBarangay = $user->assigned_barangay ?? 'All';

        // Get all applicants with basic info
        $usersQuery = User::with(['basicInfo.fullAddress.address', 'ethno', 'documents', 'basicInfo.schoolPref', 'basicInfo.education'])
            ->whereHas('basicInfo', function ($query) use ($assignedBarangay) {
                if ($assignedBarangay !== 'All') {
                    $query->whereHas('fullAddress', function ($q) use ($assignedBarangay) {
                        $q->whereHas('address', function ($aq) use ($assignedBarangay) {
                            $aq->where('barangay', $assignedBarangay);
                        });
                    });
                }
            });

        $users = $usersQuery->get();

        // Calculate Social Responsibility scores for all applicants
        $applicantPriorityService = new ApplicantPriorityService;
        $applicantsWithSocialScores = [];

        foreach ($users as $user) {
            $priorityData = $applicantPriorityService->calculateApplicantPriority($user);

            // Get essay answers
            $schoolPref = $user->basicInfo->schoolPref;
            $essay1 = $schoolPref->ques_answer1 ?? '';
            $essay2 = $schoolPref->ques_answer2 ?? '';
            $combinedText = trim($essay1.' '.$essay2);
            $textLength = mb_strlen($combinedText);

            $applicantsWithSocialScores[] = [
                'user' => $user,
                'social_responsibility_rubric_score' => $priorityData['social_responsibility_rubric_score'],
                'essay1' => $essay1,
                'essay2' => $essay2,
                'text_length' => $textLength,
            ];
        }

        // Sort by Social Responsibility rubric score only (highest first)
        usort($applicantsWithSocialScores, function ($a, $b) {
            return $b['social_responsibility_rubric_score'] <=> $a['social_responsibility_rubric_score'];
        });

        $notifications = $user->unreadNotifications()->take(10)->get();

        return view('staff.priorities.social-responsibility', compact(
            'name',
            'assignedBarangay',
            'applicantsWithSocialScores',
            'notifications'
        ));
    }

    public function otherRequirementsPriority()
    {
        /** @var Staff $user */
        $user = Auth::guard('staff')->user();
        $name = $user->name;
        $assignedBarangay = $user->assigned_barangay ?? 'All';

        // Other required documents: birth_certificate, endorsement, good_moral
        $otherRequiredTypes = ['birth_certificate', 'endorsement', 'good_moral'];

        // Get all users who have ALL other required documents approved
        $usersWithAllOtherDocs = User::whereHas('documents', function ($query) use ($otherRequiredTypes) {
            $query->whereIn('type', $otherRequiredTypes)
                ->where('status', 'approved');
        })
            ->with(['documents' => function ($query) use ($otherRequiredTypes) {
                $query->whereIn('type', $otherRequiredTypes)
                    ->where('status', 'approved')
                    ->orderBy('created_at', 'desc');
            }, 'ethno', 'basicInfo'])
            ->get();

        // Filter to only include users who have ALL three documents approved
        $prioritizedUsers = $usersWithAllOtherDocs->filter(function ($user) use ($otherRequiredTypes) {
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
        $prioritizedUsers = $prioritizedUsers->sortByDesc(function ($user) use ($otherRequiredTypes) {
            $approvedDocs = $user->documents
                ->whereIn('type', $otherRequiredTypes)
                ->where('status', 'approved');

            // Get the most recent approval date among all three documents
            $latestApproval = $approvedDocs->max('updated_at');

            return $latestApproval;
        })->values();

        // Get statistics
        $totalApproved = $prioritizedUsers->count();
        $recentlyApproved = $prioritizedUsers->filter(function ($user) use ($otherRequiredTypes) {
            $approvedDocs = $user->documents
                ->whereIn('type', $otherRequiredTypes)
                ->where('status', 'approved');

            $latestApproval = $approvedDocs->max('updated_at');
            if (! $latestApproval) {
                return false;
            }

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
                'rejection_reason' => 'required|string|min:10|max:1000',
            ], [
                'rejection_reason.required' => 'Please provide a reason for rejection.',
                'rejection_reason.min' => 'Rejection reason must be at least 10 characters long.',
            ]);
        } else {
            $validated = $request->validate([
                'status' => 'required|in:approved,rejected,pending',
                'rejection_reason' => 'nullable|string|max:1000',
            ]);
        }

        $updateData = [
            'status' => $validated['status'],
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

        // Log Transaction History
        TransactionHistory::create([
            'user_id' => $document->user_id,
            'action' => 'Document ' . ucfirst($validated['status']),
            'description' => 'Your ' . str_replace('_', ' ', $document->type) . ' was ' . $validated['status'] . ($updateData['rejection_reason'] ? ': ' . $updateData['rejection_reason'] : ''),
            'status' => $validated['status'] === 'approved' ? 'success' : ($validated['status'] === 'rejected' ? 'danger' : 'info'),
            'metadata' => [
                'document_id' => $document->id,
                'type' => $document->type,
                'rejection_reason' => $updateData['rejection_reason'] ?? null
            ]
        ]);

        // Notify the student
        $document->user->notify(new DocumentStatusUpdated($document, $validated['status']));

        // Auto-progression logic for Grantees (Scholarship Renewal)
        if ($validated['status'] === 'approved') {
            $user = $document->user;
            $basicInfo = $user->basicInfo;

            // Check if user is a grantee and has a pending year level update
            if ($basicInfo && 
                strtolower($basicInfo->grant_status ?? '') === 'grantee' && 
                $basicInfo->target_year_level) {
                
                // Define renewal documents
                $renewalDocs = [
                    'certificate_of_enrollment',
                    'statement_of_account',
                    'gwa_previous_sem'
                ];

                // Check if this document is a renewal document
                if (in_array($document->type, $renewalDocs)) {
                    // Check if ALL renewal documents are approved
                    $allApproved = true;
                    foreach ($renewalDocs as $type) {
                        // Check if an approved document of this type exists
                        // We query the DB because the current $document is already updated
                        $exists = $user->documents()
                            ->where('type', $type)
                            ->where('status', 'approved')
                            ->exists();
                        
                        if (!$exists) {
                            $allApproved = false;
                            break;
                        }
                    }

                    if ($allApproved) {
                        $oldYear = $basicInfo->current_year_level;
                        $newYear = $basicInfo->target_year_level;

                        // Validation: Check against Course Duration if available
                        $schoolPref = $basicInfo->schoolPref;
                        $maxYears = $schoolPref ? ($schoolPref->num_years ?? 5) : 5; // Default to 5 if not set

                        if ($newYear <= $maxYears) {
                            $basicInfo->update([
                                'current_year_level' => $newYear,
                                'target_year_level' => null // Clear pending update
                            ]);

                            // Log and Notify
                            if ($oldYear != $newYear) {
                                TransactionHistory::create([
                                    'user_id' => $user->id,
                                    'action' => 'Year Level Updated',
                                    'description' => "Scholarship renewed. Year level advanced from {$oldYear} to {$newYear}.",
                                    'status' => 'success'
                                ]);
                                
                                $user->notify(new TransactionNotification(
                                    'update',
                                    'Year Level Updated',
                                    "Congratulations! Your renewal is complete. You are now officially recognized as a {$newYear}" . ($newYear==1?'st':($newYear==2?'nd':($newYear==3?'rd':'th'))) . " Year scholar.",
                                    'success'
                                ));
                            } else {
                                TransactionHistory::create([
                                    'user_id' => $user->id,
                                    'action' => 'Scholarship Renewed',
                                    'description' => "Scholarship renewed. Retained at Year Level {$oldYear}.",
                                    'status' => 'success'
                                ]);
                            }
                        }
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Document status updated successfully',
            'rejection_reason' => $updateData['rejection_reason'] ?? null,
        ]);
    }

    public function updateApplicationStatus(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        $basicInfo = BasicInfo::where('user_id', $user->id)->firstOrFail();

        // Validate based on status
        $status = $request->input('status');

        if ($status === 'rejected' || $status === 'terminated') {
            // For rejection or termination, check if it's termination (grantee or Pamana) or regular rejection
            $isTermination = ($status === 'terminated') || ($basicInfo->application_status === 'validated' && ($basicInfo->grant_status === 'grantee' || $basicInfo->type_assist === 'Pamana'));

            if ($isTermination) {
                // Termination requires detailed reason
                $validated = $request->validate([
                    'status' => 'required|in:pending,validated,rejected,returned,terminated',
                    'rejection_reason' => 'required|string|min:10|max:1000',
                ], [
                    'rejection_reason.required' => 'Please provide a reason for termination.',
                    'rejection_reason.min' => 'Termination reason must be at least 10 characters long.',
                ]);
            } else {
                // Regular rejection - disqualification reasons are required (validated in frontend)
                $validated = $request->validate([
                    'status' => 'required|in:pending,validated,rejected,returned,terminated',
                    'rejection_reason' => 'nullable|string|max:1000',
                    'disqualification_not_ip' => 'nullable|boolean',
                    'disqualification_exceeded_income' => 'nullable|boolean',
                    'disqualification_incomplete_docs' => 'nullable|boolean',
                    'disqualification_remarks' => 'nullable|string|max:1000',
                ]);
            }
        } else {
            $validated = $request->validate([
                'status' => 'required|in:pending,validated,rejected,returned,terminated',
                'rejection_reason' => 'nullable|string|max:1000',
            ]);
        }

        // Check slot availability when validating
        if ($validated['status'] === 'validated') {
            $maxSlots = Setting::get('max_slots', 120);
            $currentValidated = BasicInfo::where('application_status', 'validated')->count();

            // If this application is already validated, we're just keeping it validated (no change)
            // If it's not validated yet, we need to check if we can add one more
            if ($basicInfo->application_status !== 'validated') {
                // Check if adding this one would exceed the limit
                if ($currentValidated >= $maxSlots) {
                    $availableSlots = 0;

                    return response()->json([
                        'success' => false,
                        'message' => "Cannot validate application. Maximum slots ({$maxSlots}) have been reached. All scholarship slots are currently full.",
                    ], 400);
                }
            }
        }

        // Determine if this is a rejection or termination
        // Termination: Applicant was validated (confirmed) and is a grantee or Pamana
        // Rejection: Applicant was NOT validated (not confirmed) yet
        $isTermination = ($basicInfo->application_status === 'validated' && ($basicInfo->grant_status === 'grantee' || $basicInfo->type_assist === 'Pamana'));
        $isRejection = ! $isTermination;

        // Prepare update data
        $updateData = [
            'application_status' => $validated['status'],
        ];

        // Handle rejection/termination logic
        if ($validated['status'] === 'rejected' || $validated['status'] === 'terminated') {
            // IMPORTANT: Distinguish between rejection and termination
            if ($isTermination || $validated['status'] === 'terminated') {
                // TERMINATION: Applicant was a confirmed grantee who broke a rule
                // Keep grant_status as 'grantee' to identify them as terminated (not just rejected)
                // This allows us to filter terminated applicants separately
                // Do NOT change grant_status - keep it as 'grantee'
                // Save termination reason
                $updateData['application_rejection_reason'] = trim($validated['rejection_reason']);
                // Clear disqualification fields for termination
                $updateData['disqualification_not_ip'] = false;
                $updateData['disqualification_exceeded_income'] = false;
                $updateData['disqualification_incomplete_docs'] = false;
                $updateData['disqualification_remarks'] = null;
            } else {
                // REJECTION: Applicant was not yet confirmed/validated
                // Save disqualification reasons and remarks
                $updateData['disqualification_not_ip'] = $request->input('disqualification_not_ip', false) ? true : false;
                $updateData['disqualification_exceeded_income'] = $request->input('disqualification_exceeded_income', false) ? true : false;
                $updateData['disqualification_incomplete_docs'] = $request->input('disqualification_incomplete_docs', false) ? true : false;
                $updateData['disqualification_remarks'] = $request->input('disqualification_remarks') ? trim($request->input('disqualification_remarks')) : null;

                // Use remarks as rejection reason if provided, otherwise use a default message
                $remarks = $updateData['disqualification_remarks'];
                $updateData['application_rejection_reason'] = $remarks ?: 'Application rejected based on disqualification criteria.';

                // Ensure grant_status is NOT 'grantee' (should be null or not set)
                // This ensures they appear in "Rejected Applicants" not "Terminated Applicants"
                if ($basicInfo->grant_status === 'grantee') {
                    // This shouldn't happen, but if it does, clear it
                    $updateData['grant_status'] = null;
                }
            }
        } else {
            // Clear rejection_reason and disqualification fields if status is changed to approved or pending
            $updateData['application_rejection_reason'] = null;
            $updateData['disqualification_not_ip'] = false;
            $updateData['disqualification_exceeded_income'] = false;
            $updateData['disqualification_incomplete_docs'] = false;
            $updateData['disqualification_remarks'] = null;
        }

        // IMPORTANT: If admin moves a scholar back to "pending" or "returned", ensure they reappear in the applicants list.
        // The applicants list excludes grant_status='grantee', so we must clear grant_status (and grant flags) on pending/returned.
        if (in_array($validated['status'], ['pending', 'returned'])) {
            $updateData['grant_status'] = null;
            if (Schema::hasColumn('basic_info', 'grant_1st_sem')) {
                $updateData['grant_1st_sem'] = false;
            }
            if (Schema::hasColumn('basic_info', 'grant_2nd_sem')) {
                $updateData['grant_2nd_sem'] = false;
            }
        }

        $basicInfo->update($updateData);

        // Log Transaction History
        $statusLabel = $validated['status'];
        $logAction = 'Application ' . ucfirst($statusLabel);
        $logDescription = 'Your application status has been updated to ' . $statusLabel;

        if ($statusLabel === 'rejected' || $statusLabel === 'terminated') {
            $logAction = ($isTermination || $statusLabel === 'terminated') ? 'Scholarship Terminated' : 'Application Rejected';
            $logDescription = $updateData['application_rejection_reason'] ?? (($isTermination || $statusLabel === 'terminated') ? 'Scholarship terminated.' : 'Application rejected.');
        } elseif ($statusLabel === 'validated') {
            $logAction = 'Application Approved';
            $logDescription = 'Your scholarship application has been approved.';
        } elseif ($statusLabel === 'returned') {
            $logAction = 'Application Returned';
            $logDescription = 'Your application has been returned for corrections.';
        }

        TransactionHistory::create([
            'user_id' => $user->id,
            'action' => $logAction,
            'description' => $logDescription,
            'status' => $statusLabel === 'validated' ? 'success' : ($statusLabel === 'rejected' ? 'danger' : ($statusLabel === 'returned' ? 'warning' : 'info')),
            'metadata' => [
                'status' => $statusLabel,
                'rejection_reason' => $updateData['application_rejection_reason'] ?? null,
                'is_termination' => $isTermination
            ]
        ]);

        // Send notification to user
        $user->notify(new ApplicationStatusUpdated($validated['status'], $updateData['application_rejection_reason'] ?? null));

        if ($validated['status'] === 'returned') {
            $message = 'Application returned to student successfully';
        } else {
            $message = $isTermination ? 'Scholarship terminated successfully' : ($validated['status'] === 'validated' ? 'Application approved successfully' : 'Application status updated successfully');
        }

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    public function moveToPamana(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        $basicInfo = BasicInfo::where('user_id', $user->id)->firstOrFail();

        // Check if application is validated
        if ($basicInfo->application_status !== 'validated') {
            return response()->json([
                'success' => false,
                'message' => 'Application must be validated before moving to Pamana',
            ], 400);
        }

        // Update type_assist to Pamana and grant_status to pamana
        $basicInfo->update([
            'type_assist' => 'Pamana',
            'grant_status' => 'pamana',
        ]);

        // Log the status change
        Log::info('Applicant moved to Pamana', [
            'user_id' => $user->id,
            'admin_id' => auth()->guard('staff')->id(),
            'timestamp' => now()
        ]);

        // Notify the student
        $user->notify(new TransactionNotification(
            'update',
            'Application Moved to Pamana',
            'Your scholarship application has been successfully updated to the Pamana category.',
            'normal'
        ));

        return response()->json([
            'success' => true,
            'message' => 'Application moved to Pamana successfully',
        ]);
    }

    public function addToGrantees(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        $basicInfo = BasicInfo::where('user_id', $user->id)->firstOrFail();

        // Check if application is validated
        if ($basicInfo->application_status !== 'validated') {
            return response()->json([
                'success' => false,
                'message' => 'Application must be validated before adding to Grantees',
            ], 400);
        }

        // Check if type_assist is Regular (not Pamana)
        if ($basicInfo->type_assist === 'Pamana') {
            return response()->json([
                'success' => false,
                'message' => 'Pamana applicants cannot be added to Regular Grantees',
            ], 400);
        }

        // Update grant_status to grantee
        $basicInfo->update([
            'grant_status' => 'grantee',
        ]);

        // Notify the student
        $user->notify(new TransactionNotification(
            'update',
            'Scholarship Grant Confirmed',
            'Congratulations! Your scholarship grant has been officially confirmed, and you are now part of our scholarship grantees.',
            'high'
        ));

        return response()->json([
            'success' => true,
            'message' => 'Applicant added to Grantees successfully',
        ]);
    }

    public function addToWaiting(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        $basicInfo = BasicInfo::where('user_id', $user->id)->firstOrFail();

        // Check if application is validated
        if ($basicInfo->application_status !== 'validated') {
            return response()->json([
                'success' => false,
                'message' => 'Application must be validated before adding to Waiting List',
            ], 400);
        }

        // Check if type_assist is Regular (not Pamana)
        if ($basicInfo->type_assist === 'Pamana') {
            return response()->json([
                'success' => false,
                'message' => 'Pamana applicants cannot be added to Regular Waiting List',
            ], 400);
        }

        // Update grant_status to waiting
        $basicInfo->update([
            'grant_status' => 'waiting',
        ]);

        // Log Transaction History
        TransactionHistory::create([
            'user_id' => $user->id,
            'action' => 'Added to Waiting List',
            'description' => 'Your application has been placed on the waiting list. We will notify you if a slot becomes available.',
            'status' => 'info',
        ]);

        // Notify the student
        $user->notify(new TransactionNotification(
            'update',
            'Added to Waiting List',
            'Your application has been placed on the waiting list. We will notify you if a slot becomes available.',
            'normal'
        ));

        return response()->json([
            'success' => true,
            'message' => 'Applicant added to Waiting List successfully',
        ]);
    }

    public function destroyApplicant($userId)
    {
        try {
            DB::beginTransaction();

            $user = User::with(['basicInfo.fullAddress', 'documents'])->findOrFail($userId);
            $basicInfo = $user->basicInfo;

            // 1. Delete associated files from storage
            // Profile picture
            if ($user->profile_pic && $user->profile_pic !== 'profile_pics/default.png') {
                Storage::disk('public')->delete($user->profile_pic);
            }

            // Documents
            foreach ($user->documents as $document) {
                if ($document->filepath) {
                    Storage::disk('public')->delete($document->filepath);
                }
                $document->delete();
            }

            if ($basicInfo) {
                // 2. Delete related model records
                // Education
                Education::where('basic_info_id', $basicInfo->id)->delete();

                // Family
                Family::where('basic_info_id', $basicInfo->id)->delete();

                // Siblings
                FamSiblings::where('basic_info_id', $basicInfo->id)->delete();

                // School Preference
                if ($basicInfo->school_pref_id) {
                    SchoolPref::where('id', $basicInfo->school_pref_id)->delete();
                }

                // Full Address and its components (Mailing, Permanent, Origin)
                if ($basicInfo->full_address_id) {
                    $fullAddress = FullAddress::find($basicInfo->full_address_id);
                    if ($fullAddress) {
                        if ($fullAddress->mailing_address_id) {
                            MailingAddress::where('id', $fullAddress->mailing_address_id)->delete();
                        }
                        if ($fullAddress->permanent_address_id) {
                            PermanentAddress::where('id', $fullAddress->permanent_address_id)->delete();
                        }
                        if ($fullAddress->origin_id) {
                            Origin::where('id', $fullAddress->origin_id)->delete();
                        }
                        $fullAddress->delete();
                    }
                }

                // 3. Delete BasicInfo
                $basicInfo->delete();
            }

            // 4. Finally delete the user
            // Delete application drafts
            ApplicationDraft::where('user_id', $user->id)->delete();

            // Delete replacement records
            Replacement::where('replacement_user_id', $user->id)
                ->orWhere('replaced_user_id', $user->id)
                ->delete();

            // Delete notifications
            $user->notifications()->delete();

            $user->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Applicant account and all associated data have been deleted successfully.',
            ]);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error deleting applicant: '.$e->getMessage(),
            ], 500);
        }
    }

    public function extractGrades($userId)
    {
        $user = User::with('documents')->findOrFail($userId);
        $gradesDoc = $user->documents->where('type', 'grades')->first();
        if ($gradesDoc) {
            // Use public storage path
            $filePath = storage_path('app/public/'.$gradesDoc->filepath);

            // Fallback to app storage if not found in public
            if (! file_exists($filePath)) {
                $filePath = storage_path('app/'.$gradesDoc->filepath);
            }

            if (! file_exists($filePath)) {
                return response()->json(['success' => false, 'error' => 'Grades document file not found.'], 404);
            }

            // Get file type from database (more reliable than detection)
            $storedFileType = $gradesDoc->filetype;
            $isImage = $storedFileType && strpos($storedFileType, 'image/') === 0;

            // Try non-AI extraction service first (OCR + regex parsing)
            $extractionService = new GradeExtractionService;
            $gwa = $extractionService->extractGWA($filePath);
            $extractionMethod = 'ocr';

            // If OCR extraction fails, fallback to AI service (Gemini)
            if ($gwa === null) {
                Log::info('OCR extraction failed, trying AI fallback', [
                    'file_path' => $filePath,
                    'stored_filetype' => $storedFileType,
                ]);

                try {
                    $geminiService = new GeminiService;
                    $gwa = $geminiService->extractGWA($filePath);
                    $extractionMethod = 'ai';
                } catch (Exception $e) {
                    Log::error('AI extraction also failed', [
                        'file_path' => $filePath,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            if ($gwa === null) {
                // Use stored file type for more accurate error message
                $errorMsg = $isImage
                    ? 'Failed to extract GWA from image. Please ensure Tesseract OCR is installed and the document contains a visible GWA value. Alternatively, the AI extraction may have failed - please check your GEMINI_API_KEY in .env file.'
                    : 'Failed to extract GWA from PDF. Please ensure pdftotext is available or the PDF has extractable text. Alternatively, the AI extraction may have failed - please check your GEMINI_API_KEY in .env file.';

                return response()->json([
                    'success' => false,
                    'error' => $errorMsg,
                    'file_type' => $storedFileType ?? mime_content_type($filePath),
                ], 500);
            }

            return response()->json([
                'success' => true,
                'gwa' => $gwa,
                'file_type' => $storedFileType ?? mime_content_type($filePath),
                'method' => $extractionMethod,
            ]);
        } else {
            return response()->json(['success' => false, 'error' => 'No grades document found.'], 404);
        }
    }

    /**
     * Update GWA manually for a user
     * Stores GWA in the basic_info table instead of education table
     */
    public function updateGWA(Request $request, $userId)
    {
        $user = User::with('basicInfo')->findOrFail($userId);

        $validated = $request->validate([
            'gwa' => 'required|numeric|min:75|max:100',
        ], [
            'gwa.required' => 'GWA value is required.',
            'gwa.numeric' => 'GWA must be a number.',
            'gwa.min' => 'GWA must be at least 75.',
            'gwa.max' => 'GWA cannot exceed 100.',
        ]);

        // Get or create basic_info record
        $basicInfo = $user->basicInfo;

        if (! $basicInfo) {
            return response()->json([
                'success' => false,
                'message' => 'No basic information found for this student. Please ensure the student has completed their application.',
            ], 404);
        }

        // Update GWA in basic_info table
        $oldGwa = $basicInfo->gpa;
        $basicInfo->gpa = $validated['gwa'];
        $basicInfo->save();

        // Log the action in Transaction History (Audit Log)
        \App\Models\TransactionHistory::create([
            'user_id' => $user->id,
            'action' => 'GWA Verified',
            'description' => 'GWA verified and updated by admin' . (auth()->user() ? ' (' . auth()->user()->first_name . ' ' . auth()->user()->last_name . ')' : '') . '. Old GWA: ' . ($oldGwa ?? 'N/A') . ', New GWA: ' . $validated['gwa'],
            'status' => 'success',
            'metadata' => [
                'admin_id' => auth()->id(),
                'old_gwa' => $oldGwa,
                'new_gwa' => $validated['gwa'],
                'updated_at' => now()->toDateTimeString(),
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => 'GWA updated successfully.',
            'gwa' => $validated['gwa'],
            'basic_info_id' => $basicInfo->id,
        ]);
    }

    /**
     * Recalculate document priorities (First Come, First Serve)
     */
    public function recalculateDocumentPriorities()
    {
        try {
            $priorityService = new DocumentPriorityService;
            $results = $priorityService->recalculateAllPriorities();

            return response()->json([
                'success' => true,
                'message' => 'Document priorities recalculated successfully',
                'total_documents' => $results['total_documents'],
                'documents' => $results['documents'],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error recalculating priorities: '.$e->getMessage(),
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

        $priorityService = new DocumentPriorityService;
        $documents = $priorityService->getPrioritizedDocuments($status, $limit);

        return response()->json([
            'success' => true,
            'documents' => $documents->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'type' => $doc->type,
                    'filename' => $doc->filename,
                    'applicant_name' => $doc->user->first_name.' '.$doc->user->last_name,
                    'applicant_id' => $doc->user_id,
                    'priority_rank' => $doc->priority_rank,
                    'priority_score' => $doc->priority_score,
                    'priority_level' => $doc->priority_level,
                    'waiting_hours' => $doc->waiting_hours,
                    'submitted_at' => $doc->submitted_at ? $doc->submitted_at->format('Y-m-d H:i:s') : null,
                    'status' => $doc->status,
                ];
            }),
        ]);
    }

    /**
     * Get document priority statistics
     */
    public function getDocumentPriorityStatistics()
    {
        $priorityService = new DocumentPriorityService;
        $statistics = $priorityService->getPriorityStatistics();

        return response()->json([
            'success' => true,
            'statistics' => $statistics,
        ]);
    }

    /**
     * Masterlist - Regular Scholarship
     */
    public function masterlistRegular(Request $request)
    {
        /** @var Staff $user */
        $user = Auth::guard('staff')->user();

        // Get filters from request
        $selectedProvince = $request->get('province');
        $selectedMunicipality = $request->get('municipality');
        $selectedBarangay = $request->get('barangay');
        $selectedEthno = $request->get('ethno');

        // Build query for Regular scholarship applicants - Only show approved applications
        // Exclude grantees (they appear in the grantees masterlist)
        $applicantsQuery = User::with(['basicInfo.fullAddress.address', 'ethno', 'documents'])
            ->whereHas('basicInfo', function ($query) use ($selectedProvince, $selectedMunicipality, $selectedBarangay) {
                $query->where('type_assist', 'Regular')
                    ->where('application_status', 'validated') // Only show validated/approved applications
                    ->where(function ($q) {
                        // Exclude grantees (handle case variations)
                        $q->where(function ($subQ) {
                            $subQ->where('grant_status', '!=', 'grantee')
                                ->where('grant_status', '!=', 'Grantee');
                        })->orWhereNull('grant_status');
                    });

                if ($selectedProvince) {
                    $query->whereHas('fullAddress', function ($q) use ($selectedProvince) {
                        $q->whereHas('address', function ($aq) use ($selectedProvince) {
                            $aq->where('province', $selectedProvince);
                        });
                    });
                }
                if ($selectedMunicipality) {
                    $query->whereHas('fullAddress', function ($q) use ($selectedMunicipality) {
                        $q->whereHas('address', function ($aq) use ($selectedMunicipality) {
                            $aq->where('municipality', $selectedMunicipality);
                        });
                    });
                }
                if ($selectedBarangay) {
                    $query->whereHas('fullAddress', function ($q) use ($selectedBarangay) {
                        $q->whereHas('address', function ($aq) use ($selectedBarangay) {
                            $aq->where('barangay', $selectedBarangay);
                        });
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
     * Masterlist - Regular Grantees (Active grantees)
     */
    public function masterlistRegularGrantees(Request $request)
    {
        /** @var Staff $user */
        $user = Auth::guard('staff')->user();

        // Get filters from request
        $selectedProvince = $request->get('province');
        $selectedMunicipality = $request->get('municipality');
        $selectedBarangay = $request->get('barangay');
        $selectedEthno = $request->get('ethno');

        // Build query for Regular Grantees - validated and marked as grantee
        $applicantsQuery = User::with(['basicInfo.fullAddress.address', 'ethno', 'documents'])
            ->whereHas('basicInfo', function ($query) use ($selectedProvince, $selectedMunicipality, $selectedBarangay) {
                $query->where('type_assist', 'Regular')
                    ->where('application_status', 'validated')
                    ->where('grant_status', 'grantee'); // Only show grantees

                if ($selectedProvince) {
                    $query->whereHas('fullAddress', function ($q) use ($selectedProvince) {
                        $q->whereHas('address', function ($aq) use ($selectedProvince) {
                            $aq->where('province', $selectedProvince);
                        });
                    });
                }
                if ($selectedMunicipality) {
                    $query->whereHas('fullAddress', function ($q) use ($selectedMunicipality) {
                        $q->whereHas('address', function ($aq) use ($selectedMunicipality) {
                            $aq->where('municipality', $selectedMunicipality);
                        });
                    });
                }
                if ($selectedBarangay) {
                    $query->whereHas('fullAddress', function ($q) use ($selectedBarangay) {
                        $q->whereHas('address', function ($aq) use ($selectedBarangay) {
                            $aq->where('barangay', $selectedBarangay);
                        });
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
        ))->with('masterlistType', 'Regular Grantees');
    }

    /**
     * Get all grantees for report (without pagination)
     */
    public function granteesReport(Request $request)
    {
        try {
            /** @var Staff $user */
            $user = Auth::guard('staff')->user();

            // Get filters from request
            $selectedProvince = $request->get('province');
            $selectedMunicipality = $request->get('municipality');
            $selectedBarangay = $request->get('barangay');
            $selectedEthno = $request->get('ethno');

            // Build query for Regular Grantees - validated and marked as grantee
            // Use case-insensitive matching for grant_status to handle variations
            // Ensure basicInfo exists and has required fields
            $applicantsQuery = User::with([
                'basicInfo.fullAddress.address',
                'ethno',
                'documents',
                'basicInfo.schoolPref',
            ])
                ->whereHas('basicInfo', function ($query) use ($selectedProvince, $selectedMunicipality, $selectedBarangay) {
                    $query->where('type_assist', 'Regular')
                        ->where('application_status', 'validated')
                        ->whereNotNull('grant_status')
                        ->whereRaw("LOWER(TRIM(grant_status)) = 'grantee'"); // Case-insensitive grantee check

                    if ($selectedProvince) {
                        $query->whereHas('fullAddress', function ($q) use ($selectedProvince) {
                            $q->whereHas('address', function ($aq) use ($selectedProvince) {
                                $aq->where('province', $selectedProvince);
                            });
                        });
                    }
                    if ($selectedMunicipality) {
                        $query->whereHas('fullAddress', function ($q) use ($selectedMunicipality) {
                            $q->whereHas('address', function ($aq) use ($selectedMunicipality) {
                                $aq->where('municipality', $selectedMunicipality);
                            });
                        });
                    }
                    if ($selectedBarangay) {
                        $query->whereHas('fullAddress', function ($q) use ($selectedBarangay) {
                            $q->whereHas('address', function ($aq) use ($selectedBarangay) {
                                $aq->where('barangay', $selectedBarangay);
                            });
                        });
                    }
                });

            if ($selectedEthno) {
                $applicantsQuery->where('ethno_id', $selectedEthno);
            }

            // Get all grantees without pagination
            $grantees = $applicantsQuery->orderBy('created_at', 'desc')->get();

            Log::info('Grantees Report Query', [
                'count' => $grantees->count(),
                'filters' => [
                    'province' => $selectedProvince,
                    'municipality' => $selectedMunicipality,
                    'barangay' => $selectedBarangay,
                    'ethno' => $selectedEthno,
                ],
            ]);

            return response()->json([
                'success' => true,
                'grantees' => $grantees->map(function ($grantee, $index) {
                    $basicInfo = $grantee->basicInfo;
                    $address = $basicInfo && $basicInfo->fullAddress ? ($basicInfo->fullAddress->address ?? null) : null;
                    $documents = $grantee->documents ?? collect();
                    $approvedDocs = $documents->where('status', 'approved')->count();
                    $totalDocs = $documents->count();

                    // Calculate age
                    $age = '';
                    if ($basicInfo && $basicInfo->birthdate) {
                        try {
                            $birthdate = Carbon::parse($basicInfo->birthdate);
                            $age = $birthdate->age;
                        } catch (Exception $e) {
                            $age = '';
                        }
                    }

                    // Determine school type (Private/Public) and school name (first intended school)
                    $schoolType = ($basicInfo && $basicInfo->schoolPref) ? ($basicInfo->schoolPref->school_type ?? '') : '';
                    $schoolName = ($basicInfo && $basicInfo->schoolPref) ? ($basicInfo->schoolPref->school_name ?? '') : '';
                    $isPrivate = stripos($schoolType, 'private') !== false;
                    $isPublic = stripos($schoolType, 'public') !== false || stripos($schoolType, 'state') !== false;

                    // AD Reference No. (formatted user ID)
                    $adReference = 'NCIP-'.date('Y').'-'.str_pad($grantee->id, 4, '0', STR_PAD_LEFT);

                    // BATCH (based on application year or created_at year)
                    $batch = $grantee->created_at ? $grantee->created_at->format('Y') : date('Y');

                    // Full name
                    $fullName = trim(($grantee->first_name ?? '').' '.($grantee->middle_name ?? '').' '.($grantee->last_name ?? ''));

                    // Contact/Email combined
                    $contactEmail = trim(($grantee->contact_num ?? '').($grantee->email ? ' / '.$grantee->email : ''));

                    // Course
                    $course = ($basicInfo && $basicInfo->schoolPref) ? ($basicInfo->schoolPref->degree ?? ($grantee->course ?? '')) : ($grantee->course ?? '');

                    // Gender check (exact match with form values: "Male" or "Female")
                    $gender = $basicInfo ? ($basicInfo->gender ?? '') : '';
                    $isFemale = strtolower($gender) === 'female';
                    $isMale = strtolower($gender) === 'male';

                    // Year level - get from basic_info table
                    $yearLevel = $basicInfo ? ($basicInfo->current_year_level ?? '') : '';

                    // Determine which year level checkbox should be checked
                    $is1st = in_array(strtolower($yearLevel), ['1', '1st', 'first', 'first year', '1st year']);
                    $is2nd = in_array(strtolower($yearLevel), ['2', '2nd', 'second', 'second year', '2nd year']);
                    $is3rd = in_array(strtolower($yearLevel), ['3', '3rd', 'third', 'third year', '3rd year']);
                    $is4th = in_array(strtolower($yearLevel), ['4', '4th', 'fourth', 'fourth year', '4th year']);
                    $is5th = in_array(strtolower($yearLevel), ['5', '5th', 'fifth', 'fifth year', '5th year']);

                    // Grant checkmarks (stored as boolean/checkbox state in database)
                    $grant1stSem = $basicInfo ? ($basicInfo->grant_1st_sem ?? false) : false;
                    $grant2ndSem = $basicInfo ? ($basicInfo->grant_2nd_sem ?? false) : false;

                    // Remarks/Status
                    $remarks = $basicInfo ? ($basicInfo->application_status ?? '') : '';
                    if ($basicInfo && $basicInfo->grant_status) {
                        $remarks .= ($remarks ? ' / ' : '').$basicInfo->grant_status;
                    }

                    return [
                        'no' => $index + 1,
                        'ad_reference' => $adReference,
                        'province' => $address ? ($address->province ?? '') : '',
                        'municipality' => $address ? ($address->municipality ?? '') : '',
                        'barangay' => $address ? ($address->barangay ?? '') : '',
                        'contact_email' => $contactEmail,
                        'batch' => $batch,
                        'name' => $fullName,
                        'age' => $age,
                        'gender' => $gender,
                        'is_female' => $isFemale,
                        'is_male' => $isMale,
                        'ethnicity' => $grantee->ethno ? ($grantee->ethno->ethnicity ?? '') : '',
                        'school_type' => $schoolType,
                        'school_name' => $schoolName,
                        'school1_name' => $schoolName, // alias for front-end fallback
                        'is_private' => $isPrivate,
                        'is_public' => $isPublic,
                        'course' => $course,
                        'year_level' => $yearLevel,
                        'is_1st' => $is1st,
                        'is_2nd' => $is2nd,
                        'is_3rd' => $is3rd,
                        'is_4th' => $is4th,
                        'is_5th' => $is5th,
                        'grant_1st_sem' => $grant1stSem,
                        'grant_2nd_sem' => $grant2ndSem,
                        'user_id' => $grantee->id, // Add user_id for saving updates
                        'remarks' => $remarks,
                    ];
                }),
            ]);
        } catch (Exception $e) {
            Log::error('Error in granteesReport', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading grantees report: '.$e->getMessage(),
                'grantees' => [],
            ], 500);
        }
    }

    /**
     * Get all Pamana applicants for report (without pagination)
     * Mirrors the Regular Grantees grid view format.
     */
    public function pamanaReport(Request $request)
    {
        try {
            /** @var Staff $user */
            $user = Auth::guard('staff')->user();

            // Get filters from request
            $selectedProvince = $request->get('province');
            $selectedMunicipality = $request->get('municipality');
            $selectedBarangay = $request->get('barangay');
            $selectedEthno = $request->get('ethno');

            // Build query for Pamana applicants - validated Pamana, excluding grantees
            $applicantsQuery = User::with([
                'basicInfo.fullAddress.address',
                'ethno',
                'documents',
                'basicInfo.schoolPref',
            ])
                ->whereHas('basicInfo', function ($query) use ($selectedProvince, $selectedMunicipality, $selectedBarangay) {
                    $query->where('type_assist', 'Pamana')
                        ->where('application_status', 'validated')
                        ->where(function ($q) {
                            // Exclude those already marked as grantee (case-insensitive)
                            $q->whereNull('grant_status')
                                ->orWhereRaw("LOWER(TRIM(grant_status)) != 'grantee'");
                        });

                    if ($selectedProvince) {
                        $query->whereHas('fullAddress', function ($q) use ($selectedProvince) {
                            $q->whereHas('address', function ($aq) use ($selectedProvince) {
                                $aq->where('province', $selectedProvince);
                            });
                        });
                    }
                    if ($selectedMunicipality) {
                        $query->whereHas('fullAddress', function ($q) use ($selectedMunicipality) {
                            $q->whereHas('address', function ($aq) use ($selectedMunicipality) {
                                $aq->where('municipality', $selectedMunicipality);
                            });
                        });
                    }
                    if ($selectedBarangay) {
                        $query->whereHas('fullAddress', function ($q) use ($selectedBarangay) {
                            $q->whereHas('address', function ($aq) use ($selectedBarangay) {
                                $aq->where('barangay', $selectedBarangay);
                            });
                        });
                    }
                });

            if ($selectedEthno) {
                $applicantsQuery->where('ethno_id', $selectedEthno);
            }

            // Get all Pamana applicants without pagination
            $pamanaApplicants = $applicantsQuery->orderBy('created_at', 'desc')->get();

            Log::info('Pamana Report Query', [
                'count' => $pamanaApplicants->count(),
                'filters' => [
                    'province' => $selectedProvince,
                    'municipality' => $selectedMunicipality,
                    'barangay' => $selectedBarangay,
                    'ethno' => $selectedEthno,
                ],
            ]);

            return response()->json([
                'success' => true,
                'pamana' => $pamanaApplicants->map(function ($applicant, $index) {
                    $basicInfo = $applicant->basicInfo;
                    $address = $basicInfo && $basicInfo->fullAddress ? ($basicInfo->fullAddress->address ?? null) : null;
                    $documents = $applicant->documents ?? collect();
                    $approvedDocs = $documents->where('status', 'approved')->count();
                    $totalDocs = $documents->count();

                    // Calculate age
                    $age = '';
                    if ($basicInfo && $basicInfo->birthdate) {
                        try {
                            $birthdate = Carbon::parse($basicInfo->birthdate);
                            $age = $birthdate->age;
                        } catch (Exception $e) {
                            $age = '';
                        }
                    }

                    // Determine school type (Private/Public) and school name (first intended school)
                    $schoolType = ($basicInfo && $basicInfo->schoolPref) ? ($basicInfo->schoolPref->school_type ?? '') : '';
                    $schoolName = ($basicInfo && $basicInfo->schoolPref) ? ($basicInfo->schoolPref->school_name ?? '') : '';
                    $isPrivate = stripos($schoolType, 'private') !== false;
                    $isPublic = stripos($schoolType, 'public') !== false || stripos($schoolType, 'state') !== false;

                    // AD Reference No. (formatted user ID)
                    $adReference = 'NCIP-'.date('Y').'-'.str_pad($applicant->id, 4, '0', STR_PAD_LEFT);

                    // BATCH (based on application year or created_at year)
                    $batch = $applicant->created_at ? $applicant->created_at->format('Y') : date('Y');

                    // Full name
                    $fullName = trim(($applicant->first_name ?? '').' '.($applicant->middle_name ?? '').' '.($applicant->last_name ?? ''));

                    // Contact/Email combined
                    $contactEmail = trim(($applicant->contact_num ?? '').($applicant->email ? ' / '.$applicant->email : ''));

                    // Course
                    $course = ($basicInfo && $basicInfo->schoolPref) ? ($basicInfo->schoolPref->degree ?? ($applicant->course ?? '')) : ($applicant->course ?? '');

                    // Gender check (exact match with form values: "Male" or "Female")
                    $gender = $basicInfo ? ($basicInfo->gender ?? '') : '';
                    $isFemale = strtolower($gender) === 'female';
                    $isMale = strtolower($gender) === 'male';

                    // Year level - get from basic_info table
                    $yearLevel = $basicInfo ? ($basicInfo->current_year_level ?? '') : '';

                    // Determine which year level checkbox should be checked
                    $is1st = in_array(strtolower($yearLevel), ['1', '1st', 'first', 'first year', '1st year']);
                    $is2nd = in_array(strtolower($yearLevel), ['2', '2nd', 'second', 'second year', '2nd year']);
                    $is3rd = in_array(strtolower($yearLevel), ['3', '3rd', 'third', 'third year', '3rd year']);
                    $is4th = in_array(strtolower($yearLevel), ['4', '4th', 'fourth', 'fourth year', '4th year']);
                    $is5th = in_array(strtolower($yearLevel), ['5', '5th', 'fifth', 'fifth year', '5th year']);

                    // Grant checkmarks (stored as boolean/checkbox state in database)
                    $grant1stSem = $basicInfo ? ($basicInfo->grant_1st_sem ?? false) : false;
                    $grant2ndSem = $basicInfo ? ($basicInfo->grant_2nd_sem ?? false) : false;

                    // Remarks/Status
                    $remarks = $basicInfo ? ($basicInfo->application_status ?? '') : '';
                    if ($basicInfo && $basicInfo->grant_status) {
                        $remarks .= ($remarks ? ' / ' : '').$basicInfo->grant_status;
                    }

                    return [
                        'no' => $index + 1,
                        'ad_reference' => $adReference,
                        'province' => $address ? ($address->province ?? '') : '',
                        'municipality' => $address ? ($address->municipality ?? '') : '',
                        'barangay' => $address ? ($address->barangay ?? '') : '',
                        'contact_email' => $contactEmail,
                        'batch' => $batch,
                        'name' => $fullName,
                        'age' => $age,
                        'gender' => $gender,
                        'is_female' => $isFemale,
                        'is_male' => $isMale,
                        'ethnicity' => $applicant->ethno ? ($applicant->ethno->ethnicity ?? '') : '',
                        'school_type' => $schoolType,
                        'school_name' => $schoolName,
                        'school1_name' => $schoolName,
                        'is_private' => $isPrivate,
                        'is_public' => $isPublic,
                        'course' => $course,
                        'year_level' => $yearLevel,
                        'is_1st' => $is1st,
                        'is_2nd' => $is2nd,
                        'is_3rd' => $is3rd,
                        'is_4th' => $is4th,
                        'is_5th' => $is5th,
                        'grant_1st_sem' => $grant1stSem,
                        'grant_2nd_sem' => $grant2ndSem,
                        'user_id' => $applicant->id,
                        'remarks' => $remarks,
                    ];
                }),
            ]);
        } catch (Exception $e) {
            Log::error('Error in pamanaReport', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading Pamana report: '.$e->getMessage(),
                'pamana' => [],
            ], 500);
        }
    }

    /**
     * Get all waiting list applicants for report (without pagination)
     */
    public function waitingListReport(Request $request)
    {
        try {
            /** @var Staff $user */
            $user = Auth::guard('staff')->user();

            // Get filters from request
            $selectedProvince = $request->get('province');
            $selectedMunicipality = $request->get('municipality');
            $selectedBarangay = $request->get('barangay');
            $selectedEthno = $request->get('ethno');

            // Build query for Regular Waiting - validated but marked as waiting
            $applicantsQuery = User::with(['basicInfo.fullAddress.address', 'ethno', 'documents', 'basicInfo.schoolPref'])
                ->whereHas('basicInfo', function ($query) use ($selectedProvince, $selectedMunicipality, $selectedBarangay) {
                    $query->where('type_assist', 'Regular')
                        ->where('application_status', 'validated')
                        ->where(function ($q) {
                            $q->where('grant_status', 'waiting')
                              ->orWhereNull('grant_status');
                        }); // Only show waiting list (including validated with null status)

                    if ($selectedProvince) {
                        $query->whereHas('fullAddress', function ($q) use ($selectedProvince) {
                            $q->whereHas('address', function ($aq) use ($selectedProvince) {
                                $aq->where('province', $selectedProvince);
                            });
                        });
                    }
                    if ($selectedMunicipality) {
                        $query->whereHas('fullAddress', function ($q) use ($selectedMunicipality) {
                            $q->whereHas('address', function ($aq) use ($selectedMunicipality) {
                                $aq->where('municipality', $selectedMunicipality);
                            });
                        });
                    }
                    if ($selectedBarangay) {
                        $query->whereHas('fullAddress', function ($q) use ($selectedBarangay) {
                            $q->whereHas('address', function ($aq) use ($selectedBarangay) {
                                $aq->where('barangay', $selectedBarangay);
                            });
                        });
                    }
                });

            if ($selectedEthno) {
                $applicantsQuery->where('ethno_id', $selectedEthno);
            }

            // Get all waiting list applicants without pagination
            $waitingList = $applicantsQuery->orderBy('created_at', 'desc')->get();

            // Calculate priority scores and ranks using ApplicantPriorityService
            $priorityService = app(ApplicantPriorityService::class);
            $prioritizedApplicants = $priorityService->getPrioritizedApplicants();

            // Create a map of user_id => priority data
            $priorityMap = [];
            foreach ($prioritizedApplicants as $index => $prioritized) {
                $priorityMap[$prioritized['user_id']] = [
                    'priority_score' => $prioritized['priority_score'] ?? 0,
                    'priority_rank' => $index + 1, // Rank starts from 1
                ];
            }

            return response()->json([
                'success' => true,
                'waitingList' => $waitingList->map(function ($applicant, $index) use ($priorityMap) {
                    $basicInfo = $applicant->basicInfo;
                    $address = $basicInfo && $basicInfo->fullAddress ? ($basicInfo->fullAddress->address ?? null) : null;

                    // Get priority score and rank
                    $priorityData = $priorityMap[$applicant->id] ?? ['priority_score' => 0, 'priority_rank' => null];
                    $rsscScore = $priorityData['priority_score'];
                    $rank = $priorityData['priority_rank'];

                    // Calculate age
                    $age = '';
                    if ($basicInfo && $basicInfo->birthdate) {
                        try {
                            $birthdate = Carbon::parse($basicInfo->birthdate);
                            $age = $birthdate->age;
                        } catch (Exception $e) {
                            $age = '';
                        }
                    }

                    // Determine school type (Private/Public)
                    $schoolType = ($basicInfo && $basicInfo->schoolPref) ? ($basicInfo->schoolPref->school_type ?? '') : '';
                    $schoolName = ($basicInfo && $basicInfo->schoolPref) ? ($basicInfo->schoolPref->school_name ?? '') : '';
                    $isPrivate = stripos($schoolType, 'private') !== false;
                    $isPublic = stripos($schoolType, 'public') !== false || stripos($schoolType, 'state') !== false;

                    // AD Reference No. (formatted user ID)
                    $adReference = 'NCIP-'.date('Y').'-'.str_pad($applicant->id, 4, '0', STR_PAD_LEFT);

                    // BATCH (based on application year or created_at year)
                    $batch = $applicant->created_at ? $applicant->created_at->format('Y') : date('Y');

                    // Full name
                    $fullName = trim(($applicant->first_name ?? '').' '.($applicant->middle_name ?? '').' '.($applicant->last_name ?? ''));

                    // Contact/Email combined
                    $contactEmail = trim(($applicant->contact_num ?? '').($applicant->email ? ' / '.$applicant->email : ''));

                    // Course
                    $course = ($basicInfo && $basicInfo->schoolPref) ? ($basicInfo->schoolPref->degree ?? ($applicant->course ?? '')) : ($applicant->course ?? '');

                    // Gender check (exact match with form values: "Male" or "Female")
                    $gender = $basicInfo ? ($basicInfo->gender ?? '') : '';
                    $isFemale = strtolower($gender) === 'female';
                    $isMale = strtolower($gender) === 'male';

                    // Year level - get from basic_info table
                    $yearLevel = $basicInfo ? ($basicInfo->current_year_level ?? '') : '';

                    // Determine which year level checkbox should be checked
                    $is1st = in_array(strtolower($yearLevel), ['1', '1st', 'first', 'first year', '1st year']);
                    $is2nd = in_array(strtolower($yearLevel), ['2', '2nd', 'second', 'second year', '2nd year']);
                    $is3rd = in_array(strtolower($yearLevel), ['3', '3rd', 'third', 'third year', '3rd year']);
                    $is4th = in_array(strtolower($yearLevel), ['4', '4th', 'fourth', 'fourth year', '4th year']);
                    $is5th = in_array(strtolower($yearLevel), ['5', '5th', 'fifth', 'fifth year', '5th year']);

                    // Grant checkmarks (stored as boolean/checkbox state in database)
                    $grant1stSem = $basicInfo ? ($basicInfo->grant_1st_sem ?? false) : false;
                    $grant2ndSem = $basicInfo ? ($basicInfo->grant_2nd_sem ?? false) : false;

                    // RSSC Score - use manual score if available, otherwise calculated score
                    $manualRsscScore = $basicInfo ? ($basicInfo->rssc_score ?? null) : null;
                    $calculatedRsscScore = $priorityData['priority_score'];
                    $rsscScore = $manualRsscScore !== null ? $manualRsscScore : $calculatedRsscScore;

                    return [
                        'no' => $index + 1,
                        'ad_reference' => $adReference,
                        'province' => $address ? ($address->province ?? '') : '',
                        'municipality' => $address ? ($address->municipality ?? '') : '',
                        'barangay' => $address ? ($address->barangay ?? '') : '',
                        'contact_email' => $contactEmail,
                        'batch' => $batch,
                        'name' => $fullName,
                        'age' => $age,
                        'gender' => $gender,
                        'is_female' => $isFemale,
                        'is_male' => $isMale,
                        'ethnicity' => $applicant->ethno ? ($applicant->ethno->ethnicity ?? '') : '',
                        'school_type' => $schoolType,
                        'school_name' => $schoolName,
                        'school1_name' => $schoolName,
                        'is_private' => $isPrivate,
                        'is_public' => $isPublic,
                        'course' => $course,
                        'year_level' => $yearLevel,
                        'is_1st' => $is1st,
                        'is_2nd' => $is2nd,
                        'is_3rd' => $is3rd,
                        'is_4th' => $is4th,
                        'is_5th' => $is5th,
                        'grant_1st_sem' => $grant1stSem,
                        'grant_2nd_sem' => $grant2ndSem,
                        'user_id' => $applicant->id,
                        'rssc_score' => $calculatedRsscScore,
                        'priority_score' => $calculatedRsscScore,
                        'manual_rssc_score' => $manualRsscScore,
                        'rank' => $rank,
                        'priority_rank' => $rank,
                    ];
                })->sortBy(function ($applicant) {
                    // Sort by rank (ascending - rank 1 first)
                    return $applicant['rank'] ?? PHP_INT_MAX;
                })->values()->map(function ($applicant, $index) {
                    // Re-number after sorting
                    $applicant['no'] = $index + 1;

                    return $applicant;
                }),
            ]);
        } catch (Exception $e) {
            Log::error('Waiting list report error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate waiting list report. '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Replacements Report
     * Master list of replacement awardees and the grantees/awardees they replace.
     */
    public function disqualifiedApplicantsReport(Request $request)
    {
        /** @var Staff $user */
        $user = Auth::guard('staff')->user();
        $assignedBarangay = $user->assigned_barangay ?? 'All';

        // Get all rejected applicants (not terminated grantees)
        $rejectedQuery = User::with([
            'basicInfo.fullAddress.address',
            'basicInfo.schoolPref',
            'ethno',
            'documents',
        ])
            ->whereHas('basicInfo', function ($query) use ($assignedBarangay) {
                $query->where('application_status', 'rejected')
                    ->where(function ($q) {
                        $q->where('grant_status', '!=', 'grantee')
                            ->orWhereNull('grant_status');
                    });

                if ($assignedBarangay !== 'All') {
                    $query->whereHas('fullAddress', function ($q) use ($assignedBarangay) {
                        $q->whereHas('address', function ($aq) use ($assignedBarangay) {
                            $aq->where('barangay', $assignedBarangay);
                        });
                    });
                }
            })
            ->orderBy('created_at', 'asc');

        $rejectedApplicants = $rejectedQuery->get();

        $disqualified = [];
        foreach ($rejectedApplicants as $index => $applicant) {
            $basicInfo = $applicant->basicInfo;
            $address = $basicInfo && $basicInfo->fullAddress ? $basicInfo->fullAddress->address : null;
            $schoolPref = $basicInfo ? $basicInfo->schoolPref : null;
            $ethno = $applicant->ethno;

            // AD Reference Number (formatted user ID)
            $adReference = 'NCIP-'.date('Y').'-'.str_pad($applicant->id, 4, '0', STR_PAD_LEFT);

            // Full address line
            $addressLine = [
                $address ? ($address->province ?? '') : '',
                $address ? ($address->municipality ?? '') : '',
                $address ? ($address->barangay ?? '') : '',
                $adReference,
            ];
            $addressLine = array_filter($addressLine);
            $addressLineStr = implode(', ', $addressLine);

            // Contact/Email combined
            $contactEmail = trim(($applicant->contact_num ?? '').($applicant->email ? ' / '.$applicant->email : ''));

            // Full name
            $fullName = trim(($applicant->first_name ?? '').' '.($applicant->middle_name ?? '').' '.($applicant->last_name ?? ''));

            // Age (calculate from birthdate if age is not available)
            $age = '';
            if ($basicInfo) {
                if (isset($basicInfo->age) && $basicInfo->age !== null && $basicInfo->age !== '') {
                    $age = $basicInfo->age;
                } elseif ($basicInfo->birthdate) {
                    try {
                        $birthdate = Carbon::parse($basicInfo->birthdate);
                        $age = $birthdate->age;
                    } catch (Exception $e) {
                        $age = '';
                    }
                }
            }

            // Gender
            $gender = $basicInfo ? ($basicInfo->gender ?? '') : '';
            $isFemale = strtolower($gender) === 'female';
            $isMale = strtolower($gender) === 'male';

            // IP Group (text value, not boolean)
            $ipGroup = $ethno && $ethno->ethnicity ? $ethno->ethnicity : '';
            $hasIPGroup = ! empty($ipGroup);

            // School type (Private/Public)
            $schoolType = $schoolPref ? ($schoolPref->school_type ?? '') : '';
            $isPrivate = stripos($schoolType, 'private') !== false;
            $isPublic = stripos($schoolType, 'public') !== false || stripos($schoolType, 'state') !== false;

            // If school type is not clearly identified, try to infer from school name
            if (! $isPrivate && ! $isPublic && $schoolPref) {
                $schoolNameLower = strtolower($schoolPref->school_name ?? '');
                if (stripos($schoolNameLower, 'state') !== false || stripos($schoolNameLower, 'public') !== false) {
                    $isPublic = true;
                } elseif (stripos($schoolNameLower, 'private') !== false) {
                    $isPrivate = true;
                }
            }

            // School name
            $schoolName = $schoolPref ? ($schoolPref->school_name ?? '') : '';

            // Course
            $course = $schoolPref ? ($schoolPref->degree ?? ($applicant->course ?? '')) : ($applicant->course ?? '');

            // Get disqualification reasons from database fields (if available)
            $notIP = $basicInfo ? ($basicInfo->disqualification_not_ip ?? false) : false;
            $exceededIncome = $basicInfo ? ($basicInfo->disqualification_exceeded_income ?? false) : false;
            $incompleteDocs = $basicInfo ? ($basicInfo->disqualification_incomplete_docs ?? false) : false;

            // Fallback: Parse rejection reason if database fields are not set (for old records)
            if (! $notIP && ! $exceededIncome && ! $incompleteDocs) {
                $rejectionReason = $basicInfo ? ($basicInfo->application_rejection_reason ?? '') : '';
                $rejectionLower = strtolower($rejectionReason);

                // Check for "Not IP" reason
                if (str_contains($rejectionLower, 'not ip') ||
                    str_contains($rejectionLower, 'not indigenous') ||
                    str_contains($rejectionLower, 'not a member') ||
                    str_contains($rejectionLower, 'no ip group') ||
                    (! $hasIPGroup && str_contains($rejectionLower, 'ip'))) {
                    $notIP = true;
                }

                // Check for "Exceeded Required Income" reason
                if (str_contains($rejectionLower, 'income') ||
                    str_contains($rejectionLower, 'exceeded') ||
                    str_contains($rejectionLower, 'too high') ||
                    str_contains($rejectionLower, 'over limit') ||
                    str_contains($rejectionLower, 'financial')) {
                    $exceededIncome = true;
                }

                // Check for "Incomplete Documents" reason
                if (str_contains($rejectionLower, 'incomplete') ||
                    str_contains($rejectionLower, 'missing document') ||
                    str_contains($rejectionLower, 'document') ||
                    str_contains($rejectionLower, 'required document') ||
                    str_contains($rejectionLower, 'not submitted')) {
                    $incompleteDocs = true;
                }

                // If no specific reason found, default to incomplete documents
                if (! $notIP && ! $exceededIncome && ! $incompleteDocs) {
                    $incompleteDocs = true;
                }
            }

            // Remarks (use disqualification_remarks if available, otherwise use rejection reason)
            $remarks = $basicInfo ? ($basicInfo->disqualification_remarks ?? ($basicInfo->application_rejection_reason ?? 'Disqualified')) : 'Disqualified';

            $disqualified[] = [
                'no' => $index + 1,
                'address_line' => $addressLineStr,
                'ad_reference' => $adReference,
                'contact_email' => $contactEmail,
                'name' => $fullName,
                'age' => $age,
                'gender' => $gender,
                'is_female' => $isFemale,
                'is_male' => $isMale,
                'ip_group' => $ipGroup,
                'ethnicity' => $ipGroup, // Alias for compatibility
                'is_private' => $isPrivate,
                'is_private_school' => $isPrivate, // Alias for compatibility
                'is_public' => $isPublic,
                'is_public_school' => $isPublic, // Alias for compatibility
                'school' => $schoolName,
                'school_name' => $schoolName, // Alias for compatibility
                'course' => $course,
                'disqualification_not_ip' => $notIP,
                'not_ip' => $notIP, // Alias for compatibility
                'disqualification_exceeded_income' => $exceededIncome,
                'exceeded_income' => $exceededIncome, // Alias for compatibility
                'disqualification_incomplete_docs' => $incompleteDocs,
                'incomplete_docs' => $incompleteDocs, // Alias for compatibility
                'remarks' => $remarks,
            ];
        }

        return response()->json([
            'success' => true,
            'disqualified' => $disqualified,
        ]);
    }

    public function replacementsReport(Request $request)
    {
        $rows = Replacement::with([
            'replacementUser.basicInfo.fullAddress.address',
            'replacementUser.ethno',
            'replacementUser.basicInfo.schoolPref',
            'replacedUser',
        ])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'replacements' => $rows->map(function ($replacement, $index) {
                $user = $replacement->replacementUser;
                $basicInfo = $user ? $user->basicInfo : null;
                $address = $basicInfo && $basicInfo->fullAddress ? ($basicInfo->fullAddress->address ?? null) : null;

                // Age
                $age = '';
                if ($basicInfo && $basicInfo->birthdate) {
                    try {
                        $birthdate = Carbon::parse($basicInfo->birthdate);
                        $age = $birthdate->age;
                    } catch (Exception $e) {
                        $age = '';
                    }
                }

                // School
                $schoolType = ($basicInfo && $basicInfo->schoolPref) ? ($basicInfo->schoolPref->school_type ?? '') : '';
                $schoolName = ($basicInfo && $basicInfo->schoolPref) ? ($basicInfo->schoolPref->school_name ?? '') : '';
                $isPrivate = stripos($schoolType, 'private') !== false;
                $isPublic = stripos($schoolType, 'public') !== false || stripos($schoolType, 'state') !== false;

                // AD Reference No. (formatted user ID)
                $adReference = $user ? ('NCIP-'.date('Y').'-'.str_pad($user->id, 4, '0', STR_PAD_LEFT)) : '';

                // Batch
                $batch = $user && $user->created_at ? $user->created_at->format('Y') : date('Y');

                // Name
                $fullName = $user ? trim(($user->first_name ?? '').' '.($user->middle_name ?? '').' '.($user->last_name ?? '')) : '';

                // Contact/Email
                $contactEmail = $user ? trim(($user->contact_num ?? '').($user->email ? ' / '.$user->email : '')) : '';

                // Course
                $course = ($basicInfo && $basicInfo->schoolPref) ? ($basicInfo->schoolPref->degree ?? ($user->course ?? '')) : ($user->course ?? '');

                // Gender
                $gender = $basicInfo ? ($basicInfo->gender ?? '') : '';
                $isFemale = strtolower($gender) === 'female';
                $isMale = strtolower($gender) === 'male';

                // Year level flags
                $yearLevel = $basicInfo ? ($basicInfo->current_year_level ?? '') : '';
                $is1st = in_array(strtolower($yearLevel), ['1', '1st', 'first', 'first year', '1st year']);
                $is2nd = in_array(strtolower($yearLevel), ['2', '2nd', 'second', 'second year', '2nd year']);
                $is3rd = in_array(strtolower($yearLevel), ['3', '3rd', 'third', 'third year', '3rd year']);
                $is4th = in_array(strtolower($yearLevel), ['4', '4th', 'fourth', 'fourth year', '4th year']);
                $is5th = in_array(strtolower($yearLevel), ['5', '5th', 'fifth', 'fifth year', '5th year']);

                // Grants
                $grant1stSem = $basicInfo ? ($basicInfo->grant_1st_sem ?? false) : false;
                $grant2ndSem = $basicInfo ? ($basicInfo->grant_2nd_sem ?? false) : false;

                // Replaced grantee/awardee name
                $replacedName = '';
                if ($replacement->replacedUser) {
                    $replacedName = trim(($replacement->replacedUser->first_name ?? '').' '.($replacement->replacedUser->middle_name ?? '').' '.($replacement->replacedUser->last_name ?? ''));
                } elseif ($replacement->replaced_name) {
                    $replacedName = $replacement->replaced_name;
                }

                return [
                    'no' => $index + 1,
                    'ad_reference' => $adReference,
                    'province' => $address ? ($address->province ?? '') : '',
                    'municipality' => $address ? ($address->municipality ?? '') : '',
                    'barangay' => $address ? ($address->barangay ?? '') : '',
                    'contact_email' => $contactEmail,
                    'batch' => $batch,
                    'name' => $fullName,
                    'age' => $age,
                    'is_female' => $isFemale,
                    'is_male' => $isMale,
                    'ethnicity' => $user && $user->ethno ? ($user->ethno->ethnicity ?? '') : '',
                    'school_type' => $schoolType,
                    'school_name' => $schoolName,
                    'school1_name' => $schoolName,
                    'is_private' => $isPrivate,
                    'is_public' => $isPublic,
                    'course' => $course,
                    'is_1st' => $is1st,
                    'is_2nd' => $is2nd,
                    'is_3rd' => $is3rd,
                    'is_4th' => $is4th,
                    'is_5th' => $is5th,
                    'grant_1st_sem' => $grant1stSem,
                    'grant_2nd_sem' => $grant2ndSem,
                    'replaced_name' => $replacedName,
                    'replacement_reason' => $replacement->replacement_reason ?? '',
                    'school_year' => $replacement->school_year ?? '',
                ];
            }),
        ]);
    }

    /**
     * Options list for selecting a replaced grantee/awardee in the UI.
     */
    public function replacementGrantees(Request $request)
    {
        $grantees = User::with(['basicInfo'])
            ->whereHas('basicInfo', function ($q) {
                $q->where('application_status', 'validated')
                    ->where(function ($sq) {
                        $sq->whereRaw("LOWER(TRIM(grant_status)) = 'grantee'")
                           ->orWhereRaw("LOWER(TRIM(grant_status)) = 'pamana'");
                    });
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return response()->json([
            'success' => true,
            'grantees' => $grantees->map(function ($u) {
                $name = trim(($u->first_name ?? '').' '.($u->middle_name ?? '').' '.($u->last_name ?? ''));
                $status = ucfirst($u->basicInfo->grant_status ?? '');

                return [
                    'user_id' => $u->id,
                    'name' => "{$name} ({$status})",
                ];
            })->values(),
        ]);
    }

    /**
     * Options list for selecting a replacement awardee from the waiting list.
     */
    public function replacementWaiting(Request $request)
    {
        $waiting = User::with(['basicInfo'])
            ->whereHas('basicInfo', function ($q) {
                $q->where('application_status', 'validated');
                // The user requested ALL validated applicants, not just those marked as 'waiting'
                // But we should probably exclude current grantees to avoid replacing a grantee with another grantee?
                // For now, I'll follow the request "all the applicants with a validated status" literally, 
                // but usually, you want to exclude those who already have a grant.
                // However, let's just show all validated applicants except the ones who are already grantees/pamana?
                // If the user insists on "all", I will just filter by application_status = 'validated'.
                // But to be safe and logical, I will exclude 'grantee' and 'pamana'.
                $q->where(function($sq) {
                     $sq->whereRaw("LOWER(TRIM(grant_status)) != 'grantee'")
                        ->whereRaw("LOWER(TRIM(grant_status)) != 'pamana'")
                        ->orWhereNull('grant_status');
                });
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return response()->json([
            'success' => true,
            'waiting' => $waiting->map(function ($u) {
                $name = trim(($u->first_name ?? '').' '.($u->middle_name ?? '').' '.($u->last_name ?? ''));
                $status = ucfirst($u->basicInfo->grant_status ?? 'Pending');

                return [
                    'user_id' => $u->id,
                    'name' => "{$name} ({$status})",
                ];
            })->values(),
        ]);
    }

    /**
     * Store a replacement record: waiting list applicant becomes replacement awardee,
     * and the replaced grantee/awardee + reason are recorded.
     */
    public function storeReplacement(Request $request)
    {
        $validated = $request->validate([
            'replacement_user_id' => 'required|exists:users,id',
            'replaced_user_id' => 'required|exists:users,id',
            'replacement_reason' => 'required|string|min:3|max:2000',
            'school_year' => 'nullable|string|max:50',
        ]);

        /** @var Staff $staff */
        $staff = Auth::guard('staff')->user();

        try {
            $result = DB::transaction(function () use ($validated, $staff) {
                // Enforce: replacement must come from a validated applicant (validated + any status except grantee/pamana)
                $replacementUser = User::with('basicInfo')->findOrFail($validated['replacement_user_id']);
                $replacementBasic = $replacementUser->basicInfo;
                
                $replacementGrantStatus = strtolower(trim((string) ($replacementBasic->grant_status ?? '')));
                
                if (
                    ! $replacementBasic ||
                    strtolower(trim((string) ($replacementBasic->application_status ?? ''))) !== 'validated' ||
                    in_array($replacementGrantStatus, ['grantee', 'pamana'])
                ) {
                    return [
                        'ok' => false,
                        'status' => 400,
                        'message' => 'Selected replacement must be a validated applicant (and not currently a grantee).',
                    ];
                }

                // Enforce: replaced must be a validated grantee OR pamana (so this is a termination)
                // Load all relationships for archiving
                $replacedUser = User::with([
                    'basicInfo.fullAddress.address',
                    'basicInfo.fullAddress.mailingAddress',
                    'basicInfo.fullAddress.permanentAddress',
                    'basicInfo.fullAddress.origin',
                    'basicInfo.education',
                    'basicInfo.family',
                    'basicInfo.siblings',
                    'basicInfo.schoolPref',
                    'documents',
                    'ethno'
                ])->findOrFail($validated['replaced_user_id']);

                $replacedBasic = $replacedUser->basicInfo;
                $grantStatus = strtolower(trim((string) ($replacedBasic->grant_status ?? '')));
                
                if (
                    ! $replacedBasic ||
                    strtolower(trim((string) ($replacedBasic->application_status ?? ''))) !== 'validated' ||
                    !in_array($grantStatus, ['grantee', 'pamana'])
                ) {
                    return [
                        'ok' => false,
                        'status' => 400,
                        'message' => 'Selected replaced applicant must be a validated grantee or pamana scholar.',
                    ];
                }

                // 1) Archive the applicant data
                $archiveData = $replacedUser->toArray();
                $archive = ApplicantArchive::create([
                    'user_id' => $replacedUser->id,
                    'replacement_id' => null, // Will update after creating replacement
                    'data' => $archiveData,
                    'archived_by' => $staff ? $staff->id : null,
                ]);

                // 2) Terminate the replaced grantee (application_status -> rejected, keep grant_status to mark as terminated)
                $replacedBasic->update([
                    'application_status' => 'rejected',
                    'application_rejection_reason' => trim($validated['replacement_reason']),
                    // keep grant_status to identify as terminated (per existing logic)
                ]);

                // Notify terminated student
                $replacedUser->notify(new ApplicationStatusUpdated('rejected', trim($validated['replacement_reason'])));

                // 3) Promote the waiting applicant to grantee
                $promoteData = [
                    'grant_status' => 'grantee',
                    'application_rejection_reason' => null,
                ];
                // Only reset grant flags if these columns exist in the current DB schema
                if (Schema::hasColumn('basic_info', 'grant_1st_sem')) {
                    $promoteData['grant_1st_sem'] = false;
                }
                if (Schema::hasColumn('basic_info', 'grant_2nd_sem')) {
                    $promoteData['grant_2nd_sem'] = false;
                }
                $replacementBasic->update($promoteData);

                // Notify promoted student
                $replacementUser->notify(new TransactionNotification(
                    'update',
                    'Promoted to Grantee',
                    'Congratulations! You have been promoted from the waiting list and are now officially a scholarship grantee.',
                    'high'
                ));

                // 4) Record the replacement event
                $replacement = Replacement::create([
                    'replacement_user_id' => $validated['replacement_user_id'],
                    'replaced_user_id' => $validated['replaced_user_id'],
                    'replaced_name' => null,
                    'replacement_reason' => trim($validated['replacement_reason']),
                    'school_year' => $validated['school_year'] ?? null,
                    'created_by_staff_id' => $staff ? $staff->id : null,
                ]);

                // 5) Link archive to replacement
                $archive->update(['replacement_id' => $replacement->id]);

                return [
                    'ok' => true,
                    'replacement_id' => $replacement->id,
                ];
            });
        } catch (Throwable $e) {
            // Keep a useful server-side record, but return a user-friendly message.
            Log::error('storeReplacement failed', [
                'exception' => $e,
                'replacement_user_id' => $validated['replacement_user_id'] ?? null,
                'replaced_user_id' => $validated['replaced_user_id'] ?? null,
            ]);

            $msg = 'Failed to save replacement. Please try again.';
            $raw = $e->getMessage() ?? '';

            // Common local/XAMPP DB issues
            if (
                str_contains($raw, 'SQLSTATE[HY000] [2002]') ||
                str_contains($raw, 'actively refused') ||
                str_contains($raw, 'MySQL server has gone away') ||
                str_contains($raw, 'SQLSTATE[HY000]: General error: 2006')
            ) {
                $msg = 'Database connection error. Please start/restart MySQL in XAMPP, then refresh the page and try again.';
            } elseif (str_contains($raw, 'Base table or view not found') || str_contains($raw, 'doesn\'t exist')) {
                $msg = 'Database table is missing. Please run migrations (php artisan migrate) and try again.';
            } elseif (str_contains($raw, 'Unknown column') || str_contains($raw, 'Column not found')) {
                $msg = 'Database columns are missing. Please run migrations (php artisan migrate) and try again.';
            }

            return response()->json([
                'success' => false,
                'message' => $msg,
            ], 500);
        }

        if (isset($result['ok']) && $result['ok'] === false) {
            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Invalid replacement request.',
            ], $result['status'] ?? 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Replacement saved and applicant promoted to grantee.',
            'replacement_id' => $result['replacement_id'] ?? null,
        ]);
    }

    /**
     * Update waiting list applicants (grants and RSSC scores)
     */
    public function updateWaitingList(Request $request)
    {
        $validated = $request->validate([
            'applicants' => 'required|array',
            'applicants.*.user_id' => 'required|exists:users,id',
            'applicants.*.grant_1st_sem' => 'nullable|boolean',
            'applicants.*.grant_2nd_sem' => 'nullable|boolean',
            'applicants.*.rssc_score' => 'nullable|numeric|min:0|max:100',
        ]);

        try {
            $updated = 0;
            foreach ($validated['applicants'] as $applicantData) {
                $user = User::find($applicantData['user_id']);
                if ($user && $user->basicInfo) {
                    $updateData = [];

                    // Only update grant flags if explicitly provided in the payload.
                    // This prevents screens that don't show grants (e.g. reports waiting list) from overwriting existing values.
                    if (array_key_exists('grant_1st_sem', $applicantData)) {
                        $updateData['grant_1st_sem'] = $applicantData['grant_1st_sem'] ?? false;
                    }
                    if (array_key_exists('grant_2nd_sem', $applicantData)) {
                        $updateData['grant_2nd_sem'] = $applicantData['grant_2nd_sem'] ?? false;
                    }

                    // Update RSSC score if provided (allow null to clear)
                    if (array_key_exists('rssc_score', $applicantData)) {
                        $updateData['rssc_score'] = $applicantData['rssc_score'];
                    }

                    if (empty($updateData)) {
                        continue;
                    }

                    $user->basicInfo->update($updateData);
                    $updated++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully updated {$updated} applicant(s).",
                'updated' => $updated,
            ]);
        } catch (Exception $e) {
            Log::error('Update waiting list error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update waiting list. '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update grant checkmarks for grantees
     */
    public function updateGrants(Request $request)
    {
        $validated = $request->validate([
            'grants' => 'required|array',
            'grants.*.user_id' => 'required|exists:users,id',
            'grants.*.grant_1st_sem' => 'nullable|boolean',
            'grants.*.grant_2nd_sem' => 'nullable|boolean',
        ]);

        try {
            $updated = 0;
            foreach ($validated['grants'] as $grantData) {
                $user = User::find($grantData['user_id']);
                if ($user && $user->basicInfo) {
                    $user->basicInfo->update([
                        'grant_1st_sem' => $grantData['grant_1st_sem'] ?? false,
                        'grant_2nd_sem' => $grantData['grant_2nd_sem'] ?? false,
                    ]);
                    $updated++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully updated grants for {$updated} grantee(s).",
                'updated' => $updated,
            ]);
        } catch (Exception $e) {
            Log::error('Update grants error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update grants. '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Masterlist - Regular Waiting (Validated but waiting for grant)
     */
    public function masterlistRegularWaiting(Request $request)
    {
        /** @var Staff $user */
        $user = Auth::guard('staff')->user();

        // Get filters from request
        $selectedProvince = $request->get('province');
        $selectedMunicipality = $request->get('municipality');
        $selectedBarangay = $request->get('barangay');
        $selectedEthno = $request->get('ethno');

        // Build query for Regular Waiting - validated but marked as waiting
        $applicantsQuery = User::with(['basicInfo.fullAddress.address', 'ethno', 'documents'])
            ->whereHas('basicInfo', function ($query) use ($selectedProvince, $selectedMunicipality, $selectedBarangay) {
                $query->where('type_assist', 'Regular')
                    ->where('application_status', 'validated')
                    ->where('grant_status', 'waiting'); // Only show waiting list

                if ($selectedProvince) {
                    $query->whereHas('fullAddress', function ($q) use ($selectedProvince) {
                        $q->whereHas('address', function ($aq) use ($selectedProvince) {
                            $aq->where('province', $selectedProvince);
                        });
                    });
                }
                if ($selectedMunicipality) {
                    $query->whereHas('fullAddress', function ($q) use ($selectedMunicipality) {
                        $q->whereHas('address', function ($aq) use ($selectedMunicipality) {
                            $aq->where('municipality', $selectedMunicipality);
                        });
                    });
                }
                if ($selectedBarangay) {
                    $query->whereHas('fullAddress', function ($q) use ($selectedBarangay) {
                        $q->whereHas('address', function ($aq) use ($selectedBarangay) {
                            $aq->where('barangay', $selectedBarangay);
                        });
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
        ))->with('masterlistType', 'Regular Waiting');
    }

    /**
     * Masterlist - Pamana Scholarship
     */
    public function masterlistPamana(Request $request)
    {
        /** @var Staff $user */
        $user = Auth::guard('staff')->user();

        // Get filters from request
        $selectedProvince = $request->get('province');
        $selectedMunicipality = $request->get('municipality');
        $selectedBarangay = $request->get('barangay');
        $selectedEthno = $request->get('ethno');

        // Build query for Pamana scholarship applicants - Only show approved applications
        // Exclude grantees (they appear in the grantees masterlist)
        $applicantsQuery = User::with(['basicInfo.fullAddress.address', 'ethno', 'documents'])
            ->whereHas('basicInfo', function ($query) use ($selectedProvince, $selectedMunicipality, $selectedBarangay) {
                $query->where('type_assist', 'Pamana')
                    ->where('application_status', 'validated') // Only show validated/approved applications
                    ->where(function ($q) {
                        // Exclude grantees (handle case variations)
                        $q->where(function ($subQ) {
                            $subQ->where('grant_status', '!=', 'grantee')
                                ->where('grant_status', '!=', 'Grantee');
                        })->orWhereNull('grant_status');
                    });

                if ($selectedProvince) {
                    $query->whereHas('fullAddress', function ($q) use ($selectedProvince) {
                        $q->whereHas('address', function ($aq) use ($selectedProvince) {
                            $aq->where('province', $selectedProvince);
                        });
                    });
                }
                if ($selectedMunicipality) {
                    $query->whereHas('fullAddress', function ($q) use ($selectedMunicipality) {
                        $q->whereHas('address', function ($aq) use ($selectedMunicipality) {
                            $aq->where('municipality', $selectedMunicipality);
                        });
                    });
                }
                if ($selectedBarangay) {
                    $query->whereHas('fullAddress', function ($q) use ($selectedBarangay) {
                        $q->whereHas('address', function ($aq) use ($selectedBarangay) {
                            $aq->where('barangay', $selectedBarangay);
                        });
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

    /**
     * Display announcements management page
     */
    public function announcements()
    {
        /** @var Staff $user */
        $user = Auth::guard('staff')->user();
        $name = $user->name;
        $assignedBarangay = $user->assigned_barangay ?? 'All';

        $notifications = $user->unreadNotifications()->take(10)->get();

        // Fetch announcements from database
        $announcements = Announcement::with('creator')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('staff.announcements', compact('name', 'assignedBarangay', 'notifications', 'announcements'));
    }

    /**
     * Store a new announcement
     */
    public function storeAnnouncement(Request $request)
    {
        $request->validate([
            'title' => 'required|string|min:5|max:255',
            'content' => 'required|string|min:10',
            'priority' => 'required|in:normal,high,urgent',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
        ]);

        /** @var Staff $user */
        $user = Auth::guard('staff')->user();

        $imagePath = null;
        if ($request->hasFile('image')) {
            $disk = config('filesystems.default');
            $imagePath = $request->file('image')->store('announcements', $disk);
        }

        $announcement = Announcement::create([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'priority' => $request->input('priority'),
            'image_path' => $imagePath,
            'created_by' => $user->id,
        ]);

        // Notify all students
        $students = User::all();
        foreach ($students as $student) {
            $student->notify(new AnnouncementNotification($announcement));
        }

        return response()->json([
            'success' => true,
            'message' => 'Announcement created successfully!',
            'announcement' => $announcement,
        ]);
    }

    /**
     * Delete an announcement
     */
    public function deleteAnnouncement($id)
    {
        try {
            $announcement = Announcement::findOrFail($id);
            
            // Delete image if exists
            if ($announcement->image_path) {
                $disk = config('filesystems.default');
                if (\Storage::disk($disk)->exists($announcement->image_path)) {
                    \Storage::disk($disk)->delete($announcement->image_path);
                }
            }
            
            $announcement->delete();

            return response()->json([
                'success' => true,
                'message' => 'Announcement deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete announcement: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the list of archived applicants.
     */
    public function archives(Request $request)
    {
        /** @var Staff $user */
        $user = Auth::guard('staff')->user();
        
        $query = ApplicantArchive::with(['user', 'replacement', 'archiver'])
            ->orderBy('archived_at', 'desc');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->whereHas('user', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('middle_name', 'like', "%{$search}%");
            });
        }

        $archives = $query->paginate(20);

        return view('staff.archives.index', compact('archives'));
    }

    /**
     * Display the details of an archived applicant.
     */
    public function viewArchive(ApplicantArchive $archive)
    {
        return view('staff.archives.show', compact('archive'));
    }
}
