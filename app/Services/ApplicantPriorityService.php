<?php

namespace App\Services;

use App\Models\User;
use App\Models\BasicInfo;
use Carbon\Carbon;

class ApplicantPriorityService
{
    /**
     * Weight allocation (must total 1.0 / 100%)
     */
    private array $priorityWeights = [
        'ip' => 0.30,          // 30%
        'course' => 0.25,      // 25%
        'tribal' => 0.20,      // 20%
        'income_tax' => 0.15,  // 15%
        'academic_performance' => 0.05,  // 5%
        'other_requirements' => 0.05,  // 5%
    ];
    /**
     * Priority Indigenous Groups (2nd priority tier)
     */
    private $priorityEthnoGroups = [
        "b'laan", 'bagobo', 'kalagan', 'kaulo'
    ];

    /**
     * Priority courses (3rd priority tier)
     * Based on preferred courses declared in the scholarship form
     * (registration-time course value is only used as a fallback)
     */
    private $priorityCourses = [
        'Agriculture',
        'Aqua-Culture and Fisheries',
        'Anthropology',
        'Business Administration (Accounting, Marketing, Management, Economics, Entrepreneurship)',
        'Civil Engineering',
        'Community Development',
        'Criminology',
        'Education',
        'Foreign Service',
        'Forestry and Environment Studies (Forestry, Environmental Science, Agro-Forestry)',
        'Geodetic Engineering',
        'Geology',
        'Law',
        'Medicine and Allied Health Sciences (Nursing, Midwifery, Medical Technology, etc.)',
        'Mechanical Engineering',
        'Mining Engineering',
        'Social Sciences (AB courses)',
        'Social Work',
        ];

    private $excludedCourses = [
        'BS Information Technology',
        'BS Computer Science',
        'BS Accountancy',
        'BS Nursing',
        'BS Education',
        'BA Political Science',
    ];

    /**
     * Check if applicant is in priority IP group
     */
    private function isPriorityEthno(?string $ethnicity): bool
    {
        if (!$ethnicity) {
            return false;
        }
        $norm = strtolower(trim($ethnicity));
        return in_array($norm, $this->priorityEthnoGroups, true);
    }

    /**
     * Calculate IP Group rubric score (0-10 scale)
     * Based on NCIP IP Group Priority Rubric (Rank 1 - Highest Priority)
     * 
     * Scoring Rubric:
     * - 10/10: Validated documentation (tribal cert + endorsement + birth cert all approved)
     * - 8/10: Missing 1 supporting document (tribal cert approved OR 2 docs approved)
     * - 6/10: Questionable/incomplete but partially verifiable
     * - 4/10: Self-declared only (no documents submitted)
     * - 0/10: No IP affiliation
     */
    private function calculateIpRubricScore(User $user): float
    {
        // No IP affiliation
        if (!$user->ethno || !$user->ethno->ethnicity) {
            return 0;
        }

        $documents = $user->documents ?? collect();
        
        // IP verification documents:
        // 1. tribal_certificate - Certificate of Tribal Membership/Confirmation (PRIMARY)
        // 2. endorsement - Endorsement of the IPS/IP Traditional Leaders
        // 3. birth_certificate - Can show birthplace/indigenous origin
        $ipDocuments = [
            'tribal_certificate' => $documents->where('type', 'tribal_certificate')->first(),
            'endorsement' => $documents->where('type', 'endorsement')->first(),
            'birth_certificate' => $documents->where('type', 'birth_certificate')->first(),
        ];

        // Count document statuses
        $approvedDocs = 0;
        $pendingDocs = 0;
        $rejectedDocs = 0;
        $totalSubmitted = 0;

        foreach ($ipDocuments as $docType => $document) {
            if ($document) {
                $totalSubmitted++;
                if ($document->status === 'approved') {
                    $approvedDocs++;
                } elseif ($document->status === 'pending') {
                    $pendingDocs++;
                } elseif ($document->status === 'rejected') {
                    $rejectedDocs++;
                }
            }
        }

        // Apply rubric scoring (0-10 scale)
        
        // Rubric 10/10: All 3 key IP documents approved, OR tribal cert + endorsement approved
        if ($approvedDocs >= 3 || 
            ($ipDocuments['tribal_certificate']?->status === 'approved' && 
             $ipDocuments['endorsement']?->status === 'approved')) {
            return 10;
        }
        
        // Rubric 8/10: Missing 1 supporting document - has tribal cert approved OR has 2 approved docs
        if ($approvedDocs === 2 || 
            ($approvedDocs === 1 && $ipDocuments['tribal_certificate']?->status === 'approved')) {
            return 8;
        }

        // Rubric 6/10: Documentation questionable or incomplete but partially verifiable
        if ($approvedDocs === 1 || 
            ($totalSubmitted >= 2 && $pendingDocs >= 1) ||
            ($rejectedDocs > 0 && $totalSubmitted >= 1)) {
            return 6;
        }

        // Rubric 4/10: Claims IP group but no supporting documents (self-declared only)
        if ($user->ethno && $user->ethno->ethnicity && $totalSubmitted === 0) {
            return 4;
        }

        // Rubric 0/10: No affiliation
        return 0;
    }

