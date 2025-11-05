<?php

namespace App\Services;

use App\Models\User;
use App\Models\ApplicantScore;
use Illuminate\Support\Facades\DB;

class ApplicantScoringService
{
    // Weight configuration for different scoring criteria
    private $weights = [
        'financial_need' => 0.25,      // 25% - Most important for scholarship
        'academic_performance' => 0.20,  // 20% - Academic merit
        'document_completeness' => 0.15, // 15% - Administrative compliance
        'geographic_priority' => 0.15,   // 15% - Geographic equity
        'indigenous_heritage' => 0.15,  // 15% - Cultural heritage priority
        'family_situation' => 0.10,     // 10% - Family circumstances
    ];

    // Maximum possible scores for each category
    private $maxScores = [
        'financial_need' => 100,
        'academic_performance' => 100,
        'document_completeness' => 100,
        'geographic_priority' => 100,
        'indigenous_heritage' => 100,
        'family_situation' => 100,
    ];

    /**
     * Calculate weighted score for a single applicant
     */
    public function calculateApplicantScore(User $user): ApplicantScore
    {
        $financialScore = $this->calculateFinancialNeedScore($user);
        $academicScore = $this->calculateAcademicPerformanceScore($user);
        $documentScore = $this->calculateDocumentCompletenessScore($user);
        $geographicScore = $this->calculateGeographicPriorityScore($user);
        $heritageScore = $this->calculateIndigenousHeritageScore($user);
        $familyScore = $this->calculateFamilySituationScore($user);

        // Calculate weighted total score
        $totalScore = (
            $financialScore * $this->weights['financial_need'] +
            $academicScore * $this->weights['academic_performance'] +
            $documentScore * $this->weights['document_completeness'] +
            $geographicScore * $this->weights['geographic_priority'] +
            $heritageScore * $this->weights['indigenous_heritage'] +
            $familyScore * $this->weights['family_situation']
        );

        // Generate scoring notes
        $notes = $this->generateScoringNotes($user, [
            'financial' => $financialScore,
            'academic' => $academicScore,
            'document' => $documentScore,
            'geographic' => $geographicScore,
            'heritage' => $heritageScore,
            'family' => $familyScore,
        ]);

        // Create or update applicant score
        return ApplicantScore::updateOrCreate(
            ['user_id' => $user->id],
            [
                'total_score' => round($totalScore, 2),
                'financial_need_score' => round($financialScore, 2),
                'academic_performance_score' => round($academicScore, 2),
                'document_completeness_score' => round($documentScore, 2),
                'geographic_priority_score' => round($geographicScore, 2),
                'indigenous_heritage_score' => round($heritageScore, 2),
                'family_situation_score' => round($familyScore, 2),
                'scoring_notes' => $notes,
                'last_calculated_at' => now(),
            ]
        );
    }

    /**
     * Calculate financial need score (0-100)
     * Higher score = greater financial need
     */
    private function calculateFinancialNeedScore(User $user): float
    {
        $score = 0;
        $basicInfo = $user->basicInfo;
        
        if (!$basicInfo) return 0;

        // Check family income levels
        $familyMembers = $basicInfo->family ?? collect();
        $totalIncome = 0;
        $incomeCount = 0;

        foreach ($familyMembers as $member) {
            if ($member->income && is_numeric($member->income)) {
                $totalIncome += (float) $member->income;
                $incomeCount++;
            }
        }

        if ($incomeCount > 0) {
            $averageIncome = $totalIncome / $incomeCount;
            
            // Income-based scoring (Philippine context)
            if ($averageIncome <= 15000) {
                $score += 40; // Very low income
            } elseif ($averageIncome <= 25000) {
                $score += 30; // Low income
            } elseif ($averageIncome <= 40000) {
                $score += 20; // Moderate income
            } elseif ($averageIncome <= 60000) {
                $score += 10; // Higher income
            }
        }

        // Check for siblings in school (financial burden)
        $siblings = $basicInfo->siblings ?? collect();
        $siblingsInSchool = $siblings->where('present_status', 'Studying')->count();
        $score += min($siblingsInSchool * 10, 30); // Max 30 points for siblings

        // Check if parents are unemployed/underemployed
        $unemployedParents = $familyMembers->whereIn('occupation', ['Unemployed', 'None', ''])->count();
        $score += $unemployedParents * 15; // 15 points per unemployed parent

        // Check for single parent household
        $singleParent = $familyMembers->where('status', 'Single Parent')->count() > 0;
        if ($singleParent) {
            $score += 15;
        }

        return min($score, 100);
    }

