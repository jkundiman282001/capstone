<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolPref extends Model
{
    use HasFactory;

    protected $table = 'school_pref';

    protected $primaryKey = 'school_pref_id';

    protected $fillable = [
        'address',
        'degree',
        'school_type',
        'num_years',
        'address2',
        'degree2',
        'school_type2',
        'num_years2',
        'ques_answer1',
        'ques_answer2',
    ];
} 