    /**
     * Get applicant's course (from registration or school preference)
     */
    private function getApplicantCourse(User $user): ?string
    {
        // Prefer the scholarship form's preferred courses (first, then second)
        $basicInfo = $user->basicInfo;
        $schoolPref = $basicInfo?->schoolPref;

        if ($schoolPref) {
            if ($schoolPref->degree) {
                return trim($schoolPref->degree);
            }

            if ($schoolPref->degree2) {
                return trim($schoolPref->degree2);
            }
        }

        // Fall back to the course captured during account creation
        if ($user->course) {
            $registeredCourse = trim($user->course);
            return $registeredCourse !== '' ? $registeredCourse : null;
        }

        return null;
    }

    /**
     * Normalize course name to match priority courses
     */
    private function normalizeCourseName(?string $courseName): ?string
    {
        if (!$courseName) {
            return null;
        }

        $courseName = trim($courseName);

        // Check for exact match
        if (in_array($courseName, $this->priorityCourses)) {
            return $courseName;
        }

        // Check for partial match
        foreach ($this->priorityCourses as $priorityCourse) {
            if (stripos($courseName, $priorityCourse) !== false || stripos($priorityCourse, $courseName) !== false) {
                return $priorityCourse;
            }
        }

        // Return as-is if no match (will be categorized as "Other")
        return $courseName;
    }

    /**
     * Check if course is a priority course
     */
    private function isPriorityCourse(?string $courseName): bool
    {
        if (!$courseName) {
            return false;
        }

        $normalized = $this->normalizeCourseName($courseName);
        if (!$normalized) {
            return false;
        }

        if ($this->isExcludedCourse($normalized)) {
            return false;
        }

        return in_array($normalized, $this->priorityCourses, true);
    }

    private function isExcludedCourse(string $courseName): bool
    {
        return in_array(trim($courseName), $this->excludedCourses, true);
    }

    /**
     * Check if applicant has approved tribal certificate
     */
    private function hasApprovedTribalCertificate(User $applicant): bool
    {
        return $applicant->documents()
            ->where('type', 'tribal_certificate')
            ->where('status', 'approved')
            ->exists();
    }

    /**
     * Check if applicant has approved income tax document
     */
    private function hasApprovedIncomeTax(User $applicant): bool
    {
        return $applicant->documents()
            ->where('type', 'income_document')
            ->where('status', 'approved')
            ->exists();
    }

    /**
     * Check if applicant has approved grades document (Academic Performance)
     */
    private function hasApprovedGrades(User $applicant): bool
    {
        return $applicant->documents()
            ->where('type', 'grades')
            ->where('status', 'approved')
            ->exists();
    }

    /**
     * Check if applicant has all other required documents approved (Birth Certificate, Endorsement, Good Moral)
     */
    private function hasAllOtherRequirements(User $applicant): bool
    {
        $otherRequiredTypes = ['birth_certificate', 'endorsement', 'good_moral'];
        
        $approvedDocs = $applicant->documents()
            ->whereIn('type', $otherRequiredTypes)
            ->where('status', 'approved')
            ->get();
        
        // Check if all three documents are approved
        $hasBirthCert = $approvedDocs->where('type', 'birth_certificate')->isNotEmpty();
        $hasEndorsement = $approvedDocs->where('type', 'endorsement')->isNotEmpty();
        $hasGoodMoral = $approvedDocs->where('type', 'good_moral')->isNotEmpty();
        
        return $hasBirthCert && $hasEndorsement && $hasGoodMoral;
    }

