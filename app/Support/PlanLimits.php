<?php

namespace App\Support;

use App\Models\Booking;

/**
 * Subscription plan limits (vehicle caps, monthly booking caps for Basic).
 */
class PlanLimits
{
    /** Basic plan: max new bookings created per calendar month (admin + customer portal). */
    public const BASIC_MONTHLY_BOOKINGS = 40;

    public static function vehicleLimit(?string $plan): ?int
    {
        return match ($plan) {
            'basic' => 5,
            'standard' => 15,
            'premium' => null,
            default => null,
        };
    }

    /**
     * Max bookings that can be created this month, or null = unlimited.
     */
    public static function monthlyBookingCreationLimit(?string $plan): ?int
    {
        return match ($plan) {
            'basic' => self::BASIC_MONTHLY_BOOKINGS,
            'standard', 'premium' => null,
            default => null,
        };
    }

    public static function monthlyBookingsCreatedCount(int $tenantId): int
    {
        return Booking::where('tenant_id', $tenantId)
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();
    }

    public static function canCreateVehicle(string $plan, int $currentVehicleCount): bool
    {
        $limit = self::vehicleLimit($plan);
        if ($limit === null) {
            return true;
        }

        return $currentVehicleCount < $limit;
    }

    public static function canCreateBooking(string $plan, int $tenantId): bool
    {
        $limit = self::monthlyBookingCreationLimit($plan);
        if ($limit === null) {
            return true;
        }

        return self::monthlyBookingsCreatedCount($tenantId) < $limit;
    }
}
