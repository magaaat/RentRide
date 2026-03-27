<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    protected $fillable = [
        'tenant_id',
        'plan_name',
        'price',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (Subscription $subscription) {
            // Automatically set start and end dates if not provided:
            // start_date = today, end_date = one month from today.
            if (!$subscription->start_date) {
                $subscription->start_date = now();
            }

            if (!$subscription->end_date) {
                $subscription->end_date = $subscription->start_date->copy()->addMonth();
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}