    /**
     * Calculate academic performance score (0-100)
     * Higher score = better academic performance
     */
    private function calculateAcademicPerformanceScore(User $user): float
    {
        $score = 0;
        $basicInfo = $user->basicInfo;
        
        if (!$basicInfo) return 0;

        $educationRecords = $basicInfo->education ?? collect();
        
        if ($educationRecords->count() > 0) {
            $totalGPA = 0;
            $gpaCount = 0;

            foreach ($educationRecords as $edu) {
                if ($edu->grade_ave && is_numeric($edu->grade_ave)) {
                    $gpa = (float) $edu->grade_ave;
                    $totalGPA += $gpa;
                    $gpaCount++;
                }
            }

            if ($gpaCount > 0) {
                $averageGPA = $totalGPA / $gpaCount;
                
                // GPA-based scoring (Philippine grading system)
                if ($averageGPA >= 95) {
                    $score += 40; // Excellent
                } elseif ($averageGPA >= 90) {
                    $score += 35; // Very good
                } elseif ($averageGPA >= 85) {
                    $score += 30; // Good
                } elseif ($averageGPA >= 80) {
                    $score += 25; // Satisfactory
                } elseif ($averageGPA >= 75) {
                    $score += 20; // Passing
                }
            }

            // Check for academic honors/rank
            foreach ($educationRecords as $edu) {
                if ($edu->rank && is_numeric($edu->rank)) {
                    $rank = (int) $edu->rank;
                    if ($rank <= 5) {
                        $score += 20; // Top 5
                    } elseif ($rank <= 10) {
                        $score += 15; // Top 10
                    } elseif ($rank <= 20) {
                        $score += 10; // Top 20
                    }
                }
            }
        }

        // Bonus for recent graduation
        $recentGraduation = $educationRecords->where('year_grad', '>=', now()->subYears(2)->year)->count() > 0;
        if ($recentGraduation) {
            $score += 10;
        }

        return min($score, 100);
    }

    /**
     * Calculate document completeness score (0-100)
     * Higher score = more complete documentation
     */
    private function calculateDocumentCompletenessScore(User $user): float
    {
        $requiredDocuments = [
            'birth_certificate',
            'income_document',
            'tribal_certificate',
            'endorsement',
            'good_moral',
            'grades'
        ];

        $documents = $user->documents ?? collect();
        $approvedCount = 0;
        $pendingCount = 0;

        foreach ($requiredDocuments as $docType) {
            $document = $documents->where('type', $docType)->first();
            if ($document) {
                if ($document->status === 'approved') {
                    $approvedCount++;
                } elseif ($document->status === 'pending') {
                    $pendingCount++;
                }
            }
        }

        // Score based on document status
        $score = ($approvedCount * 15) + ($pendingCount * 5); // 15 points for approved, 5 for pending
        
        // Bonus for early submission
        $earliestSubmission = $documents->min('created_at');
        if ($earliestSubmission && $earliestSubmission->diffInDays(now()) <= 30) {
            $score += 10; // Bonus for submitting within 30 days
        }

        return min($score, 100);
    }

