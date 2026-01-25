<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The table associated with the model.
     */
    protected $table = 'login_history';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'login_at',
        'ip_address',
        'user_agent',
        'location',
        'status',
        'failure_reason',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'login_at' => 'datetime',
    ];

    /**
     * Boot function to set default login_at.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->login_at) {
                $model->login_at = now();
            }
        });
    }

    /**
     * Get the user that owns this login record.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include successful logins.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope a query to only include failed logins.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Get browser name from user agent.
     */
    public function getBrowserAttribute(): string
    {
        $agent = $this->user_agent ?? '';
        
        if (str_contains($agent, 'Firefox')) return 'Firefox';
        if (str_contains($agent, 'Chrome')) return 'Chrome';
        if (str_contains($agent, 'Safari')) return 'Safari';
        if (str_contains($agent, 'Edge')) return 'Edge';
        if (str_contains($agent, 'Opera')) return 'Opera';
        
        return 'Unknown';
    }
}
