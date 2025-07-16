<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BasicInfo extends Model
{
    use HasFactory;

    protected $table = 'basic_info';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'user_id',
        'house_num',
        'birthdate',
        'birthplace',
        'gender',
        'civil_status',
        'ethno_id',
        'address_id',
        'education_id',
        'family_id',
        'school_pref_id',
        'full_address_id',
    ];
} 