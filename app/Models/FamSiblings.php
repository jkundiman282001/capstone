<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamSiblings extends Model
{
    use HasFactory;

    protected $table = 'fam_siblings';

    protected $fillable = [
        'basic_info_id',
        'name',
        'age',
        'scholarship',
        'course_year',
        'present_status',
    ];
} 