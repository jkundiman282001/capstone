<?php

namespace App\Services;

use App\Models\Document;
use Carbon\Carbon;

class DocumentPriorityService
{
    /**
     * Document type priority weights (for future use)
     * Lower number = higher priority
     */
    private $documentTypePriorities = [
        'income_document' => 1,      // Highest priority
        'birth_certificate' => 2,
        'tribal_certificate' => 3,
        'endorsement' => 4,
        'good_moral' => 5,
        'grades' => 6,
    ];

    /**
     * Priority Indigenous Groups (secondary priority after FCFS)
     */
    private $priorityEthnoGroups = [
        "b'laan", 'bagobo', 'kalagan', 'kaulo'
    ];

    private function isPriorityEthno(?string $ethnicity): bool
    {
        if (!$ethnicity) {
            return false;
        }
        $norm = strtolower(trim($ethnicity));
        return in_array($norm, $this->priorityEthnoGroups, true);
    }

    /**
     * Calculate priority for a single document based on "First Come, First Serve"
     * Earlier submissions get higher priority
     */
    public function calculateDocumentPriority(Document $document): Document
    {
        // Use submitted_at if available, otherwise use created_at
        $submittedAt = $document->submitted_at ?? $document->created_at;
        
        // Update submitted_at if it's null
        if (!$document->submitted_at && $document->created_at) {
            $document->submitted_at = $document->created_at;
            $document->save();
        }

        // Calculate priority score based on submission time
        // Earlier submissions = higher priority score (First Come, First Serve)
        // Formula: Older documents get higher scores
        $now = Carbon::now();
        $hoursSinceSubmission = $now->diffInHours($submittedAt);
        
        // Priority score: Base score + hours since submission
        // Older documents (more hours) = higher score = higher priority
        // Use timestamp difference in seconds for more precision
        $secondsSinceSubmission = $now->diffInSeconds($submittedAt);
        
        // Base score from hours (older = higher)
        // Convert hours to a score (max ~100000 hours = ~11 years)
        $priorityScore = $hoursSinceSubmission * 1000;
        
        // Add seconds for fine-grained ordering (ensures earliest submission wins ties)
        $priorityScore += $secondsSinceSubmission;
        
        // Add document type weight (for future expansion - lower weight = higher priority type)
        // For now, we'll use it as a secondary factor (subtract small amount)
        $typeWeight = $this->documentTypePriorities[$document->type] ?? 10;
        $priorityScore = $priorityScore - ($typeWeight * 0.01); // Very small adjustment

        // Secondary priority: Indigenous group priority bonus
        // This should be weaker than pure FCFS so it only affects near-ties
        $ethno = optional(optional($document->user)->ethno)->ethnicity;
        if ($this->isPriorityEthno($ethno)) {
            // Add a modest bonus to keep FCFS dominant
            $priorityScore += 500; // small bump
        }

        $document->priority_score = max($priorityScore, 0); // Ensure non-negative
        $document->save();

        return $document;
    }

