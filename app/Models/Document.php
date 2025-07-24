<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'user_id',
        'filename',
        'filepath',
        'filetype',
        'filesize',
        'description',
        'status',
        'type',
    ];
}
