<?php

namespace App\Services;

use App\Models\User;
use App\Models\BasicInfo;
use Carbon\Carbon;

class ApplicantPriorityService
{
    /**
     * Priority Indigenous Groups (2nd priority tier)
     */
    private $priorityEthnoGroups = [
        "b'laan", 'bagobo', 'kalagan', 'kaulo'
    ];

    /**
     * Priority courses (3rd priority tier)
     * Based on courses from registration form
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
     * Get applicant's course (from registration or school preference)
     */
    private function getApplicantCourse(User $user): ?string
    {
        // First, check user's registered course
        if ($user->course) {
            return trim($user->course);
        }

        // Then, check school preference
        $basicInfo = $user->basicInfo;
        if ($basicInfo && $basicInfo->schoolPref) {
            if ($basicInfo->schoolPref->degree) {
                return trim($basicInfo->schoolPref->degree);
            }
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
     * Get prioritized applicants based on: IP Group (1st) → Course (2nd) → Tribal Certificate (3rd) → FCFS (Tiebreaker)
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

            // Get IP group
            $ethnicity = optional($applicant->ethno)->ethnicity ?? null;
            $isPriorityEthno = $this->isPriorityEthno($ethnicity);

            // Check for approved tribal certificate (Rank 3)
            $hasApprovedTribalCert = $this->hasApprovedTribalCertificate($applicant);

            // Get course
            $courseName = $this->getApplicantCourse($applicant);
            $normalizedCourse = $this->normalizeCourseName($courseName);
            $isPriorityCourse = $this->isPriorityCourse($courseName);

            $prioritizedApplicants[] = [
                'user_id' => $applicant->id,
                'user' => $applicant,
                'application_submitted_at' => $applicationSubmittedAt,
                'ethnicity' => $ethnicity,
                'is_priority_ethno' => $isPriorityEthno,
                'has_approved_tribal_cert' => $hasApprovedTribalCert,
                'course' => $courseName,
                'normalized_course' => $normalizedCourse ?? 'Other',
                'is_priority_course' => $isPriorityCourse,
                'priority_rank' => null, // Will be assigned after sorting
            ];
        }

        // Sort applicants by priority:
        // 1. IP Group (priority IP groups first) - 1st Priority
        // 2. Course (priority courses first) - 2nd Priority
        // 3. Certificate of Tribal Membership (approved first) - 3rd Priority
        // 4. FCFS (earliest submission as tiebreaker)
        usort($prioritizedApplicants, function($a, $b) {
            // 1st Priority: IP Group (priority groups first)
            $aIsPriorityEthno = $a['is_priority_ethno'] ? 1 : 0;
            $bIsPriorityEthno = $b['is_priority_ethno'] ? 1 : 0;
            if ($aIsPriorityEthno != $bIsPriorityEthno) {
                return $bIsPriorityEthno <=> $aIsPriorityEthno; // Descending = priority first
            }

            // 2nd Priority: Course (priority courses first)
            $aIsPriorityCourse = $a['is_priority_course'] ? 1 : 0;
            $bIsPriorityCourse = $b['is_priority_course'] ? 1 : 0;
            if ($aIsPriorityCourse != $bIsPriorityCourse) {
                return $bIsPriorityCourse <=> $aIsPriorityCourse; // Descending = priority first
            }

            // 3rd Priority: Certificate of Tribal Membership (approved first)
            $aHasTribalCert = $a['has_approved_tribal_cert'] ? 1 : 0;
            $bHasTribalCert = $b['has_approved_tribal_cert'] ? 1 : 0;
            if ($aHasTribalCert != $bHasTribalCert) {
                return $bHasTribalCert <=> $aHasTribalCert; // Descending = approved first
            }

            // 4th Priority: FCFS (application submission time - earliest first) - Tiebreaker
            $aTime = $a['application_submitted_at']->timestamp;
            $bTime = $b['application_submitted_at']->timestamp;
            if ($aTime != $bTime) {
                return $aTime <=> $bTime; // Ascending = earliest first
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
        $priorityCourseCount = count(array_filter($applicants, function($a) {
            return $a['is_priority_course'];
        }));

        $oldestApplication = !empty($applicants) ? $applicants[0] : null;
        $newestApplication = !empty($applicants) ? end($applicants) : null;

        return [
            'total_applicants' => $totalApplicants,
            'priority_ethno_count' => $priorityEthnoCount,
            'tribal_cert_count' => $tribalCertCount,
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

