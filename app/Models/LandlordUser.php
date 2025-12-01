<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\UserType;
use App\Notifications\TenantResetPasswordNotification;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

class LandlordUser extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, TwoFactorAuthenticatable, HasRoles;

    protected $table = 'users';

    protected $guard_name = 'landlord';

    /**
     * Resolve the connection for landlord users (always central DB).
     */
    public function getConnectionName()
    {
        return central_connection();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type',
        'school_id',
        'approval_status',
        'profile_photo',
        'is_active',
        'phone',
        'gender',
        'date_of_birth',
        'address',
        'qualification',
        'specialization',
        'emergency_contact_name',
        'emergency_contact_phone',
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
            'user_type' => UserType::class,
            'approved_at' => 'datetime',
            'suspended_at' => 'datetime',
            'expelled_at' => 'datetime',
            'registration_data' => 'array',
            'is_active' => 'boolean',
        ];
    }
}
