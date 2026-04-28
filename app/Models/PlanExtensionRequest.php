<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanExtensionRequest extends Model
{
    protected $fillable = [
        'tenant_id',
        'requested_plan',
        'payment_method',
        'payment_reference',
        'payment_proof_path',
        'payment_notes',
        'status', // pending|approved|rejected
        'notes',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'requested_plan', 'key');
    }
}

