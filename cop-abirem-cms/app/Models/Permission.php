<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'slug',
        'module',
        'description',
    ];

    /**
     * Get the roles that have this permission.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permission')
                    ->withTimestamps();
    }

    /**
     * Scope a query to filter by module.
     */
    public function scopeModule($query, string $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Get permissions grouped by module.
     */
    public static function getGroupedByModule()
    {
        return static::orderBy('module')
                    ->orderBy('name')
                    ->get()
                    ->groupBy('module');
    }

    /**
     * Get all available modules.
     */
    public static function getModules(): array
    {
        return static::distinct()->pluck('module')->toArray();
    }

    /**
     * Find permission by slug.
     */
    public static function findBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->first();
    }
}
