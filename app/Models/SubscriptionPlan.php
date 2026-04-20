<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'key',
        'name',
        'tier',
        'feature_tier',
        'base_price',
        'billing_period',
        'currency',
        'discount_type',
        'discount_value',
        'features',
        'is_active',
        'show_on_landing',
        'sort_order',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'features' => 'array',
        'is_active' => 'boolean',
        'show_on_landing' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function discountedPrice(): float
    {
        $base = (float) $this->base_price;
        $type = $this->discount_type ?? 'none';
        $value = (float) ($this->discount_value ?? 0);

        if ($type === 'percent') {
            $base = $base * (1 - ($value / 100));
        } elseif ($type === 'fixed') {
            $base = $base - $value;
        }

        return max(0, round($base, 2));
    }
}

