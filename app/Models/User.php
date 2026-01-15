<?php

namespace App\Models;

use App\Notifications\VerifyEmailNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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
        'educational_status',
        'college_year',
        'grade_scale',
        'gpa',
        'role',
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

    public function transactionHistories(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\TransactionHistory::class, 'user_id');
    }

    /**
     * Get the converted grade based on the selected scale.
     */
    public function getConvertedGradeAttribute()
    {
        if (!$this->gpa || !$this->grade_scale) {
            return null;
        }

        $grade = (float) $this->gpa;
        
        if ($this->grade_scale === "1.0") {
            if ($grade >= 97) return "1.00";
            if ($grade >= 94) return "1.25";
            if ($grade >= 91) return "1.50";
            if ($grade >= 88) return "1.75";
            if ($grade >= 85) return "2.00";
            if ($grade >= 82) return "2.25";
            if ($grade >= 79) return "2.50";
            if ($grade >= 76) return "2.75";
            if ($grade >= 75) return "3.00";
            return "5.00";
        } elseif ($this->grade_scale === "4.0") {
            if ($grade >= 96) return "4.00";
            if ($grade >= 90) return "3.50";
            if ($grade >= 85) return "3.00";
            if ($grade >= 80) return "2.50";
            if ($grade >= 75) return "2.00";
            return "1.00";
        }

        return null;
    }

    /**
     * Get the user's initials.
     */
    public function getInitialsAttribute()
    {
        $first = substr($this->first_name ?? 'U', 0, 1);
        $last = substr($this->last_name ?? 'S', 0, 1);
        return strtoupper($first . $last);
    }

    /**
     * Get the profile picture URL.
     */
    public function getProfilePicUrlAttribute()
    {
        if ($this->profile_pic) {
            try {
                // Check if it's a full URL (already on S3/external)
                if (filter_var($this->profile_pic, FILTER_VALIDATE_URL)) {
                    return $this->profile_pic;
                }

                // Get the default disk
                $disk = config('filesystems.default');
                
                // ALWAYS use our custom route for profile pictures.
                // This ensures compatibility across local and cloud storage (like R2/S3)
                // by streaming the file through our server which has the credentials.
                return route('profile-pic.show', ['filename' => basename($this->profile_pic)], false);
            } catch (\Throwable $e) {
                Log::warning("Failed to generate profile pic URL for user {$this->id}: " . $e->getMessage());
                // Fallback to the local route which has its own error handling
                return route('profile-pic.show', ['filename' => basename($this->profile_pic)], false);
            }
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
