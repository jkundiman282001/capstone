<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationDraft extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'current_step',
        'form_data',
    ];

    protected $casts = [
        'form_data' => 'array',
        'current_step' => 'integer',
    ];

    /**
     * Get the user that owns the draft
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
