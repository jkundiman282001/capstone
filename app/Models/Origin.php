<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Origin extends Model
{
    use HasFactory;

    protected $table = 'origin';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'address_id',
        'house_num',
    ];

    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }
}