    /**
     * Recalculate priorities for all pending documents and assign ranks
     */
    public function recalculateAllPriorities(): array
    {
        // Get all pending documents (eager load user + ethno for tie-breakers)
        $pendingDocuments = Document::with(['user.ethno'])
            ->where('status', 'pending')
            ->whereNotNull('created_at')
            ->get();

        // Set submitted_at for documents that don't have it
        foreach ($pendingDocuments as $document) {
            if (!$document->submitted_at) {
                $document->submitted_at = $document->created_at;
                $document->save();
            }
        }

        // Calculate priority scores for all documents
        foreach ($pendingDocuments as $document) {
            $this->calculateDocumentPriority($document);
        }

        // Sort primarily by submission time (ascending = oldest first)
        // Tie-breaker: priority indigenous group first, then created_at
        $sortedDocuments = $pendingDocuments
            ->filter(function ($doc) { return !is_null($doc->submitted_at); })
            ->sort(function ($a, $b) {
                // Primary: submitted_at asc
                if ($a->submitted_at != $b->submitted_at) {
                    return $a->submitted_at <=> $b->submitted_at;
                }
                // Secondary: priority indigenous group desc (true first)
                $aPriority = $this->isPriorityEthno(optional(optional($a->user)->ethno)->ethnicity);
                $bPriority = $this->isPriorityEthno(optional(optional($b->user)->ethno)->ethnicity);
                if ($aPriority !== $bPriority) {
                    return $bPriority <=> $aPriority;
                }
                // Tertiary: created_at asc
                return $a->created_at <=> $b->created_at;
            })
            ->values();

        // Assign ranks
        $rank = 1;
        foreach ($sortedDocuments as $document) {
            $document->priority_rank = $rank;
            $document->save();
            $rank++;
        }

        return [
            'total_documents' => $sortedDocuments->count(),
            'documents' => $sortedDocuments->map(function($doc) {
                return [
                    'id' => $doc->id,
                    'type' => $doc->type,
                    'applicant' => $doc->user->first_name . ' ' . $doc->user->last_name,
                    'priority_rank' => $doc->priority_rank,
                    'priority_score' => $doc->priority_score,
                    'priority_ethno' => optional(optional($doc->user)->ethno)->ethnicity,
                    'submitted_at' => $doc->submitted_at ? $doc->submitted_at->format('Y-m-d H:i:s') : null,
                    'submitted_hours_ago' => $doc->submitted_at ? Carbon::now()->diffInHours($doc->submitted_at) : null,
                ];
            })->toArray()
        ];
    }

    /**
     * Get documents ordered by priority (First Come, First Serve)
     * Oldest submissions = Highest priority = Rank #1
     */
    public function getPrioritizedDocuments($status = 'pending', $limit = null)
    {
        $query = Document::with(['user.ethno'])
            ->where('status', $status)
            ->whereNotNull('submitted_at')
            ->orderBy('submitted_at', 'asc') // Earliest first = Highest priority
            ->orderBy('created_at', 'asc'); // Tiebreaker

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Get top priority documents for review
     */
    public function getTopPriorityDocuments($limit = 10)
    {
        return Document::with('user')
            ->where('status', 'pending')
            ->whereNotNull('submitted_at')
            ->orderBy('submitted_at', 'asc') // Oldest first = Highest priority
            ->orderBy('priority_rank', 'asc') // Use rank if available
            ->limit($limit)
            ->get();
    }

    /**
     * Calculate priority when a new document is uploaded
     */
    public function onDocumentUploaded(Document $document): Document
    {
        // Set submitted_at to current time
        $document->submitted_at = Carbon::now();
        $document->save();

        // Calculate priority
        $this->calculateDocumentPriority($document);

        // Recalculate all ranks
        $this->recalculateAllPriorities();

        return $document;
    }

    /**
     * Get priority statistics
     */
    public function getPriorityStatistics(): array
    {
        $totalPending = Document::where('status', 'pending')->count();
        $rankedPending = Document::where('status', 'pending')
            ->whereNotNull('priority_rank')
            ->count();
        
        $oldestDocument = Document::where('status', 'pending')
            ->orderBy('submitted_at', 'asc')
            ->orderBy('created_at', 'asc')
            ->first();

        $newestDocument = Document::where('status', 'pending')
            ->orderBy('submitted_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();

        return [
            'total_pending' => $totalPending,
            'ranked_pending' => $rankedPending,
            'oldest_submission' => $oldestDocument ? [
                'document_id' => $oldestDocument->id,
                'type' => $oldestDocument->type,
                'submitted_at' => $oldestDocument->submitted_at ? $oldestDocument->submitted_at->format('Y-m-d H:i:s') : null,
                'hours_waiting' => $oldestDocument->submitted_at ? Carbon::now()->diffInHours($oldestDocument->submitted_at) : null,
            ] : null,
            'newest_submission' => $newestDocument ? [
                'document_id' => $newestDocument->id,
                'type' => $newestDocument->type,
                'submitted_at' => $newestDocument->submitted_at ? $newestDocument->submitted_at->format('Y-m-d H:i:s') : null,
            ] : null,
        ];
    }
}