    /**
     * Calculate geographic priority score (0-100)
     * Higher score = higher geographic priority
     */
    private function calculateGeographicPriorityScore(User $user): float
    {
        $score = 0;
        $basicInfo = $user->basicInfo;
        
        if (!$basicInfo || !$basicInfo->fullAddress || !$basicInfo->fullAddress->address) {
            return 0;
        }

        $address = $basicInfo->fullAddress->address;
        
        // Priority provinces/regions (customize based on NCIP priorities)
        $priorityProvinces = [
            'Bukidnon', 'Davao del Norte', 'Davao del Sur', 'Davao Oriental',
            'Cotabato', 'Sarangani', 'South Cotabato', 'Agusan del Norte',
            'Agusan del Sur', 'Surigao del Norte', 'Surigao del Sur'
        ];

        if (in_array($address->province, $priorityProvinces)) {
            $score += 30;
        }

        // Priority municipalities (customize based on NCIP priorities)
        $priorityMunicipalities = [
            'Malaybalay', 'Valencia', 'Maramag', 'Quezon', 'Kibawe',
            'Davao City', 'Tagum', 'Digos', 'Mati', 'Kidapawan'
        ];

        if (in_array($address->municipality, $priorityMunicipalities)) {
            $score += 20;
        }

        // Rural/remote area bonus
        $ruralKeywords = ['Rural', 'Remote', 'Mountain', 'Hill', 'Valley'];
        foreach ($ruralKeywords as $keyword) {
            if (stripos($address->barangay, $keyword) !== false) {
                $score += 15;
                break;
            }
        }

        // Distance from major cities (simplified)
        $majorCities = ['Davao City', 'Cagayan de Oro', 'General Santos', 'Cotabato City'];
        if (!in_array($address->municipality, $majorCities)) {
            $score += 10; // Bonus for being outside major cities
        }

        return min($score, 100);
    }

    /**
     * Calculate indigenous heritage score (0-100)
     * Higher score = stronger indigenous heritage
     */
    private function calculateIndigenousHeritageScore(User $user): float
    {
        $score = 0;
        
        // Check if user has indigenous ethnicity
        if ($user->ethno && $user->ethno->ethnicity) {
            $score += 40; // Base score for having indigenous ethnicity
            
            // Priority indigenous groups (customize based on NCIP priorities)
            $priorityGroups = [
                'Lumad', 'Manobo', 'T\'boli', 'B\'laan', 'Bagobo',
                'Maguindanao', 'Maranao', 'Tausug', 'Yakan', 'Subanen'
            ];
            
            if (in_array($user->ethno->ethnicity, $priorityGroups)) {
                $score += 30;
            }
        }

        // Check family indigenous heritage
        $basicInfo = $user->basicInfo;
        if ($basicInfo && $basicInfo->family) {
            $familyMembers = $basicInfo->family;
            $indigenousFamilyCount = $familyMembers->where('ethno_id', '!=', null)->count();
            $score += min($indigenousFamilyCount * 10, 30); // Max 30 points
        }

        return min($score, 100);
    }

    /**
     * Calculate family situation score (0-100)
     * Higher score = more challenging family situation
     */
    private function calculateFamilySituationScore(User $user): float
    {
        $score = 0;
        $basicInfo = $user->basicInfo;
        
        if (!$basicInfo) return 0;

        $familyMembers = $basicInfo->family ?? collect();
        
        // Check for single parent household
        $singleParent = $familyMembers->where('status', 'Single Parent')->count() > 0;
        if ($singleParent) {
            $score += 25;
        }

        // Check for elderly parents (financial burden)
        $elderlyParents = $familyMembers->where('age', '>=', 60)->count();
        $score += min($elderlyParents * 10, 20); // Max 20 points

        // Check for disabled family members
        $disabledMembers = $familyMembers->where('status', 'Disabled')->count();
        $score += min($disabledMembers * 15, 30); // Max 30 points

        // Check for large family size
        $familySize = $familyMembers->count();
        if ($familySize >= 6) {
            $score += 15; // Large family bonus
        }

        // Check for siblings in college (financial burden)
        $siblings = $basicInfo->siblings ?? collect();
        $collegeSiblings = $siblings->where('present_status', 'Studying')->count();
        $score += min($collegeSiblings * 8, 25); // Max 25 points

        return min($score, 100);
    }

