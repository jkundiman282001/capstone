<?php

namespace App\Services;

use App\Models\BasicInfo;
use App\Models\User;

class ApplicantPriorityService
{
    /**
     * Weight allocation (must total 1.0 / 100%)
     * Based on AHP (Analytical Hierarchy Process) principles
     */
    private array $priorityWeights = [
        'ip' => 0.20,                    // 20% - IP group rubric (with priority IP bonus baked in)
        'academic' => 0.30,              // 30% - GWA (from education.grade_ave or basic_info.gpa)
        'income_tax' => 0.30,            // 30% - ITR / Income Tax Return document approval
        'awards' => 0.10,                // 10% - Citations/Awards (from education.rank)
        'social_responsibility' => 0.10, // 10% - Social responsibility (from essays in school_pref)
    ];

    /**
     * AHP Pairwise Comparison Matrix (for future dynamic weight calculation)
     * Matrix represents relative importance: [row][column] = importance of row vs column
     * Scale: 1 = equal, 3 = moderate, 5 = strong, 7 = very strong, 9 = extreme
     */
    private array $pairwiseComparisonMatrix = [
        // Note: This matrix is illustrative and kept for future AHP work.
        'ip' => [
            'ip' => 1,
            'academic' => 0.5,
            'income_tax' => 0.5,
            'awards' => 2,
            'social_responsibility' => 2,
        ],
        'academic' => [
            'ip' => 2,
            'academic' => 1,
            'income_tax' => 1,
            'awards' => 3,
            'social_responsibility' => 3,
        ],
        'income_tax' => [
            'ip' => 2,
            'academic' => 1,
            'income_tax' => 1,
            'awards' => 3,
            'social_responsibility' => 3,
        ],
        'awards' => [
            'ip' => 0.5,
            'academic' => 0.33,
            'income_tax' => 0.33,
            'awards' => 1,
            'social_responsibility' => 1,
        ],
        'social_responsibility' => [
            'ip' => 0.5,
            'academic' => 0.33,
            'income_tax' => 0.33,
            'awards' => 1,
            'social_responsibility' => 1,
        ],
    ];

    /**
     * Random Index (RI) values for consistency checking (Saaty's scale)
     * Used for Consistency Ratio calculation
     */
    private array $randomIndex = [
        1 => 0.00,
        2 => 0.00,
        3 => 0.58,
        4 => 0.90,
        5 => 1.12,
        6 => 1.24,
        7 => 1.32,
        8 => 1.41,
        9 => 1.45,
        10 => 1.49,
    ];

