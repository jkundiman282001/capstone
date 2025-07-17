<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    use HasFactory;
    protected $table = 'education';
    protected $primaryKey = 'id';
    protected $fillable = [
        'basic_info_id',
        'category',
        'school_name',
        'school_type',
        'year_grad',
        'grade_ave',
        'rank',
    ];
} 