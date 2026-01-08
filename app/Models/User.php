<?php

namespace App\Models;

use App\Notifications\VerifyEmailNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @method void notify($notification)
 * @method bool hasVerifiedEmail()
 * @method bool markEmailAsVerified()
 * @method bool sendEmailVerificationNotification()
 * @method string getEmailForVerification()
 */
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'contact_num',
        'email',
        'password',
        'profile_pic',
        'ethno_id',
        'course',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function basicInfo(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\App\Models\BasicInfo::class, 'user_id');
    }

    public function ethno(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Ethno::class, 'ethno_id');
    }

    public function documents(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Document::class, 'user_id');
    }

    public function applicationHistory(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\ApplicationHistory::class, 'user_id');
    }

    /**
     * Get the profile picture URL.
     */
    public function getProfilePicUrlAttribute()
    {
        if ($this->profile_pic) {
            // Use the direct route to serve images, bypasses symlink issues on Cloud
            return route('profile-pic.show', ['filename' => basename($this->profile_pic)]);
        }

        return null;
    }

    /**
     * Send the email verification notification using the custom template.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailNotification);
    }
}
