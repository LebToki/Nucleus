<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Event;
use App\Models\Delegation;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Auth\Passwords\CanResetPassword;

class User extends Authenticatable implements CanResetPasswordContract
{
    use CanResetPassword, HasFactory, Notifiable;

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function delegations(): HasMany
    {
        return $this->hasMany(Delegation::class, 'assignee_id');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'personal_phone',
        'corporate_phone',
        'secondary_phone',
        'personal_email',
        'corporate_email',
        'secondary_email',
        'personal_website',
        'corporate_website',
        'secondary_website',
        'linkedin',
        'facebook',
        'instagram',
        'x',
        'tiktok',
        'youtube',
        'avatar',
        'role',
        'is_superuser',
        'password',
    ];

    public function hasRole(string|array $roles): bool
    {
        return $this->is_superuser || in_array($this->role, (array) $roles, true);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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
            'is_superuser' => 'boolean',
            'password' => 'hashed',
        ];
    }
}
