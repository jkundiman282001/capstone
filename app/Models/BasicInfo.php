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
        'full_address_id',
        'house_num',
        'birthdate',
        'birthplace',
        'gender',
        'civil_status',
        'school_pref_id',
        'type_assist',
        'assistance_for',
        'application_status',
        'application_rejection_reason',
        'grant_status',
        'current_year_level',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function fullAddress()
    {
        return $this->belongsTo(\App\Models\FullAddress::class, 'full_address_id');
    }

    public function schoolPref()
    {
        return $this->belongsTo(\App\Models\SchoolPref::class, 'school_pref_id');
    }

    public function education()
    {
        return $this->hasMany(\App\Models\Education::class, 'basic_info_id');
    }

    public function family()
    {
        return $this->hasMany(\App\Models\Family::class, 'basic_info_id');
    }

    public function siblings()
    {
        return $this->hasMany(\App\Models\FamSiblings::class, 'basic_info_id');
    }
} 