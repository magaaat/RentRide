<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'driver_license_front_path',
        'driver_license_back_path',
        'password',
        'role',
        'staff_role',
        'permissions',
        'is_active',
        'tenant_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'permissions' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function isTenantUser(): bool
    {
        return $this->isAdmin() || $this->isStaff();
    }

    public function isStaff(): bool
    {
        return $this->tenant_id !== null
            && ! in_array($this->role, ['super_admin', 'admin', 'customer'], true);
    }

    public function canManageTenantSettings(): bool
    {
        return $this->isAdmin();
    }

    public function canManageStaff(): bool
    {
        return $this->isAdmin();
    }

    public function hasPermission(string $permission): bool
    {
        if (! $this->isPermissionAvailableForCurrentTenant($permission)) {
            return false;
        }

        if ($this->isAdmin()) {
            return true;
        }
        if (! $this->isStaff() || ! $this->is_active) {
            return false;
        }

        return in_array($permission, $this->permissions ?? [], true);
    }

    public static function staffRoles(): array
    {
        return [
            'branch_manager' => 'Branch Manager',
            'reservation_staff' => 'Reservation Staff',
            'fleet_maintenance_staff' => 'Fleet & Maintenance Staff',
            'cashier_billing_staff' => 'Cashier/Billing Staff',
        ];
    }

    public static function staffRolesForTenant(?Tenant $tenant): array
    {
        $roles = self::staffRoles();
        $stored = is_array($tenant?->staff_role_permissions) ? $tenant->staff_role_permissions : [];

        foreach (array_keys($stored) as $roleKey) {
            if (! is_string($roleKey) || $roleKey === '' || isset($roles[$roleKey])) {
                continue;
            }

            $roles[$roleKey] = Str::of($roleKey)
                ->replace(['-', '_'], ' ')
                ->title()
                ->toString();
        }

        return $roles;
    }

    public static function permissionLabels(): array
    {
        return [
            'bookings.manage' => 'Bookings',
            'customers.manage' => 'Customers',
            'vehicles.manage' => 'Vehicles',
            'maintenance.manage' => 'Maintenance',
            'payments.manage' => 'Payments',
            'reports.view' => 'Reports/Analytics',
        ];
    }

    public static function permissionFeatureRequirements(): array
    {
        return [
            'maintenance.manage' => Tenant::FEATURE_MAINTENANCE_TRACKING,
            'payments.manage' => Tenant::FEATURE_PAYMENT_TRACKING,
            'reports.view' => Tenant::FEATURE_SALES_DASHBOARD,
        ];
    }

    public function isPermissionAvailableForCurrentTenant(string $permission): bool
    {
        $feature = self::permissionFeatureRequirements()[$permission] ?? null;
        if ($feature === null) {
            return true;
        }

        return (bool) $this->tenant?->hasFeature($feature);
    }

    public static function permissionAllowedForTenant(string $permission, ?Tenant $tenant): bool
    {
        $feature = self::permissionFeatureRequirements()[$permission] ?? null;
        if ($feature === null) {
            return true;
        }

        return (bool) $tenant?->hasFeature($feature);
    }

    public static function permissionLabelsForTenant(?Tenant $tenant): array
    {
        return collect(self::permissionLabels())
            ->filter(fn ($label, $permission) => self::permissionAllowedForTenant($permission, $tenant))
            ->all();
    }

    public static function sanitizePermissionsForTenant(array $permissions, ?Tenant $tenant): array
    {
        $allowed = array_keys(self::permissionLabelsForTenant($tenant));

        return array_values(array_intersect($permissions, $allowed));
    }

    public static function defaultPermissionsForRole(string $role): array
    {
        return match ($role) {
            'branch_manager' => ['bookings.manage', 'customers.manage', 'vehicles.manage', 'maintenance.manage', 'payments.manage', 'reports.view'],
            'reservation_staff' => ['bookings.manage', 'customers.manage', 'payments.manage'],
            'fleet_maintenance_staff' => ['vehicles.manage', 'maintenance.manage'],
            'cashier_billing_staff' => ['payments.manage'],
            default => [],
        };
    }

    public function hasDriverLicensePhotos(): bool
    {
        return ! empty($this->driver_license_front_path) && ! empty($this->driver_license_back_path);
    }
}
