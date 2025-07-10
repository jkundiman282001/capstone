<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ethno extends Model
{
    use HasFactory;

    protected $table = 'ethno';
    protected $fillable = ['ethnicity'];
} 