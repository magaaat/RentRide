<?php

namespace App\Support;

use App\Models\Booking;
use App\Models\SubscriptionPlan;

/**
 * Subscription plan limits (vehicle caps, monthly booking caps for Basic).
 * Limits use **feature_tier** (basic / standard / premium) on the plan row, not the marketing tier label or key.
 */
class PlanLimits
{
    /** Basic plan: max new bookings created per calendar month (admin + customer portal). */
    public const BASIC_MONTHLY_BOOKINGS = 40;

    /**
     * Resolve feature tier from a subscription_plans.key stored on the tenant.
     */
    public static function resolveTier(?string $planKey): string
    {
        if ($planKey === null || $planKey === '') {
            return 'basic';
        }

        $plan = SubscriptionPlan::query()->where('key', $planKey)->first();
        if ($plan) {
            $ft = $plan->feature_tier;
            if ($ft && in_array($ft, ['basic', 'standard', 'premium'], true)) {
                return $ft;
            }
            $t = $plan->tier;
            if ($t && in_array($t, ['basic', 'standard', 'premium'], true)) {
                return $t;
            }
        }

        return in_array($planKey, ['basic', 'standard', 'premium'], true) ? $planKey : 'basic';
    }

    public static function vehicleLimit(?string $planKey): ?int
    {
        $tier = self::resolveTier($planKey);

        return match ($tier) {
            'basic' => 5,
            'standard' => 15,
            'premium' => null,
            default => null,
        };
    }

    /**
     * Max bookings that can be created this month, or null = unlimited.
     */
    public static function monthlyBookingCreationLimit(?string $planKey): ?int
    {
        $tier = self::resolveTier($planKey);

        return match ($tier) {
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
