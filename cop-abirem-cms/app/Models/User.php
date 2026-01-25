<?php

namespace App\Models;

use App\Traits\HasRoles;
use App\Traits\Auditable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasRoles, Auditable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'member_id',
        'is_active',
        'must_change_password',
        'last_login_at',
        'last_login_ip',
        'login_attempts',
        'locked_until',
        'two_factor_enabled',
        'two_factor_secret',
        'created_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'must_change_password' => 'boolean',
        'two_factor_enabled' => 'boolean',
        'last_login_at' => 'datetime',
        'locked_until' => 'datetime',
    ];

    /**
     * Get the role associated with the user.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the member profile linked to this user (if any).
     */
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Get the user who created this account.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get users created by this user.
     */
    public function createdUsers()
    {
        return $this->hasMany(User::class, 'created_by');
    }

    /**
     * Get the login history for this user.
     */
    public function loginHistory()
    {
        return $this->hasMany(LoginHistory::class)->orderBy('login_at', 'desc');
    }

    /**
     * Get the activity logs for this user.
     */
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class)->orderBy('created_at', 'desc');
    }

    /**
     * Check if user account is locked.
     */
    public function isLocked(): bool
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    /**
     * Lock the user account for specified minutes.
     */
    public function lockAccount(int $minutes = 15): void
    {
        $this->update([
            'locked_until' => now()->addMinutes($minutes),
        ]);
    }

    /**
     * Unlock the user account.
     */
    public function unlockAccount(): void
    {
        $this->update([
            'locked_until' => null,
            'login_attempts' => 0,
        ]);
    }

    /**
     * Increment failed login attempts.
     */
    public function incrementLoginAttempts(): void
    {
        $this->increment('login_attempts');
        
        // Lock after 5 failed attempts
        if ($this->login_attempts >= 5) {
            $this->lockAccount(15);
        }
    }

    /**
     * Reset login attempts on successful login.
     */
    public function resetLoginAttempts(): void
    {
        $this->update([
            'login_attempts' => 0,
            'locked_until' => null,
        ]);
    }

    /**
     * Record successful login.
     */
    public function recordLogin(string $ip = null, string $userAgent = null): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip,
            'login_attempts' => 0,
            'locked_until' => null,
        ]);

        // Log to login history
        $this->loginHistory()->create([
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'status' => 'success',
        ]);
    }

    /**
     * Record failed login.
     */
    public function recordFailedLogin(string $ip = null, string $userAgent = null, string $reason = null): void
    {
        $this->incrementLoginAttempts();

        $this->loginHistory()->create([
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'status' => 'failed',
            'failure_reason' => $reason,
        ]);
    }

    /**
     * Scope a query to only include active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive users.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope a query to filter by role.
     */
    public function scopeWithRole($query, $roleSlug)
    {
        return $query->whereHas('role', function ($q) use ($roleSlug) {
            $q->where('slug', $roleSlug);
        });
    }

    /**
     * Get the user's full name with title (if member).
     */
    public function getFullNameAttribute(): string
    {
        if ($this->member) {
            return trim("{$this->member->title} {$this->name}");
        }
        return $this->name;
    }

    /**
     * Check if user is an administrator.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is an elder.
     */
    public function isElder(): bool
    {
        return $this->hasRole('elder');
    }

    /**
     * Check if user can access admin panel.
     */
    public function canAccessAdmin(): bool
    {
        return !$this->hasRole('member');
    }
}
