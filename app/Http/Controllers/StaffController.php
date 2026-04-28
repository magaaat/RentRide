<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStaffRequest;
use App\Http\Requests\UpdateStaffRequest;
use App\Http\Requests\UpdateStaffRolePermissionsRequest;
use App\Mail\StaffWelcomeMail;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class StaffController extends Controller
{
    protected const CUSTOM_STAFF_ROLE = 'custom_staff';

    public function index()
    {
        $admin = Auth::user();
        abort_unless($admin && $admin->canManageStaff(), 403);

        $roleLabels = User::staffRolesForTenant($admin->tenant);

        $staff = User::query()
            ->where('tenant_id', $admin->tenant_id)
            ->whereNotIn('role', ['super_admin', 'admin', 'customer'])
            ->orderBy('name')
            ->get();

        $permissionLabels = User::permissionLabelsForTenant($admin->tenant);
        $roleTemplates = $this->resolveRoleTemplates($admin->tenant);

        $staffCountByRole = User::query()
            ->where('tenant_id', $admin->tenant_id)
            ->whereNotIn('role', ['super_admin', 'admin', 'customer'])
            ->selectRaw('COALESCE(NULLIF(staff_role, \'\'), role) as role_key, COUNT(*) as total')
            ->groupBy('role_key')
            ->pluck('total', 'role_key')
            ->toArray();

        return view('admin.staff.index', [
            'staff' => $staff,
            'roleLabels' => $roleLabels,
            'permissionLabels' => $permissionLabels,
            'roleTemplates' => $roleTemplates,
            'staffCountByRole' => $staffCountByRole,
        ]);
    }

    public function create()
    {
        $admin = Auth::user();
        abort_unless($admin?->canManageStaff(), 403);

        $roleTemplates = $this->resolveRoleTemplates($admin->tenant);

        return view('admin.staff.create', [
            'roleLabels' => User::staffRolesForTenant($admin->tenant),
            'permissionLabels' => User::permissionLabelsForTenant($admin->tenant),
            'defaultPermissionsByRole' => $roleTemplates,
        ]);
    }

    public function store(StoreStaffRequest $request)
    {
        $admin = Auth::user();
        abort_unless($admin && $admin->canManageStaff(), 403);

        $data = $request->validated();
        $resolvedRole = $this->resolveRequestedRole($data, $admin->tenant);

        $generatePassword = $request->boolean('generate_password');

        $plainPassword = $generatePassword
            ? Str::password(12)
            : $data['password'];

        $roleTemplates = $this->resolveRoleTemplates($admin->tenant);
        $permissions = $data['permissions'] ?? ($roleTemplates[$resolvedRole['staff_role']] ?? User::defaultPermissionsForRole($resolvedRole['staff_role']));
        if ($resolvedRole['staff_role'] !== 'branch_manager') {
            $permissions = array_values($permissions);
        }
        $permissions = User::sanitizePermissionsForTenant($permissions, $admin->tenant);

        $staffUser = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $plainPassword,
            'role' => $resolvedRole['role'],
            'staff_role' => $resolvedRole['staff_role'],
            'tenant_id' => $admin->tenant_id,
            'permissions' => $permissions,
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);

        $tenant = $admin->tenant;
        if ($tenant) {
            try {
                Mail::to($staffUser->email)->send(
                    new StaffWelcomeMail($tenant, $staffUser, $plainPassword)
                );
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $message = $generatePassword
            ? 'Staff created. A secure password was generated and sent to their email.'
            : 'Staff created successfully.';

        return redirect()->route('admin.staff.index')->with('success', $message);
    }

    public function edit(User $staff)
    {
        $admin = Auth::user();
        abort_unless($admin && $admin->canManageStaff(), 403);
        abort_unless((int) $staff->tenant_id === (int) $admin->tenant_id && $staff->isStaff(), 404);

        $roleTemplates = $this->resolveRoleTemplates($admin->tenant);

        return view('admin.staff.edit', [
            'staffUser' => $staff,
            'roleLabels' => User::staffRolesForTenant($admin->tenant),
            'permissionLabels' => User::permissionLabelsForTenant($admin->tenant),
            'defaultPermissionsByRole' => $roleTemplates,
        ]);
    }

    public function update(UpdateStaffRequest $request, User $staff)
    {
        $admin = Auth::user();
        abort_unless($admin && $admin->canManageStaff(), 403);
        abort_unless((int) $staff->tenant_id === (int) $admin->tenant_id && $staff->isStaff(), 404);

        $data = $request->validated();
        $resolvedRole = $this->resolveRequestedRole($data, $admin->tenant);

        $permissions = $data['permissions'] ?? [];
        if ($resolvedRole['staff_role'] !== 'branch_manager') {
            $permissions = array_values($permissions);
        }
        $permissions = User::sanitizePermissionsForTenant($permissions, $admin->tenant);

        $staff->name = $data['name'];
        $staff->email = $data['email'];
        $staff->role = $resolvedRole['role'];
        $staff->staff_role = $resolvedRole['staff_role'];
        $staff->permissions = $permissions;
        $staff->is_active = (bool) ($data['is_active'] ?? false);
        if (! empty($data['password'])) {
            $staff->password = $data['password'];
        }
        $staff->save();

        return redirect()->route('admin.staff.index')->with('success', 'Staff updated.');
    }

    public function destroy(User $staff)
    {
        $admin = Auth::user();
        abort_unless($admin && $admin->canManageStaff(), 403);
        abort_unless((int) $staff->tenant_id === (int) $admin->tenant_id && $staff->isStaff(), 404);

        $staff->delete();

        return redirect()->route('admin.staff.index')->with('success', 'Staff deleted.');
    }

    public function updateRolePermissions(UpdateStaffRolePermissionsRequest $request)
    {
        $admin = Auth::user();
        abort_unless($admin && $admin->canManageStaff(), 403);

        $roleLabels = User::staffRolesForTenant($admin->tenant);
        $permissionLabels = User::permissionLabelsForTenant($admin->tenant);

        $data = $request->validated();

        $role = $data['role'];
        $tenant = $admin->tenant;
        abort_unless($tenant, 404);
        if (! array_key_exists($role, $roleLabels)) {
            return redirect()->route('admin.staff.index')
                ->withErrors(['role' => 'Invalid staff role selected.']);
        }
        $permissions = User::sanitizePermissionsForTenant(
            array_values($data['permissions'] ?? []),
            $tenant
        );

        $templates = $this->resolveRoleTemplates($tenant);
        $templates[$role] = $permissions;
        $tenant->staff_role_permissions = $templates;
        $tenant->save();

        User::query()
            ->where('tenant_id', $admin->tenant_id)
            ->where(function ($q) use ($role) {
                $q->where('staff_role', $role)
                    ->orWhere(function ($inner) use ($role) {
                        $inner->whereNull('staff_role')->where('role', $role);
                    });
            })
            ->update(['permissions' => $permissions]);

        return redirect()->route('admin.staff.index')
            ->with('success', 'Updated role permissions and applied to all "' . ($roleLabels[$role] ?? $role) . '" staff.');
    }

    protected function resolveRoleTemplates(?Tenant $tenant): array
    {
        $roles = array_keys(User::staffRolesForTenant($tenant));
        $stored = $tenant?->staff_role_permissions;
        $stored = is_array($stored) ? $stored : [];

        $templates = [];
        foreach ($roles as $role) {
            $templates[$role] = User::sanitizePermissionsForTenant(
                array_values($stored[$role] ?? User::defaultPermissionsForRole($role)),
                $tenant
            );
        }

        return $templates;
    }

    protected function resolveRequestedRole(array $data, ?Tenant $tenant): array
    {
        $selectedRole = trim((string) ($data['role'] ?? ''));
        if ($selectedRole === '__custom__') {
            $customRaw = trim((string) ($data['custom_role_name'] ?? ''));
            if ($customRaw === '') {
                throw ValidationException::withMessages([
                    'custom_role_name' => 'Custom role name is required.',
                ]);
            }

            $roleKey = Str::of($customRaw)
                ->lower()
                ->replaceMatches('/[^a-z0-9]+/', '_')
                ->trim('_')
                ->toString();

            if ($roleKey === '') {
                throw ValidationException::withMessages([
                    'custom_role_name' => 'Custom role name must contain letters or numbers.',
                ]);
            }

            if ($tenant) {
                $templates = is_array($tenant->staff_role_permissions) ? $tenant->staff_role_permissions : [];
                if (! array_key_exists($roleKey, $templates)) {
                    $templates[$roleKey] = User::sanitizePermissionsForTenant(
                        User::defaultPermissionsForRole('reservation_staff'),
                        $tenant
                    );
                    $tenant->staff_role_permissions = $templates;
                    $tenant->save();
                }
            }

            return [
                'role' => self::CUSTOM_STAFF_ROLE,
                'staff_role' => $roleKey,
            ];
        }

        $allowedRoles = User::staffRolesForTenant($tenant);
        if (! array_key_exists($selectedRole, $allowedRoles)) {
            throw ValidationException::withMessages([
                'role' => 'Invalid staff role selected.',
            ]);
        }

        return [
            'role' => $selectedRole,
            'staff_role' => $selectedRole,
        ];
    }
}

