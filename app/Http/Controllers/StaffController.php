<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffController extends Controller
{
    public function index()
    {
        $admin = Auth::user();
        abort_unless($admin && $admin->canManageStaff(), 403);

        $staff = User::query()
            ->where('tenant_id', $admin->tenant_id)
            ->whereIn('role', array_keys(User::staffRoles()))
            ->orderBy('name')
            ->get();

        $roleLabels = User::staffRoles();
        $permissionLabels = User::permissionLabels();
        $roleTemplates = $this->resolveRoleTemplates($admin->tenant);

        $staffCountByRole = User::query()
            ->where('tenant_id', $admin->tenant_id)
            ->whereIn('role', array_keys($roleLabels))
            ->selectRaw('role, COUNT(*) as total')
            ->groupBy('role')
            ->pluck('total', 'role')
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
            'roleLabels' => User::staffRoles(),
            'permissionLabels' => User::permissionLabels(),
            'defaultPermissionsByRole' => $roleTemplates,
        ]);
    }

    public function store(Request $request)
    {
        $admin = Auth::user();
        abort_unless($admin && $admin->canManageStaff(), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'in:' . implode(',', array_keys(User::staffRoles()))],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'in:' . implode(',', array_keys(User::permissionLabels()))],
            'is_active' => ['nullable', 'boolean'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $roleTemplates = $this->resolveRoleTemplates($admin->tenant);
        $permissions = $data['permissions'] ?? ($roleTemplates[$data['role']] ?? User::defaultPermissionsForRole($data['role']));
        if ($data['role'] !== 'branch_manager') {
            $permissions = array_values($permissions);
        }

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $data['role'],
            'staff_role' => $data['role'],
            'tenant_id' => $admin->tenant_id,
            'permissions' => $permissions,
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff created successfully.');
    }

    public function edit(User $staff)
    {
        $admin = Auth::user();
        abort_unless($admin && $admin->canManageStaff(), 403);
        abort_unless((int) $staff->tenant_id === (int) $admin->tenant_id && $staff->isStaff(), 404);

        $roleTemplates = $this->resolveRoleTemplates($admin->tenant);

        return view('admin.staff.edit', [
            'staffUser' => $staff,
            'roleLabels' => User::staffRoles(),
            'permissionLabels' => User::permissionLabels(),
            'defaultPermissionsByRole' => $roleTemplates,
        ]);
    }

    public function update(Request $request, User $staff)
    {
        $admin = Auth::user();
        abort_unless($admin && $admin->canManageStaff(), 403);
        abort_unless((int) $staff->tenant_id === (int) $admin->tenant_id && $staff->isStaff(), 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $staff->id],
            'role' => ['required', 'in:' . implode(',', array_keys(User::staffRoles()))],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'in:' . implode(',', array_keys(User::permissionLabels()))],
            'is_active' => ['nullable', 'boolean'],
            'password' => ['nullable', 'confirmed', 'min:8'],
        ]);

        $permissions = $data['permissions'] ?? [];
        if ($data['role'] !== 'branch_manager') {
            $permissions = array_values($permissions);
        }

        $staff->name = $data['name'];
        $staff->email = $data['email'];
        $staff->role = $data['role'];
        $staff->staff_role = $data['role'];
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

    public function updateRolePermissions(Request $request)
    {
        $admin = Auth::user();
        abort_unless($admin && $admin->canManageStaff(), 403);

        $roleLabels = User::staffRoles();
        $permissionLabels = User::permissionLabels();

        $data = $request->validate([
            'role' => ['required', 'in:' . implode(',', array_keys($roleLabels))],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'in:' . implode(',', array_keys($permissionLabels))],
        ]);

        $role = $data['role'];
        $permissions = array_values($data['permissions'] ?? []);

        $tenant = $admin->tenant;
        abort_unless($tenant, 404);

        $templates = $this->resolveRoleTemplates($tenant);
        $templates[$role] = $permissions;
        $tenant->staff_role_permissions = $templates;
        $tenant->save();

        User::query()
            ->where('tenant_id', $admin->tenant_id)
            ->where('role', $role)
            ->update(['permissions' => $permissions]);

        return redirect()->route('admin.staff.index')
            ->with('success', 'Updated role permissions and applied to all "' . ($roleLabels[$role] ?? $role) . '" staff.');
    }

    protected function resolveRoleTemplates(?Tenant $tenant): array
    {
        $roles = array_keys(User::staffRoles());
        $stored = $tenant?->staff_role_permissions;
        $stored = is_array($stored) ? $stored : [];

        $templates = [];
        foreach ($roles as $role) {
            $templates[$role] = array_values($stored[$role] ?? User::defaultPermissionsForRole($role));
        }

        return $templates;
    }
}

