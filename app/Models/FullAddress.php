<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FullAddress extends Model
{
    use HasFactory;

    protected $table = 'full_address';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'mailing_address_id',
        'permanent_address_id',
        'origin_id',
    ];

    public function mailingAddress(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\MailingAddress::class, 'mailing_address_id');
    }

    public function permanentAddress(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\PermanentAddress::class, 'permanent_address_id');
    }

    public function origin(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Origin::class, 'origin_id');
    }

    public function address(): \Illuminate\Database\Eloquent\Relations\HasOneThrough
    {
        // Assuming mailing address is the primary address
        return $this->hasOneThrough(
            Address::class,
            MailingAddress::class,
            'id', // Foreign key on mailing_address table
            'id', // Foreign key on address table
            'mailing_address_id', // Local key on full_address table
            'address_id' // Local key on mailing_address table
        );
    }
}
