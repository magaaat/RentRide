<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    protected $fillable = [
        'tenant_id',
        'vehicle_name',
        'vehicle_type',
        'brand',
        'plate_number',
        'price_per_day',
        'status',
        'description',
        'maintenance_issue',
        'maintenance_severity',
        'maintenance_reported_at',
        'maintenance_target_fix_at',
        'maintenance_fixed_at',
        'maintenance_cost_estimate',
        'image',
    ];

    protected $casts = [
        'maintenance_reported_at' => 'date',
        'maintenance_target_fix_at' => 'date',
        'maintenance_fixed_at' => 'date',
        'maintenance_cost_estimate' => 'decimal:2',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}

