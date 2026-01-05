<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolPref extends Model
{
    use HasFactory;

    protected $table = 'school_pref';

    protected $fillable = [
        'school_name',
        'address',
        'degree',
        'alt_degree',
        'school_type',
        'num_years',
        'school_name2',
        'address2',
        'degree2',
        'alt_degree2',
        'school_type2',
        'num_years2',
        'ques_answer1',
        'ques_answer2',
    ];
}