    /**
     * Get prioritized applicants based on: IP Group (1st) → Course (2nd) → Tribal Certificate (3rd) → Income Tax (4th) → Academic Performance (5th) → FCFS (Tiebreaker)
     */
    public function getPrioritizedApplicants(): array
    {
        // Get all applicants who have submitted applications
        $applicants = User::with([
            'basicInfo',
            'ethno',
            'basicInfo.schoolPref',
            'documents'
        ])
            ->whereHas('basicInfo', function($query) {
                $query->whereNotNull('type_assist');
            })
            ->get();

        $prioritizedApplicants = [];

        foreach ($applicants as $applicant) {
            $basicInfo = $applicant->basicInfo;
            if (!$basicInfo) continue;

            // Get application submission time (when basicInfo was created/updated with type_assist)
            $applicationSubmittedAt = $basicInfo->updated_at ?? $basicInfo->created_at ?? now();
            if (!$applicationSubmittedAt) {
                $applicationSubmittedAt = now();
            }

            // Get IP group and calculate rubric score (0-10 scale)
            $ethnicity = optional($applicant->ethno)->ethnicity ?? null;
            $isPriorityEthno = $this->isPriorityEthno($ethnicity);
            $ipRubricScore = $this->calculateIpRubricScore($applicant); // 0-10 scale

            // Check for approved tribal certificate (Rank 3)
            $hasApprovedTribalCert = $this->hasApprovedTribalCertificate($applicant);

            // Check for approved income tax document (Rank 4)
            $hasApprovedIncomeTax = $this->hasApprovedIncomeTax($applicant);

            // Check for approved grades document (Rank 5 - Academic Performance)
            $hasApprovedGrades = $this->hasApprovedGrades($applicant);

            // Check for all other required documents (Rank 6 - Other Requirements)
            $hasAllOtherRequirements = $this->hasAllOtherRequirements($applicant);

            // Get course
            $courseName = $this->getApplicantCourse($applicant);
            $normalizedCourse = $this->normalizeCourseName($courseName);
            $isPriorityCourse = $this->isPriorityCourse($courseName);

            $priorityScore = $this->calculatePriorityScore(
                $ipRubricScore, // Now passing rubric score (0-10) instead of boolean
                $isPriorityCourse,
                $hasApprovedTribalCert,
                $hasApprovedIncomeTax,
                $hasApprovedGrades,
                $hasAllOtherRequirements
            );

            $prioritizedApplicants[] = [
                'user_id' => $applicant->id,
                'user' => $applicant,
                'application_submitted_at' => $applicationSubmittedAt,
                'ethnicity' => $ethnicity,
                'is_priority_ethno' => $isPriorityEthno,
                'ip_rubric_score' => $ipRubricScore, // 0-10 scale rubric score
                'has_approved_tribal_cert' => $hasApprovedTribalCert,
                'has_approved_income_tax' => $hasApprovedIncomeTax,
                'has_approved_grades' => $hasApprovedGrades,
                'has_all_other_requirements' => $hasAllOtherRequirements,
                'course' => $courseName,
                'normalized_course' => $normalizedCourse ?? 'Other',
                'is_priority_course' => $isPriorityCourse,
                'priority_rank' => null, // Will be assigned after sorting
                'priority_score' => $priorityScore,
            ];
        }

        // Sort applicants by weighted score first, then FCFS as tie breaker
        usort($prioritizedApplicants, function($a, $b) {
            // Weighted score (descending)
            $scoreComparison = $b['priority_score'] <=> $a['priority_score'];
            if ($scoreComparison !== 0) {
                return $scoreComparison;
            }

            // FCFS (earliest submission wins)
            $aTime = $a['application_submitted_at']->timestamp;
            $bTime = $b['application_submitted_at']->timestamp;
            if ($aTime !== $bTime) {
                return $aTime <=> $bTime;
            }

            // Final tiebreaker: user ID (stable sort)
            return $a['user_id'] <=> $b['user_id'];
        });

        // Assign priority ranks
        $rank = 1;
        foreach ($prioritizedApplicants as &$applicant) {
            $applicant['priority_rank'] = $rank;
            $applicant['days_since_submission'] = $applicant['application_submitted_at']->diffInDays(now());
            $applicant['hours_since_submission'] = $applicant['application_submitted_at']->diffInHours(now());
            $rank++;
        }

        return $prioritizedApplicants;
    }

