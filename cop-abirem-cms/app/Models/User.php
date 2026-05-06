<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'member_id',
        'is_active',
        'last_login_at',
        'last_login_ip',
        // must_change_password intentionally excluded — set only via explicit assignment
    ];

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
            'password' => 'hashed',
            'is_active' => 'boolean',
            'must_change_password' => 'boolean',
            'last_login_at' => 'datetime',
            'locked_until' => 'datetime',
        ];
    }

    /**
     * Get the role associated with the user.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the member profile associated with the user.
     */
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Check if user has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        if (!$this->role) {
            return false;
        }

        return $this->role->permissions->contains('slug', $permission);
    }

    /**
     * Check if user has any of the given permissions.
     */
    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if user is a super admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role && $this->role->slug === 'super-admin';
    }

    /**
     * Check if user is an admin (any admin role).
     */
    public function isAdmin(): bool
    {
        if (!$this->role) {
            return false;
        }

        return in_array($this->role->slug, \App\Helpers\RoleHelper::ADMIN_PANEL_SLUGS);
    }

    /**
     * Check if user has a linked member profile.
     */
    public function hasMemberProfile(): bool
    {
        return $this->member_id !== null;
    }

    /**
     * Get activity logs for this user.
     */
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Get login history for this user.
     */
    public function loginHistory()
    {
        return $this->hasMany(LoginHistory::class);
    }

    /**
     * Check if user account is locked.
     */
    public function isLocked(): bool
    {
        if (!$this->locked_until) {
            return false;
        }
        
        return $this->locked_until > now();
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(string $role): bool
    {
        if (!$this->role) {
            return false;
        }

        return $this->role->slug === $role;
    }

    /**
     * Send the password reset notification using the church-branded email.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
