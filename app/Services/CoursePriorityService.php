<?php

namespace App\Services;

use App\Models\BasicInfo;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CoursePriorityService
{
    /**
     * Get prioritized courses based on multiple criteria
     */
    public function getPrioritizedCourses(): array
    {
        // Get all applicants with school preferences
        $applicants = User::with([
            'basicInfo.schoolPref', 
            'basicInfo.fullAddress.address'
        ])
            ->whereHas('basicInfo', function($query) {
                $query->whereNotNull('school_pref_id');
            })
            ->get();

        $courseData = [];

        foreach ($applicants as $applicant) {
            $schoolPref = $applicant->basicInfo->schoolPref ?? null;
            if (!$schoolPref) continue;

            // Score-based prioritization removed - using ApplicantPriorityService instead
            $totalScore = 0;
            $priorityRank = 9999;

            // Process first preference course
            if ($schoolPref->degree) {
                $courseName = trim($schoolPref->degree);
                if (!isset($courseData[$courseName])) {
                    $courseData[$courseName] = [
                        'course_name' => $courseName,
                        'first_preference_count' => 0,
                        'second_preference_count' => 0,
                        'total_applicants' => 0,
                        'high_priority_applicants' => 0,
                        'medium_priority_applicants' => 0,
                        'low_priority_applicants' => 0,
                        'average_score' => 0,
                        'total_score_sum' => 0,
                        'top_applicants' => [],
                        'schools' => [],
                        'provinces' => [],
                        'priority_score' => 0,
                    ];
                }

                $courseData[$courseName]['first_preference_count']++;
                $courseData[$courseName]['total_applicants']++;
                $courseData[$courseName]['total_score_sum'] += $totalScore;

                // Priority level counting removed - using ApplicantPriorityService instead
                $courseData[$courseName]['high_priority_applicants']++;

                // Track schools
                if ($schoolPref->address && !in_array($schoolPref->address, $courseData[$courseName]['schools'])) {
                    $courseData[$courseName]['schools'][] = $schoolPref->address;
                }

                // Track provinces
                $fullAddress = $applicant->basicInfo->fullAddress ?? null;
                $address = $fullAddress->address ?? null;
                $province = $address->province ?? 'Unknown';
                if ($province && $province !== 'Unknown' && !in_array($province, $courseData[$courseName]['provinces'])) {
                    $courseData[$courseName]['provinces'][] = $province;
                }
            }

            // Process second preference course
            if ($schoolPref->degree2) {
                $courseName = trim($schoolPref->degree2);
                if (!isset($courseData[$courseName])) {
                    $courseData[$courseName] = [
                        'course_name' => $courseName,
                        'first_preference_count' => 0,
                        'second_preference_count' => 0,
                        'total_applicants' => 0,
                        'high_priority_applicants' => 0,
                        'medium_priority_applicants' => 0,
                        'low_priority_applicants' => 0,
                        'average_score' => 0,
                        'total_score_sum' => 0,
                        'top_applicants' => [],
                        'schools' => [],
                        'provinces' => [],
                        'priority_score' => 0,
                    ];
                }

                $courseData[$courseName]['second_preference_count']++;
                $courseData[$courseName]['total_applicants']++;
                $courseData[$courseName]['total_score_sum'] += $totalScore;

                // Priority level counting removed - using ApplicantPriorityService instead
                $courseData[$courseName]['high_priority_applicants']++;

                // Track schools
                if ($schoolPref->address2 && !in_array($schoolPref->address2, $courseData[$courseName]['schools'])) {
                    $courseData[$courseName]['schools'][] = $schoolPref->address2;
                }

                // Track provinces
                $fullAddress = $applicant->basicInfo->fullAddress ?? null;
                $address = $fullAddress->address ?? null;
                $province = $address->province ?? 'Unknown';
                if ($province && $province !== 'Unknown' && !in_array($province, $courseData[$courseName]['provinces'])) {
                    $courseData[$courseName]['provinces'][] = $province;
                }
            }
        }

        // Calculate average scores and priority scores
        foreach ($courseData as $courseName => &$data) {
            if ($data['total_applicants'] > 0) {
                $data['average_score'] = round($data['total_score_sum'] / $data['total_applicants'], 2);
            }

            // Calculate priority score (weighted)
            // Factors: Total applicants (40%), High priority applicants (30%), Average score (20%), First preference count (10%)
            $priorityScore = (
                min($data['total_applicants'] / 10, 1) * 40 + // Normalize to max 10 applicants = 40 points
                min($data['high_priority_applicants'] / 5, 1) * 30 + // Normalize to max 5 high priority = 30 points
                ($data['average_score'] / 100) * 20 + // Average score as percentage = 20 points
                min($data['first_preference_count'] / 10, 1) * 10 // First preference count = 10 points
            );

            $data['priority_score'] = round($priorityScore, 2);

            // Sort top applicants by score
            usort($data['top_applicants'], function($a, $b) {
                return $b['score'] <=> $a['score'];
            });
            $data['top_applicants'] = array_slice($data['top_applicants'], 0, 5); // Top 5 only
        }

        // Sort courses by priority score (descending)
        uasort($courseData, function($a, $b) {
            if ($b['priority_score'] != $a['priority_score']) {
                return $b['priority_score'] <=> $a['priority_score'];
            }
            // Tie-breaker: total applicants
            return $b['total_applicants'] <=> $a['total_applicants'];
        });

        // Assign priority ranks
        $rank = 1;
        $prioritizedCourses = [];
        foreach ($courseData as $courseName => $data) {
            $data['priority_rank'] = $rank;
            $data['priority_level'] = $this->getPriorityLevel($data['priority_score']);
            $prioritizedCourses[] = $data;
            $rank++;
        }

        return $prioritizedCourses;
    }

    /**
     * Get priority level based on priority score
     */
    private function getPriorityLevel(float $score): string
    {
        if ($score >= 70) {
            return 'High Priority';
        } elseif ($score >= 50) {
            return 'Medium Priority';
        } elseif ($score >= 30) {
            return 'Low Priority';
        } else {
            return 'Very Low Priority';
        }
    }

    /**
     * Get course statistics
     */
    public function getCourseStatistics(): array
    {
        $courses = $this->getOverallCoursePrioritization();

        $totalCourses = count($courses);
        $totalApplicants = array_sum(array_column($courses, 'total_applicants'));
        $highPriorityCourses = count(array_filter($courses, function($course) {
            return $course['priority_score'] >= 70;
        }));
        $totalHighPriorityApplicants = array_sum(array_column($courses, 'high_priority_applicants'));

        // Get most popular course
        $mostPopular = !empty($courses) ? $courses[0] : null;

        // Get course with highest average score
        $highestScoreCourse = null;
        $maxAvgScore = 0;
        foreach ($courses as $course) {
            if ($course['average_score'] > $maxAvgScore) {
                $maxAvgScore = $course['average_score'];
                $highestScoreCourse = $course;
            }
        }

        return [
            'total_courses' => $totalCourses,
            'total_applicants' => $totalApplicants,
            'high_priority_courses' => $highPriorityCourses,
            'total_high_priority_applicants' => $totalHighPriorityApplicants,
            'most_popular_course' => $mostPopular ? [
                'name' => $mostPopular['course_name'],
                'applicants' => $mostPopular['total_applicants'],
                'priority_score' => $mostPopular['priority_score'],
            ] : null,
            'highest_score_course' => $highestScoreCourse ? [
                'name' => $highestScoreCourse['course_name'],
                'average_score' => $highestScoreCourse['average_score'],
                'applicants' => $highestScoreCourse['total_applicants'],
            ] : null,
        ];
    }

    /**
     * Get top priority courses
     */
    public function getTopPriorityCourses(int $limit = 10): array
    {
        $courses = $this->getPrioritizedCourses();
        return array_slice($courses, 0, $limit);
    }

    /**
     * Get courses by priority level
     */
    public function getCoursesByPriorityLevel(string $level = 'high'): array
    {
        $courses = $this->getPrioritizedCourses();
        
        $thresholds = [
            'high' => 70,
            'medium' => 50,
            'low' => 30,
            'very_low' => 0,
        ];

        $threshold = $thresholds[$level] ?? 70;
        
        if ($level === 'high') {
            return array_filter($courses, function($course) use ($threshold) {
                return $course['priority_score'] >= $threshold;
            });
        } elseif ($level === 'medium') {
            return array_filter($courses, function($course) use ($threshold) {
                return $course['priority_score'] >= $threshold && $course['priority_score'] < 70;
            });
        } elseif ($level === 'low') {
            return array_filter($courses, function($course) use ($threshold) {
                return $course['priority_score'] >= $threshold && $course['priority_score'] < 50;
            });
        } else {
            return array_filter($courses, function($course) use ($threshold) {
                return $course['priority_score'] < $threshold;
            });
        }
    }

    /**
     * Get overall course prioritization for dashboard
     * Combines courses from user registration and school preferences
     */
    public function getOverallCoursePrioritization(): array
    {
        // Define priority courses from registration form
        $priorityCourses = [
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
            'BS Information Technology',
            'BS Computer Science',
            'BS Accountancy',
            'BS Nursing',
            'BS Education',
            'BA Political Science',
        ];

        $excludedCourses = [
            'BS Information Technology',
            'BS Computer Science',
            'BS Accountancy',
            'BS Nursing',
            'BS Education',
            'BA Political Science',
        ];

        // Initialize course data and tracking arrays
        $courseData = [];
        $courseApplicants = []; // Track which applicants we've counted per course

        foreach ($priorityCourses as $course) {
            if (in_array($course, $excludedCourses, true)) {
                continue;
            }

            $this->initializeCourseBucket($courseData, $courseApplicants, $course);
        }

        // Capture preferred courses from scholarship applications (authoritative source)
        $applicantsWithPreferences = User::whereHas('basicInfo', function($query) {
                $query->whereHas('schoolPref');
            })
            ->with(['basicInfo.schoolPref'])
            ->get();

        foreach ($applicantsWithPreferences as $user) {
            $schoolPref = $user->basicInfo->schoolPref;

            $this->recordCourseDemand(
                $courseData,
                $courseApplicants,
                $priorityCourses,
                $excludedCourses,
                $user,
                $schoolPref->degree,
                'preference_first_count',
                'Preferred Course (1st)'
            );

            $this->recordCourseDemand(
                $courseData,
                $courseApplicants,
                $priorityCourses,
                $excludedCourses,
                $user,
                $schoolPref->degree2,
                'preference_second_count',
                'Preferred Course (2nd)'
            );
        }

        // Use the account-level course only when no scholarship preference exists yet
        $usersWithCourse = User::whereNotNull('course')
            ->where('course', '!=', '')
            ->with(['basicInfo.schoolPref'])
            ->get();

        foreach ($usersWithCourse as $user) {
            if ($user->basicInfo && $user->basicInfo->schoolPref) {
                continue;
            }

            $this->recordCourseDemand(
                $courseData,
                $courseApplicants,
                $priorityCourses,
                $excludedCourses,
                $user,
                $user->course,
                'registered_count',
                'Registered Course'
            );
        }

        // Calculate average scores and priority scores
        foreach ($courseData as $courseName => &$data) {
            if ($data['total_applicants'] > 0) {
                $data['average_score'] = round($data['total_score_sum'] / $data['total_applicants'], 2);
            }

            // Calculate priority score
            // Factors: Total applicants (40%), High priority applicants (30%), Average score (20%), Registered count (10%)
            $priorityScore = (
                min($data['total_applicants'] / 50, 1) * 40 + // Normalize to max 50 applicants
                min($data['high_priority_applicants'] / 10, 1) * 30 + // Normalize to max 10 high priority
                ($data['average_score'] / 100) * 20 + // Average score as percentage
                min($data['registered_count'] / 30, 1) * 10 // Registered count
            );

            $data['priority_score'] = round($priorityScore, 2);

            if (!empty($data['applicants'])) {
                usort($data['applicants'], function ($a, $b) {
                    return $b['score'] <=> $a['score'];
                });
                $data['applicants'] = array_slice($data['applicants'], 0, 10);
            }
        }

        // Remove courses with no applicants
        $courseData = array_filter($courseData, function($data) {
            return $data['total_applicants'] > 0;
        });

        // Sort by priority score (descending)
        uasort($courseData, function($a, $b) {
            if ($b['priority_score'] != $a['priority_score']) {
                return $b['priority_score'] <=> $a['priority_score'];
            }
            return $b['total_applicants'] <=> $a['total_applicants'];
        });

        // Assign ranks
        $rank = 1;
        $prioritizedCourses = [];
        foreach ($courseData as $courseName => $data) {
            $data['priority_rank'] = $rank;
            $data['priority_level'] = $this->getPriorityLevel($data['priority_score']);
            $prioritizedCourses[] = $data;
            $rank++;
        }

        return $prioritizedCourses;
    }

    /**
     * Initialize the tracking bucket for a course if it does not exist yet.
     */
    private function initializeCourseBucket(array &$courseData, array &$courseApplicants, string $courseName): void
    {
        if (isset($courseData[$courseName])) {
            if (!isset($courseApplicants[$courseName])) {
                $courseApplicants[$courseName] = [];
            }
            return;
        }

        $courseData[$courseName] = [
            'course_name' => $courseName,
            'registered_count' => 0,
            'preference_first_count' => 0,
            'preference_second_count' => 0,
            'total_applicants' => 0,
            'high_priority_applicants' => 0,
            'medium_priority_applicants' => 0,
            'low_priority_applicants' => 0,
            'average_score' => 0,
            'total_score_sum' => 0,
            'priority_score' => 0,
            'priority_rank' => 0,
            'applicants' => [],
        ];

        $courseApplicants[$courseName] = [];
    }

    /**
     * Resolve a submitted course to a tracked priority course name.
     */
    private function resolveCourseName(?string $rawCourseName, array $priorityCourses, array $excludedCourses): ?string
    {
        if (!$rawCourseName) {
            return null;
        }

        $courseName = trim($rawCourseName);
        if ($courseName === '') {
            return null;
        }

        foreach ($excludedCourses as $excluded) {
            if (strcasecmp($courseName, $excluded) === 0) {
                return null;
            }
        }

        foreach ($priorityCourses as $priorityCourse) {
            if (
                strcasecmp($courseName, $priorityCourse) === 0 ||
                stripos($priorityCourse, $courseName) !== false ||
                stripos($courseName, $priorityCourse) !== false
            ) {
                if (in_array($priorityCourse, $excludedCourses, true)) {
                    return null;
                }
                return $priorityCourse;
            }
        }

        return 'Other';
    }

    /**
     * Record demand for a course coming from a specific source.
     */
    private function recordCourseDemand(
        array &$courseData,
        array &$courseApplicants,
        array $priorityCourses,
        array $excludedCourses,
        User $user,
        ?string $rawCourseName,
        string $countKey,
        string $sourceLabel
    ): void {
        if (!$rawCourseName) {
            return;
        }

        $normalizedCourse = $this->resolveCourseName($rawCourseName, $priorityCourses, $excludedCourses);
        if (!$normalizedCourse) {
            return;
        }

        $this->initializeCourseBucket($courseData, $courseApplicants, $normalizedCourse);

        $courseData[$normalizedCourse][$countKey]++;

        // Score-based prioritization removed - using ApplicantPriorityService instead
        $totalScore = 0;

        if (!in_array($user->id, $courseApplicants[$normalizedCourse], true)) {
            $courseApplicants[$normalizedCourse][] = $user->id;
            $courseData[$normalizedCourse]['total_applicants']++;
            $courseData[$normalizedCourse]['total_score_sum'] += $totalScore;

            // Priority level counting removed - using ApplicantPriorityService instead
            $courseData[$normalizedCourse]['high_priority_applicants']++;
        }

        $this->addApplicantToCourse(
            $courseData[$normalizedCourse],
            $user,
            $totalScore,
            $sourceLabel,
            $rawCourseName
        );
    }

    /**
     * Track applicant information for aggregated course data.
     */
    private function addApplicantToCourse(array &$courseEntry, User $user, float $totalScore, string $source, string $submittedCourse): void
    {
        if (!isset($courseEntry['applicants'])) {
            $courseEntry['applicants'] = [];
        }

        foreach ($courseEntry['applicants'] as $existingApplicant) {
            if (($existingApplicant['id'] ?? null) === $user->id) {
                return;
            }
        }

        $courseEntry['applicants'][] = [
            'id' => $user->id,
            'name' => trim($user->first_name . ' ' . $user->last_name),
            'score' => round($totalScore, 2),
            'source' => $source,
            'submitted_course' => trim((string) $submittedCourse),
        ];
    }

    /**
     * Get course prioritization for a specific applicant
     */
    public function getApplicantCoursePrioritization(User $user): array
    {
        $basicInfo = $user->basicInfo;
        $schoolPref = $basicInfo->schoolPref ?? null;
        
        if (!$schoolPref) {
            return [
                'has_courses' => false,
                'courses' => [],
                'message' => 'No school preferences found for this applicant.'
            ];
        }

        $courses = [];
        // Score-based prioritization removed - using ApplicantPriorityService instead
        $totalScore = 0;
        $priorityRank = null;

        // Process first preference course
        if ($schoolPref->degree) {
            $course = $this->analyzeCourseForApplicant(
                $schoolPref->degree,
                $schoolPref->address,
                $schoolPref->school_type,
                $schoolPref->num_years,
                $user,
                null,
                'first',
                1
            );
            $courses[] = $course;
        }

        // Process second preference course
        if ($schoolPref->degree2) {
            $course = $this->analyzeCourseForApplicant(
                $schoolPref->degree2,
                $schoolPref->address2,
                $schoolPref->school_type2,
                $schoolPref->num_years2,
                $user,
                null,
                'second',
                2
            );
            $courses[] = $course;
        }

        // Sort courses by priority score
        usort($courses, function($a, $b) {
            return $b['priority_score'] <=> $a['priority_score'];
        });

        return [
            'has_courses' => true,
            'courses' => $courses,
            'applicant_score' => $totalScore,
            'applicant_rank' => $priorityRank,
            'total_courses' => count($courses)
        ];
    }

    /**
     * Analyze a course for a specific applicant
     */
    private function analyzeCourseForApplicant(
        string $courseName,
        ?string $schoolAddress,
        ?string $schoolType,
        ?int $numYears,
        User $user,
        $applicantScore, // Kept for compatibility but not used
        string $preference,
        int $preferenceRank
    ): array {
        // Score-based prioritization removed - using ApplicantPriorityService instead
        $totalScore = 0;
        $financialScore = 0;
        $academicScore = 0;
        $geographicScore = 0;
        $heritageScore = 0;

        // Calculate course priority score for this applicant
        // Factors: Preference rank (70%), Course alignment (30%)
        $preferenceWeight = $preference === 'first' ? 1.0 : 0.7;
        $preferenceWeightScore = $preferenceWeight * 70; // Max 70 points
        $alignmentScore = $this->calculateCourseAlignment($courseName, $user, null) * 30; // Max 30 points

        $priorityScore = $preferenceWeightScore + $alignmentScore;

        // Determine priority level
        $priorityLevel = $this->getApplicantCoursePriorityLevel($priorityScore, 0);

        // Generate recommendations
        $recommendations = $this->generateCourseRecommendations($courseName, $user, null, $schoolType);

        return [
            'course_name' => trim($courseName),
            'preference' => $preference,
            'preference_rank' => $preferenceRank,
            'school_address' => $schoolAddress ?? 'Not specified',
            'school_type' => $schoolType ?? 'Not specified',
            'num_years' => $numYears ?? null,
            'priority_score' => round($priorityScore, 2),
            'priority_level' => $priorityLevel,
            'applicant_score' => round($totalScore, 2),
            'financial_score' => round($financialScore, 2),
            'academic_score' => round($academicScore, 2),
            'geographic_score' => round($geographicScore, 2),
            'heritage_score' => round($heritageScore, 2),
            'recommendations' => $recommendations,
            'match_quality' => $this->calculateMatchQuality($totalScore, $priorityScore),
        ];
    }

    /**
     * Calculate course alignment with applicant's background
     */
    private function calculateCourseAlignment(string $courseName, User $user, $applicantScore): float
    {
        // Score-based alignment removed - using ApplicantPriorityService instead
        // Base alignment based on course priority
        $alignment = 0.5; // Base alignment
        
        // Check if course is a priority course
        $priorityCourses = [
            'Agriculture', 'Aqua-Culture and Fisheries', 'Anthropology',
            'Business Administration', 'Civil Engineering', 'Community Development',
            'Criminology', 'Education', 'Foreign Service',
            'Forestry and Environment Studies', 'Geodetic Engineering', 'Geology',
            'Law', 'Medicine and Allied Health Sciences', 'Mechanical Engineering',
            'Mining Engineering', 'Social Sciences', 'Social Work'
        ];
        
        foreach ($priorityCourses as $priorityCourse) {
            if (stripos($courseName, $priorityCourse) !== false) {
                $alignment += 0.3; // Priority course bonus
                break;
            }
        }

        return min($alignment, 1.0); // Cap at 1.0
    }

    /**
     * Get priority level for applicant's course
     */
    private function getApplicantCoursePriorityLevel(float $priorityScore, float $applicantScore): string
    {
        // Simplified priority levels based on priority score only
        if ($priorityScore >= 80) {
            return 'Very High Priority';
        } elseif ($priorityScore >= 70) {
            return 'High Priority';
        } elseif ($priorityScore >= 60) {
            return 'Medium Priority';
        } elseif ($priorityScore >= 50) {
            return 'Low Priority';
        } else {
            return 'Very Low Priority';
        }
    }

    /**
     * Calculate match quality
     */
    private function calculateMatchQuality(float $applicantScore, float $priorityScore): string
    {
        $average = ($applicantScore + $priorityScore) / 2;
        
        if ($average >= 80) {
            return 'Excellent Match';
        } elseif ($average >= 70) {
            return 'Good Match';
        } elseif ($average >= 60) {
            return 'Fair Match';
        } else {
            return 'Poor Match';
        }
    }

    /**
     * Generate course recommendations
     */
    private function generateCourseRecommendations(string $courseName, User $user, $applicantScore, ?string $schoolType): array
    {
        $recommendations = [];
        
        // Score-based recommendations removed - using ApplicantPriorityService instead
        $recommendations[] = 'Application under review - priority determined by ApplicantPriorityService';
        
        if ($schoolType === 'Public') {
            $recommendations[] = 'Public school preference may increase scholarship eligibility';
        }

        return $recommendations;
    }
}

