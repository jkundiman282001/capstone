<?php

namespace App\Services;

use App\Models\BasicInfo;
use App\Models\User;
use App\Models\ApplicantScore;
use Illuminate\Support\Facades\DB;

class CoursePriorityService
{
    /**
     * Get prioritized courses based on multiple criteria
     */
    public function getPrioritizedCourses(): array
    {
        // Get all applicants with school preferences and scores
        $applicants = User::with([
            'basicInfo.schoolPref', 
            'applicantScore', 
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

            $applicantScore = $applicant->applicantScore;
            $totalScore = $applicantScore ? ($applicantScore->total_score ?? 0) : 0;
            $priorityRank = $applicantScore ? ($applicantScore->priority_rank ?? 9999) : 9999;

            // Process first preference course
            if ($schoolPref->degree) {
                $courseName = trim($schoolPref->degree);
                if (!isset($courseData[$courseName])) {
                    $courseData[$courseName] = [
                        'course_name' => $courseName,
                        'first_preference_count' => 0,
                        'second_preference_count' => 0,
                        'total_applicants' => 0,
                        'high_priority_applicants' => 0, // Score >= 80
                        'medium_priority_applicants' => 0, // Score 60-79
                        'low_priority_applicants' => 0, // Score < 60
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

                // Count by priority level
                if ($totalScore >= 80) {
                    $courseData[$courseName]['high_priority_applicants']++;
                } elseif ($totalScore >= 60) {
                    $courseData[$courseName]['medium_priority_applicants']++;
                } else {
                    $courseData[$courseName]['low_priority_applicants']++;
                }

                // Track top applicants
                if ($totalScore >= 70) {
                    $courseData[$courseName]['top_applicants'][] = [
                        'user_id' => $applicant->id,
                        'name' => $applicant->first_name . ' ' . $applicant->last_name,
                        'score' => $totalScore,
                        'rank' => $priorityRank,
                    ];
                }

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

                // Count by priority level (with lower weight for second preference)
                if ($totalScore >= 80) {
                    $courseData[$courseName]['high_priority_applicants']++;
                } elseif ($totalScore >= 60) {
                    $courseData[$courseName]['medium_priority_applicants']++;
                } else {
                    $courseData[$courseName]['low_priority_applicants']++;
                }

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

            $courseData[$course] = [
                'course_name' => $course,
                'registered_count' => 0, // From user.course field
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
            $courseApplicants[$course] = [];
        }
        // Initialize "Other" category tracking
        $courseApplicants['Other'] = [];

        // Get courses from user registration (user.course field)
        $usersWithCourse = User::whereNotNull('course')
            ->where('course', '!=', '')
            ->with(['applicantScore'])
            ->get();

        foreach ($usersWithCourse as $user) {
            $originalCourseName = trim($user->course);
            if ($originalCourseName === '') {
                continue;
            }

            if (in_array($originalCourseName, $excludedCourses, true)) {
                continue;
            }

            $courseName = $originalCourseName;

            if (!isset($courseData[$courseName])) {
                $matched = false;
                foreach ($priorityCourses as $priorityCourse) {
                    if (stripos($courseName, $priorityCourse) !== false || stripos($priorityCourse, $courseName) !== false) {
                        $courseName = $priorityCourse;
                        $matched = true;
                        break;
                    }
                }

                if (!$matched) {
                    if (!isset($courseData['Other'])) {
                        $courseData['Other'] = [
                            'course_name' => 'Other',
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
                        $courseApplicants['Other'] = [];
                    }
                    $courseName = 'Other';
                }
            }

            if ($courseName === 'Other' && in_array($originalCourseName, $excludedCourses, true)) {
                continue;
            }

            if (!isset($courseApplicants[$courseName])) {
                $courseApplicants[$courseName] = [];
            }

            $courseData[$courseName]['registered_count']++;

            // Track this user for this course to avoid double counting
            if (!in_array($user->id, $courseApplicants[$courseName])) {
                $courseApplicants[$courseName][] = $user->id;
                $courseData[$courseName]['total_applicants']++;
                
                $applicantScore = $user->applicantScore;
                $totalScore = $applicantScore ? ($applicantScore->total_score ?? 0) : 0;
                $courseData[$courseName]['total_score_sum'] += $totalScore;

                // Count by priority level
                if ($totalScore >= 80) {
                    $courseData[$courseName]['high_priority_applicants']++;
                } elseif ($totalScore >= 60) {
                    $courseData[$courseName]['medium_priority_applicants']++;
                } else {
                    $courseData[$courseName]['low_priority_applicants']++;
                }

                $this->addApplicantToCourse($courseData[$courseName], $user, $totalScore, 'Registered Course', $originalCourseName);
            }
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
        $applicantScore = $user->applicantScore;
        
        if (!$schoolPref) {
            return [
                'has_courses' => false,
                'courses' => [],
                'message' => 'No school preferences found for this applicant.'
            ];
        }

        $courses = [];
        $totalScore = $applicantScore ? ($applicantScore->total_score ?? 0) : 0;
        $priorityRank = $applicantScore ? ($applicantScore->priority_rank ?? null) : null;

        // Process first preference course
        if ($schoolPref->degree) {
            $course = $this->analyzeCourseForApplicant(
                $schoolPref->degree,
                $schoolPref->address,
                $schoolPref->school_type,
                $schoolPref->num_years,
                $user,
                $applicantScore ?? null,
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
                $applicantScore ?? null,
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
        ?ApplicantScore $applicantScore,
        string $preference,
        int $preferenceRank
    ): array {
        $totalScore = $applicantScore ? ($applicantScore->total_score ?? 0) : 0;
        $financialScore = $applicantScore ? ($applicantScore->financial_need_score ?? 0) : 0;
        $academicScore = $applicantScore ? ($applicantScore->academic_performance_score ?? 0) : 0;
        $geographicScore = $applicantScore ? ($applicantScore->geographic_priority_score ?? 0) : 0;
        $heritageScore = $applicantScore ? ($applicantScore->indigenous_heritage_score ?? 0) : 0;

        // Calculate course priority score for this applicant
        // Factors: Applicant's total score (50%), Preference rank (30%), Course alignment (20%)
        $preferenceWeight = $preference === 'first' ? 1.0 : 0.7;
        $applicantScoreWeight = ($totalScore / 100) * 50; // Max 50 points
        $preferenceWeightScore = $preferenceWeight * 30; // Max 30 points
        $alignmentScore = $this->calculateCourseAlignment($courseName, $user, $applicantScore) * 20; // Max 20 points

        $priorityScore = $applicantScoreWeight + $preferenceWeightScore + $alignmentScore;

        // Determine priority level
        $priorityLevel = $this->getApplicantCoursePriorityLevel($priorityScore, $totalScore);

        // Generate recommendations
        $recommendations = $this->generateCourseRecommendations($courseName, $user, $applicantScore, $schoolType);

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
    private function calculateCourseAlignment(string $courseName, User $user, ?ApplicantScore $applicantScore): float
    {
        $alignment = 0.5; // Base alignment

        if (!$applicantScore) {
            return $alignment;
        }

        // Check if applicant has strong academic performance
        $academicScore = $applicantScore->academic_performance_score ?? 0;
        if ($academicScore >= 80) {
            $alignment += 0.2; // Strong academic performance
        } elseif ($academicScore >= 60) {
            $alignment += 0.1;
        }

        // Check if course name contains keywords that match indigenous heritage
        $heritageScore = $applicantScore->indigenous_heritage_score ?? 0;
        if ($heritageScore >= 70) {
            $alignment += 0.2; // Strong heritage connection
        } elseif ($heritageScore >= 50) {
            $alignment += 0.1;
        }

        // Check financial need (scholarship programs often prioritize those in need)
        $financialScore = $applicantScore->financial_need_score ?? 0;
        if ($financialScore >= 70) {
            $alignment += 0.1; // High financial need aligns with scholarship goals
        }

        return min($alignment, 1.0); // Cap at 1.0
    }

    /**
     * Get priority level for applicant's course
     */
    private function getApplicantCoursePriorityLevel(float $priorityScore, float $applicantScore): string
    {
        if ($priorityScore >= 80 && $applicantScore >= 75) {
            return 'Very High Priority';
        } elseif ($priorityScore >= 70 && $applicantScore >= 65) {
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
    private function generateCourseRecommendations(string $courseName, User $user, ?ApplicantScore $applicantScore, ?string $schoolType): array
    {
        $recommendations = [];
        
        if (!$applicantScore) {
            $recommendations[] = 'Applicant score not yet calculated - review pending';
            return $recommendations;
        }
        
        $totalScore = $applicantScore->total_score ?? 0;
        $academicScore = $applicantScore->academic_performance_score ?? 0;
        $financialScore = $applicantScore->financial_need_score ?? 0;

        if ($totalScore >= 80) {
            $recommendations[] = 'High priority applicant - Strong candidate for scholarship';
        }

        if ($academicScore >= 80) {
            $recommendations[] = 'Excellent academic performance supports course eligibility';
        } elseif ($academicScore < 60) {
            $recommendations[] = 'Consider academic support or preparatory programs';
        }

        if ($financialScore >= 70) {
            $recommendations[] = 'High financial need aligns with scholarship objectives';
        }

        if ($schoolType === 'Public') {
            $recommendations[] = 'Public school preference may increase scholarship eligibility';
        }

        if (empty($recommendations)) {
            $recommendations[] = 'Application under review - all factors being considered';
        }

        return $recommendations;
    }
}

