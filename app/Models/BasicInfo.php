<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

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
        'gpa',
        'grant_1st_sem',
        'grant_2nd_sem',
        'rssc_score',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'gpa' => 'decimal:2', // Cast to decimal with 2 decimal places
        'rssc_score' => 'decimal:2', // Cast to decimal with 2 decimal places
    ];

    protected static function booted()
    {
        static::saving(function (self $model) {
            // Business rule: pending applications must not have a grant status.
            // This prevents "pending + grantee" from being filtered out of the applicants list.
            if (
                $model->application_status !== null &&
                strtolower(trim((string) $model->application_status)) === 'pending'
            ) {
                $model->grant_status = null;
                // Only touch these flags if the current DB schema has the columns
                if (Schema::hasColumn('basic_info', 'grant_1st_sem')) {
                    $model->grant_1st_sem = false;
                }
                if (Schema::hasColumn('basic_info', 'grant_2nd_sem')) {
                    $model->grant_2nd_sem = false;
                }
            }
        });
    }

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