<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetMaintenance extends Model
{
    use HasFactory;

    protected $table = 'asset_maintenance';

    protected $fillable = [
        'asset_id',
        'maintenance_type',
        'description',
        'cost',
        'maintenance_date',
        'next_maintenance_date',
        'performed_by',
        'vendor',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'maintenance_date'      => 'date',
        'next_maintenance_date' => 'date',
        'cost'                  => 'decimal:2',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public const TYPES = [
        'repair'     => 'Repair',
        'service'    => 'Service',
        'inspection' => 'Inspection',
        'upgrade'    => 'Upgrade',
        'cleaning'   => 'Cleaning',
    ];
}
