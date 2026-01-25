<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'asset_code',
        'name',
        'category_id',
        'description',
        'serial_number',
        'purchase_date',
        'purchase_price',
        'current_value',
        'supplier',
        'warranty_expiry',
        'location',
        'assigned_to_ministry_id',
        'condition_status',
        'status',
        'disposal_date',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiry' => 'date',
        'disposal_date' => 'date',
        'purchase_price' => 'decimal:2',
        'current_value' => 'decimal:2',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class, 'category_id');
    }

    public function assignedToMinistry(): BelongsTo
    {
        return $this->belongsTo(Ministry::class, 'assigned_to_ministry_id');
    }

    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(AssetMaintenance::class);
    }
}
