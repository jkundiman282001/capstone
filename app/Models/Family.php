<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Family extends Model
{
    use HasFactory;

    protected $table = 'family';

    protected $fillable = [
        'basic_info_id',
        'ethno_id',
        'fam_type',
        'name',
        'address',
        'occupation',
        'office_address',
        'educational_attainment',
        'income',
        'status', // for father's or mother's status (Living/Deceased)
    ];

    public function ethno()
    {
        return $this->belongsTo(\App\Models\Ethno::class, 'ethno_id');
    }
} 