<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Replacement extends Model
{
    use HasFactory;

    protected $table = 'replacements';

    protected $fillable = [
        'replacement_user_id',
        'replaced_user_id',
        'replaced_name',
        'replacement_reason',
        'school_year',
        'created_by_staff_id',
    ];

    public function replacementUser()
    {
        return $this->belongsTo(User::class, 'replacement_user_id');
    }

    public function replacedUser()
    {
        return $this->belongsTo(User::class, 'replaced_user_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(Staff::class, 'created_by_staff_id');
    }
}


