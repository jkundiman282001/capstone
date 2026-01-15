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
            // Convert 1.0 scale to percentage range
            if ($grade <= 1.0) return "99-100%";
            if ($grade <= 1.25) return "96-98%";
            if ($grade <= 1.5) return "93-95%";
            if ($grade <= 1.75) return "90-92%";
            if ($grade <= 2.0) return "87-89%";
            if ($grade <= 2.25) return "84-86%";
            if ($grade <= 2.5) return "81-83%";
            if ($grade <= 2.75) return "78-80%";
            if ($grade <= 3.0) return "75-77%";
            return "Below 75%";
        } elseif ($this->grade_scale === "4.0") {
            // Convert 4.0 scale to percentage range
            if ($grade >= 3.8) return "97-100%";
            if ($grade >= 3.4) return "93-96%";
            if ($grade >= 3.0) return "89-92%";
            if ($grade >= 2.6) return "85-88%";
            if ($grade >= 2.2) return "81-84%";
            if ($grade >= 1.8) return "77-80%";
            if ($grade >= 1.5) return "75-76%";
            return "Below 75%";
        } elseif ($this->grade_scale === "75-100") {
            // Convert percentage to 1.0 scale equivalent
            if ($grade >= 99) return "1.0 Equiv.";
            if ($grade >= 96) return "1.25 Equiv.";
            if ($grade >= 93) return "1.5 Equiv.";
            if ($grade >= 90) return "1.75 Equiv.";
            if ($grade >= 87) return "2.0 Equiv.";
            if ($grade >= 84) return "2.25 Equiv.";
            if ($grade >= 81) return "2.5 Equiv.";
            if ($grade >= 78) return "2.75 Equiv.";
            if ($grade >= 75) return "3.0 Equiv.";
            return "5.0 Equiv.";
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
