<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    protected $fillable = [
        'tenant_id',
        'vehicle_id',
        'customer_id',
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
        static::created(function (Booking $booking) {
            $booking->loadMissing('vehicle');
            $amount = $booking->calculateTotalAmount();
            Payment::query()->firstOrCreate(
                ['booking_id' => $booking->id],
                [
                    'tenant_id' => $booking->tenant_id,
                    'amount' => $amount,
                    'payment_method' => 'pending',
                    'payment_status' => 'pending',
                    'payment_date' => null,
                ]
            );
        });
    }

    /**
     * Total rental price: rental day difference (minimum 1 day) × vehicle daily rate.
     */
    public function calculateTotalAmount(): float
    {
        $this->loadMissing('vehicle');
        if (! $this->vehicle) {
            return 0.0;
        }

        $days = max(1, $this->start_date->diffInDays($this->end_date));

        return round(max(0, $days) * (float) $this->vehicle->price_per_day, 2);
    }

    public function hasPaidPayment(): bool
    {
        return $this->payment && $this->payment->payment_status === 'paid';
    }

    /**
     * Another confirmed booking on the same vehicle overlaps these dates (excluding this row).
     */
    public function hasOverlappingConfirmedBooking(): bool
    {
        return static::query()
            ->where('tenant_id', $this->tenant_id)
            ->where('vehicle_id', $this->vehicle_id)
            ->where('status', 'confirmed')
            ->where('id', '!=', $this->id)
            ->where(function ($q) {
                $q->whereBetween('start_date', [$this->start_date, $this->end_date])
                    ->orWhereBetween('end_date', [$this->start_date, $this->end_date])
                    ->orWhere(function ($inner) {
                        $inner->where('start_date', '<=', $this->start_date)
                            ->where('end_date', '>=', $this->end_date);
                    });
            })
            ->exists();
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }
}

