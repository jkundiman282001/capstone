<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'user_id',
        'filename',
        'filepath',
        'filetype',
        'filesize',
        'description',
        'status',
        'type',
        'priority_rank',
        'priority_score',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'priority_score' => 'decimal:2',
    ];

    /**
     * Get the user that owns the document
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get priority level based on rank
     */
    public function getPriorityLevelAttribute(): string
    {
        if (!$this->priority_rank) {
            return 'Not Ranked';
        }

        if ($this->priority_rank <= 10) {
            return 'High Priority';
        } elseif ($this->priority_rank <= 50) {
            return 'Medium Priority';
        } else {
            return 'Low Priority';
        }
    }

    /**
     * Get waiting time in hours
     */
    public function getWaitingHoursAttribute(): int
    {
        $submittedAt = $this->submitted_at ?? $this->created_at;
        if (!$submittedAt) {
            return 0;
        }
        return $submittedAt->diffInHours(now());
    }
}