    /**
     * Generate scoring notes for transparency
     */
    private function generateScoringNotes(User $user, array $scores): string
    {
        $notes = [];
        
        // Financial need notes
        if ($scores['financial'] >= 70) {
            $notes[] = "High financial need - eligible for priority consideration";
        } elseif ($scores['financial'] >= 50) {
            $notes[] = "Moderate financial need";
        }

        // Academic performance notes
        if ($scores['academic'] >= 80) {
            $notes[] = "Excellent academic performance";
        } elseif ($scores['academic'] >= 60) {
            $notes[] = "Good academic performance";
        }

        // Document completeness notes
        if ($scores['document'] >= 90) {
            $notes[] = "Complete documentation";
        } elseif ($scores['document'] >= 70) {
            $notes[] = "Mostly complete documentation";
        } else {
            $notes[] = "Incomplete documentation - needs attention";
        }

        // Geographic priority notes
        if ($scores['geographic'] >= 60) {
            $notes[] = "High geographic priority area";
        }

        // Indigenous heritage notes
        if ($scores['heritage'] >= 70) {
            $notes[] = "Strong indigenous heritage";
        }

        // Family situation notes
        if ($scores['family'] >= 60) {
            $notes[] = "Challenging family circumstances";
        }

        return implode('; ', $notes);
    }

    /**
     * Calculate and update scores for all applicants
     */
    public function calculateAllApplicantScores(): array
    {
        $users = User::with(['basicInfo', 'documents', 'ethno', 'basicInfo.family', 'basicInfo.siblings', 'basicInfo.education'])
            ->whereHas('basicInfo')
            ->get();

        $results = [];
        
        foreach ($users as $user) {
            $score = $this->calculateApplicantScore($user);
            $results[] = [
                'user_id' => $user->id,
                'name' => $user->first_name . ' ' . $user->last_name,
                'total_score' => $score->total_score,
                'priority_rank' => null // Will be set after all scores are calculated
            ];
        }

        // Sort by total score and assign ranks
        usort($results, function($a, $b) {
            return $b['total_score'] <=> $a['total_score'];
        });

        // Update ranks
        foreach ($results as $index => $result) {
            ApplicantScore::where('user_id', $result['user_id'])
                ->update(['priority_rank' => $index + 1]);
            $results[$index]['priority_rank'] = $index + 1;
        }

        return $results;
    }

    /**
     * Get top priority applicants
     */
    public function getTopPriorityApplicants(int $limit = 10): array
    {
        return ApplicantScore::with('user')
            ->orderBy('total_score', 'desc')
            ->orderBy('priority_rank', 'asc')
            ->limit($limit)
            ->get()
            ->map(function($score) {
                return [
                    'user' => $score->user,
                    'total_score' => $score->total_score,
                    'priority_rank' => $score->priority_rank,
                    'scoring_notes' => $score->scoring_notes,
                    'last_calculated' => $score->last_calculated_at
                ];
            })
            ->toArray();
    }

    /**
     * Get scoring statistics
     */
    public function getScoringStatistics(): array
    {
        $stats = ApplicantScore::selectRaw('
            COUNT(*) as total_applicants,
            AVG(total_score) as average_score,
            MAX(total_score) as highest_score,
            MIN(total_score) as lowest_score,
            AVG(financial_need_score) as avg_financial_score,
            AVG(academic_performance_score) as avg_academic_score,
            AVG(document_completeness_score) as avg_document_score
        ')->first();

        return [
            'total_applicants' => $stats->total_applicants ?? 0,
            'average_score' => round($stats->average_score ?? 0, 2),
            'highest_score' => round($stats->highest_score ?? 0, 2),
            'lowest_score' => round($stats->lowest_score ?? 0, 2),
            'avg_financial_score' => round($stats->avg_financial_score ?? 0, 2),
            'avg_academic_score' => round($stats->avg_academic_score ?? 0, 2),
            'avg_document_score' => round($stats->avg_document_score ?? 0, 2),
        ];
    }
}
