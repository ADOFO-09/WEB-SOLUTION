<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait Auditable
{
    /**
     * Fields to exclude from logging.
     */
    protected static array $excludedFields = [
        'password',
        'remember_token',
        'two_factor_secret',
        'updated_at',
        'created_at',
    ];

    /**
     * Boot the auditable trait.
     */
    protected static function bootAuditable(): void
    {
        // Log when a model is created
        static::created(function ($model) {
            static::logActivity('created', $model);
        });

        // Log when a model is updated
        static::updated(function ($model) {
            static::logActivity('updated', $model, $model->getOriginal());
        });

        // Log when a model is deleted
        static::deleted(function ($model) {
            static::logActivity('deleted', $model);
        });
    }

    /**
     * Log the activity.
     */
    protected static function logActivity(string $action, $model, ?array $oldValues = null): void
    {
        // Skip if no authenticated user (e.g., during seeding)
        if (!auth()->check()) {
            return;
        }

        // Filter out excluded fields
        $newValues = $action !== 'deleted' 
            ? static::filterValues($model->getAttributes()) 
            : null;

        $oldValues = $oldValues 
            ? static::filterValues($oldValues) 
            : null;

        // Only log updates if there are actual changes
        if ($action === 'updated' && $newValues == $oldValues) {
            return;
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Filter out excluded fields from values.
     */
    protected static function filterValues(array $values): array
    {
        return array_diff_key($values, array_flip(static::$excludedFields));
    }

    /**
     * Get activity logs for this model.
     */
    public function activityLogs()
    {
        return ActivityLog::where('model_type', get_class($this))
                         ->where('model_id', $this->id)
                         ->orderBy('created_at', 'desc')
                         ->get();
    }

    /**
     * Disable auditing temporarily.
     */
    public static function withoutAuditing(callable $callback)
    {
        $dispatcher = static::getEventDispatcher();
        static::unsetEventDispatcher();
        
        try {
            return $callback();
        } finally {
            static::setEventDispatcher($dispatcher);
        }
    }
}
