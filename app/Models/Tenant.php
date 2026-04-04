<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\PlanExtensionRequest;

class Tenant extends Model
{
    protected $fillable = [
        'company_name',
        'slug',
        'owner_name',
        'email',
        'status',
        'domain',
        'is_domain_active',
        'phone',
        'address',
        'theme',
        'logo_path',
        'staff_role_permissions',
        'subscription_plan',
        'subscription_expiry',
        'is_featured',
    ];

    protected $casts = [
        'is_domain_active' => 'boolean',
        'is_featured' => 'boolean',
        'subscription_expiry' => 'date',
        'staff_role_permissions' => 'array',
    ];

    /**
     * Subscription feature gating.
     *
     * Use these feature keys across controllers/views/middleware.
     */
    public const FEATURE_PAYMENT_TRACKING = 'payment_tracking';
    public const FEATURE_SALES_DASHBOARD = 'sales_dashboard';
    public const FEATURE_ADVANCED_ANALYTICS = 'advanced_analytics';
    public const FEATURE_MAINTENANCE_TRACKING = 'maintenance_tracking';
    public const FEATURE_AUTO_NOTIFICATIONS = 'auto_notifications';
    public const FEATURE_FEATURED_LISTING = 'featured_listing';
    /** Standard & Premium: booking calendar view */
    public const FEATURE_BOOKING_CALENDAR = 'booking_calendar';

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function planExtensionRequests(): HasMany
    {
        return $this->hasMany(PlanExtensionRequest::class);
    }

    /**
     * Full URL to this tenant's login page (subdomain / assigned domain + APP_URL port).
     */
    public function tenantLoginUrl(): ?string
    {
        if (! $this->domain) {
            return null;
        }

        $scheme = parse_url((string) config('app.url'), PHP_URL_SCHEME) ?: 'http';
        $port = parse_url((string) config('app.url'), PHP_URL_PORT);
        $portSegment = $port ? ':' . $port : '';

        return $scheme . '://' . $this->domain . $portSegment . '/login';
    }

    public function hasFeature(string $feature): bool
    {
        $plan = $this->subscription_plan ?? 'basic';

        $planFeatures = [
            'basic' => [
                // Basic features are the default. We intentionally keep this list small
                // and only gate features that require Standard/Premium.
            ],
            'standard' => [
                self::FEATURE_PAYMENT_TRACKING,
                self::FEATURE_SALES_DASHBOARD,
                self::FEATURE_BOOKING_CALENDAR,
            ],
            'premium' => [
                self::FEATURE_PAYMENT_TRACKING,
                self::FEATURE_SALES_DASHBOARD,
                self::FEATURE_BOOKING_CALENDAR,
                self::FEATURE_ADVANCED_ANALYTICS,
                self::FEATURE_MAINTENANCE_TRACKING,
                self::FEATURE_AUTO_NOTIFICATIONS,
                self::FEATURE_FEATURED_LISTING,
            ],
        ];

        return in_array($feature, $planFeatures[$plan] ?? [], true);
    }
}

