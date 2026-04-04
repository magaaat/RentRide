<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        return in_array($this->role, [
            'branch_manager',
            'reservation_staff',
            'fleet_maintenance_staff',
            'cashier_billing_staff',
        ], true);
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

    public static function defaultPermissionsForRole(string $role): array
    {
        return match ($role) {
            'branch_manager' => ['bookings.manage', 'customers.manage', 'vehicles.manage', 'maintenance.manage', 'payments.manage', 'reports.view'],
            'reservation_staff' => ['bookings.manage', 'customers.manage'],
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
