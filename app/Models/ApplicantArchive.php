<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicantArchive extends Model
{
    protected $fillable = [
        'user_id',
        'replacement_id',
        'data',
        'archived_by',
        'archived_at',
    ];

    protected $casts = [
        'data' => 'array',
        'archived_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replacement()
    {
        return $this->belongsTo(Replacement::class);
    }

    public function archiver()
    {
        return $this->belongsTo(Staff::class, 'archived_by');
    }
}
