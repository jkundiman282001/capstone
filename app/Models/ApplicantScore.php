<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicantScore extends Model
{
    protected $fillable = [
        'user_id',
        'total_score',
        'financial_need_score',
        'academic_performance_score',
        'document_completeness_score',
        'geographic_priority_score',
        'indigenous_heritage_score',
        'family_situation_score',
        'priority_rank',
        'scoring_notes',
        'last_calculated_at',
    ];

    protected $casts = [
        'total_score' => 'decimal:2',
        'financial_need_score' => 'decimal:2',
        'academic_performance_score' => 'decimal:2',
        'document_completeness_score' => 'decimal:2',
        'geographic_priority_score' => 'decimal:2',
        'indigenous_heritage_score' => 'decimal:2',
        'family_situation_score' => 'decimal:2',
        'last_calculated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the score
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the priority level based on score
     */
    public function getPriorityLevelAttribute(): string
    {
        if ($this->total_score >= 80) {
            return 'High Priority';
        } elseif ($this->total_score >= 60) {
            return 'Medium Priority';
        } elseif ($this->total_score >= 40) {
            return 'Low Priority';
        } else {
            return 'Very Low Priority';
        }
    }

    /**
     * Get the priority color for UI
     */
    public function getPriorityColorAttribute(): string
    {
        if ($this->total_score >= 80) {
            return 'red'; // High priority
        } elseif ($this->total_score >= 60) {
            return 'orange'; // Medium priority
        } elseif ($this->total_score >= 40) {
            return 'yellow'; // Low priority
        } else {
            return 'gray'; // Very low priority
        }
    }

    /**
     * Scope for high priority applicants
     */
    public function scopeHighPriority($query)
    {
        return $query->where('total_score', '>=', 80);
    }

    /**
     * Scope for medium priority applicants
     */
    public function scopeMediumPriority($query)
    {
        return $query->whereBetween('total_score', [60, 79]);
    }

    /**
     * Scope for low priority applicants
     */
    public function scopeLowPriority($query)
    {
        return $query->whereBetween('total_score', [40, 59]);
    }

    /**
     * Scope for very low priority applicants
     */
    public function scopeVeryLowPriority($query)
    {
        return $query->where('total_score', '<', 40);
    }
}