    /**
     * Priority Indigenous Groups (2nd priority tier)
     */
    private $priorityEthnoGroups = [
        "b'laan", 'bagobo', 'kalagan', 'kaulo',
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
        if (! $ethnicity) {
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
     *
     * Priority IP Group Bonus: Applicants in priority IP groups (B'laan, Bagobo, Kalagan, Kaulo)
     * receive a +2 point bonus to ensure they rank higher than non-priority IP groups with
     * the same documentation quality.
     */
    private function calculateIpRubricScore(User $user, bool $isPriorityEthno = false): float
    {
        // No IP affiliation
        if (! $user->ethno || ! $user->ethno->ethnicity) {
            return 0;
        }

        $documents = $user->documents ?? collect();

        // IP verification documents:
        // 1. tribal_certificate - Certificate of Tribal Membership/Confirmation (PRIMARY)
        // 2. endorsement - Endorsement of the IPS/IP Traditional Leaders
        // 3. birth_certificate - Can show birthplace/indigenous origin
        $ipDocuments = [
            'tribal_certificate' => $documents->where('type', 'tribal_certificate')->sortByDesc('created_at')->first(),
            'endorsement' => $documents->where('type', 'endorsement')->sortByDesc('created_at')->first(),
            'birth_certificate' => $documents->where('type', 'birth_certificate')->sortByDesc('created_at')->first(),
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
        $baseScore = 0;

        // Rubric 10/10: All 3 key IP documents approved, OR tribal cert + endorsement approved
        if ($approvedDocs >= 3 ||
            ($ipDocuments['tribal_certificate']?->status === 'approved' &&
             $ipDocuments['endorsement']?->status === 'approved')) {
            $baseScore = 10;
        }
        // Rubric 8/10: Missing 1 supporting document - has tribal cert approved OR has 2 approved docs
        elseif ($approvedDocs === 2 ||
            ($approvedDocs === 1 && $ipDocuments['tribal_certificate']?->status === 'approved')) {
            $baseScore = 8;
        }
        // Rubric 6/10: Documentation questionable or incomplete but partially verifiable
        elseif ($approvedDocs === 1 ||
            ($totalSubmitted >= 2 && $pendingDocs >= 1) ||
            ($rejectedDocs > 0 && $totalSubmitted >= 1)) {
            $baseScore = 6;
        }
        // Rubric 4/10: Claims IP group but no supporting documents (self-declared only)
        elseif ($user->ethno && $user->ethno->ethnicity && $totalSubmitted === 0) {
            $baseScore = 4;
        }
        // Rubric 0/10: No affiliation
        else {
            $baseScore = 0;
        }

        // Apply priority IP group bonus: +2 points (allows up to 12 for priority groups)
        // This ensures priority IP groups rank higher than non-priority ones with same docs
        // We allow scores above 10 for priority groups, then normalize with max=12
        if ($isPriorityEthno && $baseScore > 0) {
            $baseScore = $baseScore + 2; // Don't cap at 10 - allow up to 12 for priority groups
        }

        return $baseScore;
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
        if (! $courseName) {
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
        if (! $courseName) {
            return false;
        }

        $normalized = $this->normalizeCourseName($courseName);
        if (! $normalized) {
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
     * Get related/relevant courses for each priority course
     * These courses are considered "mid-scale" (partially relevant)
     */
    private function getRelatedCourses(): array
    {
        return [
            'Agriculture' => ['Agricultural Engineering', 'Agribusiness', 'Agricultural Economics', 'Animal Science', 'Crop Science', 'Agricultural Technology'],
            'Aqua-Culture and Fisheries' => ['Marine Biology', 'Fisheries', 'Aquaculture', 'Marine Science', 'Oceanography'],
            'Anthropology' => ['Sociology', 'Cultural Studies', 'Ethnic Studies', 'Archaeology'],
            'Business Administration (Accounting, Marketing, Management, Economics, Entrepreneurship)' => ['Business Management', 'Marketing', 'Economics', 'Entrepreneurship', 'Finance', 'Human Resource Management', 'Operations Management'],
            'Civil Engineering' => ['Structural Engineering', 'Environmental Engineering', 'Construction Engineering', 'Transportation Engineering'],
            'Community Development' => ['Rural Development', 'Urban Planning', 'Public Administration', 'Development Studies'],
            'Criminology' => ['Criminal Justice', 'Forensic Science', 'Law Enforcement', 'Security Management'],
            'Education' => ['Elementary Education', 'Secondary Education', 'Special Education', 'Educational Administration', 'Curriculum Development'],
            'Foreign Service' => ['International Relations', 'Diplomatic Studies', 'International Studies', 'Public Administration'],
            'Forestry and Environment Studies (Forestry, Environmental Science, Agro-Forestry)' => ['Environmental Science', 'Forestry', 'Ecology', 'Conservation', 'Natural Resource Management', 'Environmental Management'],
            'Geodetic Engineering' => ['Surveying', 'Geomatics', 'Land Surveying', 'Geographic Information Systems'],
            'Geology' => ['Geological Engineering', 'Geophysics', 'Earth Science', 'Mining Engineering'],
            'Law' => ['Legal Studies', 'Jurisprudence', 'Constitutional Law'],
            'Medicine and Allied Health Sciences (Nursing, Midwifery, Medical Technology, etc.)' => ['Public Health', 'Health Sciences', 'Medical Laboratory Science', 'Radiologic Technology', 'Physical Therapy', 'Occupational Therapy', 'Pharmacy'],
            'Mechanical Engineering' => ['Industrial Engineering', 'Manufacturing Engineering', 'Automotive Engineering', 'Aerospace Engineering'],
            'Mining Engineering' => ['Geological Engineering', 'Mineral Processing', 'Mining Technology'],
            'Social Sciences (AB courses)' => ['Psychology', 'History', 'Philosophy', 'Literature', 'Communication Arts', 'Journalism'],
            'Social Work' => ['Human Services', 'Community Services', 'Social Welfare', 'Counseling'],
        ];
    }

    /**
     * Check if course is related/relevant to priority courses
     */
    private function isRelatedCourse(?string $courseName): bool
    {
        if (! $courseName) {
            return false;
        }

        $courseName = trim($courseName);

        // First check if it's already a priority course - if so, it's not "related", it's priority
        if ($this->isPriorityCourse($courseName)) {
            return false;
        }

        // Check if it's an excluded course - excluded courses are not related
        if ($this->isExcludedCourse($courseName)) {
            return false;
        }

        $relatedCourses = $this->getRelatedCourses();

        foreach ($relatedCourses as $priorityCourse => $related) {
            foreach ($related as $relatedCourse) {
                // Check if course name contains related course keywords or vice versa
                if (stripos($courseName, $relatedCourse) !== false ||
                    stripos($relatedCourse, $courseName) !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Calculate Course Priority rubric score (0-10 scale)
     *
     * Scoring Rubric:
     * - 10/10: Exact match with priority course
     * - 6/10: Course is related/relevant to priority courses (mid-scale)
     * - 0/10: Course is not priority and not related (low-scale)
     */
    private function calculateCourseRubricScore(?string $courseName): float
    {
        if (! $courseName) {
            return 0;
        }

        $courseName = trim($courseName);

        // Check if it's an excluded course (these get 0)
        if ($this->isExcludedCourse($courseName)) {
            return 0;
        }

        // Check if it's a priority course (high-scale: 10/10)
        if ($this->isPriorityCourse($courseName)) {
            return 10;
        }

        // Check if it's related to priority courses (mid-scale: 6/10)
        if ($this->isRelatedCourse($courseName)) {
            return 6;
        }

        // Not priority and not related (low-scale: 0/10)
        return 0;
    }

    /**
     * Check if applicant has approved tribal certificate
     */
    private function hasApprovedTribalCertificate(User $applicant): bool
    {
        $latest = $applicant->documents()
            ->where('type', 'tribal_certificate')
            ->orderByDesc('submitted_at')
            ->orderByDesc('created_at')
            ->first();
            
        return $latest && $latest->status === 'approved';
    }

    /**
     * Check if applicant has approved income tax document
     */
    private function hasApprovedIncomeTax(User $applicant): bool
    {
        $latest = $applicant->documents()
            ->where('type', 'income_document')
            ->orderByDesc('submitted_at')
            ->orderByDesc('created_at')
            ->first();
            
        return $latest && $latest->status === 'approved';
    }

    /**
     * Check if applicant has approved grades document (Academic Performance)
     */
    private function hasApprovedGrades(User $applicant): bool
    {
        $latest = $applicant->documents()
            ->where('type', 'grades')
            ->orderByDesc('submitted_at')
            ->orderByDesc('created_at')
            ->first();
            
        return $latest && $latest->status === 'approved';
    }

    /**
     * Check if applicant has all other required documents approved (Birth Certificate, Endorsement, Good Moral)
     */
    private function hasAllOtherRequirements(User $applicant): bool
    {
        $otherRequiredTypes = ['birth_certificate', 'endorsement', 'good_moral'];
        
        $allApproved = true;
        foreach ($otherRequiredTypes as $type) {
            $latest = $applicant->documents()
                ->where('type', $type)
                ->orderByDesc('submitted_at')
                ->orderByDesc('created_at')
                ->first();
                
            if (!$latest || $latest->status !== 'approved') {
                $allApproved = false;
                break;
            }
        }

        return $allApproved;
    }

    /**
     * Get prioritized applicants based on weighted scoring with FCFS (First Come First Serve) as tiebreaker
     *
     * Priority Order:
     * 1. Weighted Priority Score (IP Group 20%, GWA 30%, ITR 30%, Citations/Awards 10%, Social Responsibility 10%)
     * 2. FCFS Tiebreaker: When scores are equal, earlier submission time wins
     * 3. User ID: Final tiebreaker for stable sorting
     */
    public function getPrioritizedApplicants(): array
    {
        // Get all applicants who have submitted applications
        $applicants = User::with([
            'basicInfo',
            'ethno',
            'basicInfo.schoolPref',
            'basicInfo.education',
            'documents',
        ])
            ->whereHas('basicInfo', function ($query) {
                $query->whereNotNull('type_assist');
            })
            ->get();

        $prioritizedApplicants = [];

        foreach ($applicants as $applicant) {
            $basicInfo = $applicant->basicInfo;
            if (! $basicInfo) {
                continue;
            }

            // Get application submission time (when basicInfo was created/updated with type_assist)
            $applicationSubmittedAt = $basicInfo->updated_at ?? $basicInfo->created_at ?? now();
            if (! $applicationSubmittedAt) {
                $applicationSubmittedAt = now();
            }

            // Get IP group and calculate rubric score (0-10 scale)
            $ethnicity = optional($applicant->ethno)->ethnicity ?? null;
            $isPriorityEthno = $this->isPriorityEthno($ethnicity);
            $ipRubricScore = $this->calculateIpRubricScore($applicant, $isPriorityEthno); // 0-10 scale (includes priority bonus)

            // Check for approved ITR / income tax document
            $hasApprovedIncomeTax = $this->hasApprovedIncomeTax($applicant);

            // Academic score (GWA) rubric score (0-10 scale)
            $academicRubricScore = $this->calculateAcademicRubricScore($applicant);

            // Citations/Awards rubric score (0-10 scale), based on education.rank
            $awardsRubricScore = $this->calculateAwardsRubricScore($applicant);

            // Social Responsibility rubric score (0-10 scale), based on essays in school_pref
            $socialResponsibilityRubricScore = $this->calculateSocialResponsibilityRubricScore($applicant);

            $priorityScore = $this->calculatePriorityScore(
                $ipRubricScore,
                $academicRubricScore,
                $hasApprovedIncomeTax,
                $awardsRubricScore,
                $socialResponsibilityRubricScore
            );

            $prioritizedApplicants[] = [
                'user_id' => $applicant->id,
                'user' => $applicant,
                'application_submitted_at' => $applicationSubmittedAt,
                'ethnicity' => $ethnicity,
                'is_priority_ethno' => $isPriorityEthno,
                'ip_rubric_score' => $ipRubricScore, // 0-12 scale rubric score (priority bonus can push above 10)
                'has_approved_income_tax' => $hasApprovedIncomeTax,
                'academic_rubric_score' => $academicRubricScore, // 0-10
                'awards_rubric_score' => $awardsRubricScore, // 0-10
                'social_responsibility_rubric_score' => $socialResponsibilityRubricScore, // 0-10
                'priority_rank' => null, // Will be assigned after sorting
                'priority_score' => $priorityScore,
            ];
        }

        // Sort applicants by weighted score first, then FCFS (First Come First Serve) as tiebreaker
        usort($prioritizedApplicants, function ($a, $b) {
            // PRIMARY: Weighted priority score (descending - higher score = better rank)
            $scoreComparison = $b['priority_score'] <=> $a['priority_score'];
            if ($scoreComparison !== 0) {
                return $scoreComparison;
            }

            // TIEBREAKER: FCFS - First Come First Serve (earliest submission wins)
            // Earlier submission time = higher priority when scores are equal
            $aTime = $a['application_submitted_at']->timestamp ?? PHP_INT_MAX;
            $bTime = $b['application_submitted_at']->timestamp ?? PHP_INT_MAX;
            if ($aTime !== $bTime) {
                return $aTime <=> $bTime; // Ascending: earlier timestamp = smaller number = better rank
            }

            // FINAL TIEBREAKER: User ID (stable sort - ensures consistent ordering)
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
     * Calculate priority score using AHP (Analytical Hierarchy Process) methodology
     *
     * AHP Principles Applied:
     * 1. Normalization: All criteria scores normalized to 0-1 scale
     * 2. Weighted Sum: Each normalized score multiplied by its AHP-derived weight
     * 3. Consistency: Weights validated for consistency
     * 4. Hierarchical Structure: Criteria organized in priority hierarchy
     *
     * Priority IP Group Bonus: Applicants in priority IP groups (B'laan, Bagobo, Kalagan, Kaulo)
     * receive a bonus multiplier on their IP rubric score to reflect their higher priority status.
     *
     * @param  float  $ipRubricScore  IP Group rubric score (0-12 scale)
     * @param  float  $academicRubricScore  Academic rubric score (0-10 scale) derived from GWA
     * @param  bool  $hasApprovedIncomeTax  Whether income tax document is approved
     * @param  float  $awardsRubricScore  Citations/Awards rubric score (0-10 scale)
     * @param  float  $socialResponsibilityRubricScore  Social responsibility rubric score (0-10 scale)
     * @return float Final priority score (0-100 scale)
     */
    private function calculatePriorityScore(
        float $ipRubricScore,
        float $academicRubricScore,
        bool $hasApprovedIncomeTax,
        float $awardsRubricScore,
        float $socialResponsibilityRubricScore
    ): float {
        // Validate weights consistency (AHP principle)
        $this->validateWeightsConsistency();

        // Normalize rubric scores to 0-1 scale (AHP normalization)
        $normalizedIpScore = $this->normalizeScore($ipRubricScore, 0, 12);
        $normalizedAcademicScore = $this->normalizeScore($academicRubricScore, 0, 10);
        $normalizedAwardsScore = $this->normalizeScore($awardsRubricScore, 0, 10);
        $normalizedSocialScore = $this->normalizeScore($socialResponsibilityRubricScore, 0, 10);

        $normalizedScores = [
            'ip' => $normalizedIpScore,
            'academic' => $normalizedAcademicScore,
            'income_tax' => $hasApprovedIncomeTax ? 1.0 : 0.0, // Binary → 0-1
            'awards' => $normalizedAwardsScore,
            'social_responsibility' => $normalizedSocialScore,
        ];

        // Calculate weighted sum (AHP weighted aggregation)
        // Formula: Score = Σ (Normalized_Score_i × Weight_i) × 100
        $weightedSum = 0.0;
        foreach ($normalizedScores as $criterion => $normalizedScore) {
            $weightedSum += $normalizedScore * $this->priorityWeights[$criterion];
        }

        // Scale to 0-100 for readability
        $finalScore = $weightedSum * 100;

        return round($finalScore, 2);
    }

    /**
     * Calculate academic rubric score (0-10) from:
     * - basic_info.gpa (75 - 100 scale, higher is better)
     *
     * NOTE: Per current policy, we DO NOT derive academic score from education.grade_ave anymore.
     */
    private function calculateAcademicRubricScore(User $user): float
    {
        $basicInfo = $user->basicInfo;

        if (! $basicInfo || $basicInfo->gpa === null) {
            return 0.0;
        }

        // GWA scale: 100 (best) → 75 (worst), map to 0–10 where higher is better.
        $gwa = (float) $basicInfo->gpa;
        if ($gwa < 75 || $gwa > 100) {
            return 0.0;
        }

        // Normalize GWA to 0..1 then scale to 0..10
        // 100 → 1.0, 75 → 0.0
        $normalized = ($gwa - 75) / 25;

        return round(max(0.0, min(1.0, $normalized)) * 10, 2);
    }

    /**
     * Calculate citations/awards rubric score (0-10) based on education.rank.
     * Uses the best (max) award across all education records.
     */
    private function calculateAwardsRubricScore(User $user): float
    {
        $education = $user->basicInfo?->education ?? collect();
        $best = 0.0;

        foreach ($education as $record) {
            $best = max($best, $this->mapEducationRankToScore($record?->rank));
        }

        return round($best, 2);
    }

    private function mapEducationRankToScore(?string $rank): float
    {
        $value = strtolower(trim((string) $rank));
        if ($value === '') {
            return 0.0;
        }

        return match ($value) {
            'valedictorian' => 10.0,
            'salutatorian' => 9.5,
            'with highest honors' => 9.0,
            'with high honors' => 8.0,
            'with honors' => 7.0,
            "dean's lister", 'deans lister', 'dean’s lister' => 6.5,
            'top 10' => 6.0,
            'academic awardee' => 5.0,
            'none' => 0.0,
            default => 0.0,
        };
    }

    /**
     * Calculate social responsibility rubric score (0-10) based on essays:
     * - school_pref.ques_answer1 (contribution to IP community)
     * - school_pref.ques_answer2 (plans after graduation)
     *
     * This is a deterministic heuristic rubric (no AI calls) so results are stable.
     */
    private function calculateSocialResponsibilityRubricScore(User $user): float
    {
        $schoolPref = $user->basicInfo?->schoolPref;
        $text = trim((string) ($schoolPref?->ques_answer1 ?? '').' '.(string) ($schoolPref?->ques_answer2 ?? ''));

        if ($text === '') {
            return 0.0;
        }

        $lower = strtolower($text);
        $length = mb_strlen($text);
        $score = 0.0;

        // Length / completeness (max 4)
        if ($length >= 800) {
            $score += 4.0;
        } elseif ($length >= 500) {
            $score += 3.0;
        } elseif ($length >= 250) {
            $score += 2.0;
        } elseif ($length >= 120) {
            $score += 1.0;
        } else {
            $score += 0.5;
        }

        // Keyword coverage (max 5)
        $keywords = [
            // community / IP context
            'community', 'barangay', 'sitio', 'tribe', 'indigenous', 'ip', 'ncip', 'culture', 'tradition', 'ancestral',
            // action / service
            'volunteer', 'service', 'serve', 'help', 'support', 'organize', 'conduct', 'teach', 'mentor', 'train', 'lead', 'leadership',
            // impact areas
            'education', 'health', 'livelihood', 'environment', 'development', 'youth', 'scholarship',
            // planning/structure
            'program', 'project', 'initiative', 'activity',
        ];

        $hits = 0;
        foreach (array_unique($keywords) as $kw) {
            if (str_contains($lower, $kw)) {
                $hits++;
            }
        }
        $score += min(5.0, $hits * 0.5);

        // Bonus for explicit intention + concrete framing (max 1)
        if (
            preg_match('/\b(will|plan|intend|aim|goal)\b/i', $text) &&
            preg_match('/\b(community|barangay|tribe|ip)\b/i', $text) &&
            preg_match('/\b(program|project|initiative|activity)\b/i', $text)
        ) {
            $score += 1.0;
        }

        return round(min(10.0, $score), 2);
    }

    /**
     * Public helper: calculate and return a single applicant's priority breakdown.
     * Useful for student-facing views without duplicating the scoring logic.
     */
    public function calculateApplicantPriority(User $user): array
    {
        $user->loadMissing(['basicInfo.schoolPref', 'basicInfo.education', 'ethno', 'documents']);
        $basicInfo = $user->basicInfo;

        $applicationSubmittedAt = $basicInfo?->updated_at ?? $basicInfo?->created_at ?? now();
        $ethnicity = optional($user->ethno)->ethnicity ?? null;
        $isPriorityEthno = $this->isPriorityEthno($ethnicity);

        $ipRubricScore = $this->calculateIpRubricScore($user, $isPriorityEthno);
        $academicRubricScore = $this->calculateAcademicRubricScore($user);
        $awardsRubricScore = $this->calculateAwardsRubricScore($user);
        $socialRubricScore = $this->calculateSocialResponsibilityRubricScore($user);
        $hasApprovedIncomeTax = $this->hasApprovedIncomeTax($user);

        $priorityScore = $this->calculatePriorityScore(
            $ipRubricScore,
            $academicRubricScore,
            $hasApprovedIncomeTax,
            $awardsRubricScore,
            $socialRubricScore
        );

        return [
            'application_submitted_at' => $applicationSubmittedAt,
            'ethnicity' => $ethnicity,
            'is_priority_ethno' => $isPriorityEthno,
            'ip_rubric_score' => $ipRubricScore,
            'academic_rubric_score' => $academicRubricScore,
            'awards_rubric_score' => $awardsRubricScore,
            'social_responsibility_rubric_score' => $socialRubricScore,
            'has_approved_income_tax' => $hasApprovedIncomeTax,
            'priority_score' => $priorityScore,
        ];
    }

    /**
     * Normalize a score to 0-1 scale (AHP normalization method)
     *
     * @param  float  $value  The value to normalize
     * @param  float  $min  Minimum possible value
     * @param  float  $max  Maximum possible value
     * @return float Normalized value between 0 and 1
     */
    private function normalizeScore(float $value, float $min, float $max): float
    {
        if ($max == $min) {
            return 0.0; // Avoid division by zero
        }

        // Linear normalization: (value - min) / (max - min)
        $normalized = ($value - $min) / ($max - $min);

        // Ensure result is within [0, 1] bounds
        return max(0.0, min(1.0, $normalized));
    }

    /**
     * Validate weights consistency (AHP consistency check)
     * Ensures weights sum to 1.0 and checks for logical consistency
     *
     * @throws \RuntimeException If weights are inconsistent
     */
    private function validateWeightsConsistency(): void
    {
        $totalWeight = array_sum($this->priorityWeights);
        $tolerance = 0.01; // Allow small floating point errors

        if (abs($totalWeight - 1.0) > $tolerance) {
            throw new \RuntimeException(
                "Priority weights must sum to 1.0. Current sum: {$totalWeight}"
            );
        }

        // Check for negative weights
        foreach ($this->priorityWeights as $criterion => $weight) {
            if ($weight < 0) {
                throw new \RuntimeException(
                    "Weight for '{$criterion}' cannot be negative: {$weight}"
                );
            }
        }
    }

    /**
     * Calculate Consistency Ratio (CR) for AHP pairwise comparison matrix
     * CR = CI / RI, where CI = (λ_max - n) / (n - 1)
     * CR < 0.10 indicates acceptable consistency
     *
     * @return array Contains CR, CI, λ_max, and consistency status
     */
    public function calculateConsistencyRatio(): array
    {
        $n = count($this->priorityWeights);

        // Calculate weighted sum vector
        $weightedSumVector = [];
        foreach (array_keys($this->priorityWeights) as $criterion) {
            $sum = 0;
            foreach (array_keys($this->priorityWeights) as $otherCriterion) {
                $sum += $this->pairwiseComparisonMatrix[$criterion][$otherCriterion]
                     * $this->priorityWeights[$otherCriterion];
            }
            $weightedSumVector[$criterion] = $sum;
        }

        // Calculate λ_max (maximum eigenvalue)
        $lambdaMax = 0;
        foreach (array_keys($this->priorityWeights) as $criterion) {
            if ($this->priorityWeights[$criterion] > 0) {
                $lambdaMax += $weightedSumVector[$criterion] / $this->priorityWeights[$criterion];
            }
        }
        $lambdaMax = $lambdaMax / $n;

        // Calculate Consistency Index (CI)
        $ci = ($lambdaMax - $n) / ($n - 1);

        // Get Random Index (RI) for n criteria
        $ri = $this->randomIndex[$n] ?? 1.24; // Default for n=6

        // Calculate Consistency Ratio (CR)
        $cr = $ri > 0 ? ($ci / $ri) : 0;

        // Determine consistency status
        $isConsistent = $cr < 0.10;

        return [
            'consistency_ratio' => round($cr, 4),
            'consistency_index' => round($ci, 4),
            'lambda_max' => round($lambdaMax, 4),
            'random_index' => $ri,
            'is_consistent' => $isConsistent,
            'status' => $isConsistent ? 'Acceptable' : 'Inconsistent - Review weights',
        ];
    }

    /**
     * Get AHP-derived weights from pairwise comparison matrix
     * Uses eigenvector method to calculate weights
     *
     * @return array Normalized weights derived from pairwise comparisons
     */
    public function getAHPWeights(): array
    {
        $n = count($this->pairwiseComparisonMatrix);
        $weights = [];

        // Calculate geometric mean for each row
        foreach (array_keys($this->pairwiseComparisonMatrix) as $criterion) {
            $product = 1.0;
            foreach ($this->pairwiseComparisonMatrix[$criterion] as $value) {
                $product *= $value;
            }
            $weights[$criterion] = pow($product, 1.0 / $n);
        }

        // Normalize weights to sum to 1.0
        $sum = array_sum($weights);
        foreach ($weights as $criterion => $weight) {
            $weights[$criterion] = $weight / $sum;
        }

        return $weights;
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
        $priorityEthnoCount = count(array_filter($applicants, function ($a) {
            return $a['is_priority_ethno'];
        }));
        $incomeTaxCount = count(array_filter($applicants, function ($a) {
            return $a['has_approved_income_tax'];
        }));
        $academicCount = count(array_filter($applicants, function ($a) {
            return ($a['academic_rubric_score'] ?? 0) > 0;
        }));
        $awardsCount = count(array_filter($applicants, function ($a) {
            return ($a['awards_rubric_score'] ?? 0) > 0;
        }));
        $socialCount = count(array_filter($applicants, function ($a) {
            return ($a['social_responsibility_rubric_score'] ?? 0) > 0;
        }));

        $oldestApplication = ! empty($applicants) ? $applicants[0] : null;
        $newestApplication = ! empty($applicants) ? end($applicants) : null;

        // Calculate slots information
        $maxSlots = 120;
        $granteesCount = \App\Models\BasicInfo::where('application_status', 'validated')
            ->where(function ($q) {
                $q->where('grant_status', 'grantee')
                    ->orWhere('grant_status', 'Grantee');
            })
            ->count();
        $slotsLeft = max(0, $maxSlots - $granteesCount);

        return [
            'total_applicants' => $totalApplicants,
            'priority_ethno_count' => $priorityEthnoCount,
            'income_tax_count' => $incomeTaxCount,
            'academic_count' => $academicCount,
            'awards_count' => $awardsCount,
            'social_responsibility_count' => $socialCount,
            'slots_left' => $slotsLeft,
            'grantees_count' => $granteesCount,
            'max_slots' => $maxSlots,
            'oldest_application' => $oldestApplication ? [
                'user_id' => $oldestApplication['user_id'],
                'name' => $oldestApplication['user']->first_name.' '.$oldestApplication['user']->last_name,
                'submitted_at' => $oldestApplication['application_submitted_at']->format('Y-m-d H:i:s'),
                'days_waiting' => $oldestApplication['days_since_submission'],
            ] : null,
            'newest_application' => $newestApplication ? [
                'user_id' => $newestApplication['user_id'],
                'name' => $newestApplication['user']->first_name.' '.$newestApplication['user']->last_name,
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
                $limit = max(1, (int) ceil(count($applicants) * 0.2));

                return array_slice($applicants, 0, $limit);
            case 'medium':
                // Next 30% of applicants
                $start = max(1, (int) ceil(count($applicants) * 0.2));
                $limit = (int) ceil(count($applicants) * 0.3);

                return array_slice($applicants, $start, $limit);
            case 'low':
                // Remaining applicants
                $start = max(1, (int) ceil(count($applicants) * 0.5));

                return array_slice($applicants, $start);
            default:
                return $applicants;
        }
    }
}