    private function calculatePriorityScore(
        float $ipRubricScore, // Now accepts 0-10 rubric score instead of boolean
        bool $isPriorityCourse,
        bool $hasApprovedTribalCert,
        bool $hasApprovedIncomeTax,
        bool $hasApprovedGrades,
        bool $hasAllOtherRequirements
    ): float {
        $score = 0;

        // IP Group Priority (30% weight) - Using rubric score (0-10 scale)
        // Rubric score is normalized to 0-100, then weighted at 30%
        // Example: 10/10 rubric → 100 → 100 × 0.30 = 30 points
        //          8/10 rubric → 80 → 80 × 0.30 = 24 points
        //          6/10 rubric → 60 → 60 × 0.30 = 18 points
        //          4/10 rubric → 40 → 40 × 0.30 = 12 points
        //          0/10 rubric → 0 → 0 × 0.30 = 0 points
        $normalizedIpScore = $ipRubricScore * 10; // Convert 0-10 to 0-100
        $score += $normalizedIpScore * $this->priorityWeights['ip'];

        if ($isPriorityCourse) {
            $score += $this->priorityWeights['course'] * 100;
        }

        if ($hasApprovedTribalCert) {
            $score += $this->priorityWeights['tribal'] * 100;
        }

        if ($hasApprovedIncomeTax) {
            $score += $this->priorityWeights['income_tax'] * 100;
        }

        if ($hasApprovedGrades) {
            $score += $this->priorityWeights['academic_performance'] * 100;
        }

        if ($hasAllOtherRequirements) {
            $score += $this->priorityWeights['other_requirements'] * 100;
        }

        return round($score, 2);
    }

    /**
     * Get top priority applicants
     */
    public function getTopPriorityApplicants(int $limit = 20): array
    {
        $applicants = $this->getPrioritizedApplicants();
        return array_slice($applicants, 0, $limit);
    }

    /**
     * Get priority statistics
     */
    public function getPriorityStatistics(): array
    {
        $applicants = $this->getPrioritizedApplicants();

        $totalApplicants = count($applicants);
        $priorityEthnoCount = count(array_filter($applicants, function($a) {
            return $a['is_priority_ethno'];
        }));
        $tribalCertCount = count(array_filter($applicants, function($a) {
            return $a['has_approved_tribal_cert'];
        }));
        $incomeTaxCount = count(array_filter($applicants, function($a) {
            return $a['has_approved_income_tax'];
        }));
        $academicPerformanceCount = count(array_filter($applicants, function($a) {
            return $a['has_approved_grades'];
        }));
        $otherRequirementsCount = count(array_filter($applicants, function($a) {
            return $a['has_all_other_requirements'];
        }));
        $priorityCourseCount = count(array_filter($applicants, function($a) {
            return $a['is_priority_course'];
        }));

        $oldestApplication = !empty($applicants) ? $applicants[0] : null;
        $newestApplication = !empty($applicants) ? end($applicants) : null;

        return [
            'total_applicants' => $totalApplicants,
            'priority_ethno_count' => $priorityEthnoCount,
            'tribal_cert_count' => $tribalCertCount,
            'income_tax_count' => $incomeTaxCount,
            'academic_performance_count' => $academicPerformanceCount,
            'other_requirements_count' => $otherRequirementsCount,
            'priority_course_count' => $priorityCourseCount,
            'oldest_application' => $oldestApplication ? [
                'user_id' => $oldestApplication['user_id'],
                'name' => $oldestApplication['user']->first_name . ' ' . $oldestApplication['user']->last_name,
                'submitted_at' => $oldestApplication['application_submitted_at']->format('Y-m-d H:i:s'),
                'days_waiting' => $oldestApplication['days_since_submission'],
            ] : null,
            'newest_application' => $newestApplication ? [
                'user_id' => $newestApplication['user_id'],
                'name' => $newestApplication['user']->first_name . ' ' . $newestApplication['user']->last_name,
                'submitted_at' => $newestApplication['application_submitted_at']->format('Y-m-d H:i:s'),
            ] : null,
        ];
    }

    /**
     * Get applicants by priority level
     */
    public function getApplicantsByPriorityLevel(string $level = 'high'): array
    {
        $applicants = $this->getPrioritizedApplicants();

        switch ($level) {
            case 'high':
                // Top 20% of applicants
                $limit = max(1, (int)ceil(count($applicants) * 0.2));
                return array_slice($applicants, 0, $limit);
            case 'medium':
                // Next 30% of applicants
                $start = max(1, (int)ceil(count($applicants) * 0.2));
                $limit = (int)ceil(count($applicants) * 0.3);
                return array_slice($applicants, $start, $limit);
            case 'low':
                // Remaining applicants
                $start = max(1, (int)ceil(count($applicants) * 0.5));
                return array_slice($applicants, $start);
            default:
                return $applicants;
        }
    }
}

