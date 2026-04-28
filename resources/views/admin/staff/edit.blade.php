@extends('layouts.app')

@section('title', 'Edit Staff')

@section('content')
<div class="mx-auto max-w-3xl">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold">Edit staff</h1>
        <p class="text-sm text-slate-500">Update role, status, permissions, and credentials.</p>
    </div>

    <div class="rr-panel-elevated p-6">
        <form method="POST" action="{{ route('admin.staff.update', $staffUser) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="rr-label">Name</label>
                    <input type="text" name="name" value="{{ old('name', $staffUser->name) }}" required class="rr-input">
                </div>
                <div>
                    <label class="rr-label">Email</label>
                    <input type="email" name="email" value="{{ old('email', $staffUser->email) }}" required class="rr-input">
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="rr-label">Staff role</label>
                    <select name="role" id="role" class="rr-input">
                        @foreach($roleLabels as $key => $label)
                            <option value="{{ $key }}" @selected(old('role', $staffUser->staff_role ?: $staffUser->role) === $key)>{{ $label }}</option>
                        @endforeach
                        <option value="__custom__" @selected(old('role') === '__custom__')>+ Create new role</option>
                    </select>
                    <div id="custom-role-wrapper" class="mt-2" style="display:none;">
                        <input
                            type="text"
                            name="custom_role_name"
                            id="custom_role_name"
                            value="{{ old('custom_role_name') }}"
                            class="rr-input"
                            placeholder="e.g. documents staff"
                        >
                        <p class="mt-1 text-xs text-slate-500">New role will be saved for this tenant and reusable later.</p>
                    </div>
                </div>
                <div class="flex items-end pb-1">
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $staffUser->is_active)) class="size-4 rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                        Active
                    </label>
                </div>
            </div>

            <div>
                <label class="rr-label">Permissions</label>
                @php($selectedPerms = old('permissions', $staffUser->permissions ?? []))
                <div id="permission-grid" class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                    @foreach($permissionLabels as $perm => $label)
                        <label class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm">
                            <input type="checkbox" name="permissions[]" value="{{ $perm }}" @checked(in_array($perm, $selectedPerms, true)) class="size-4 rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="rr-label">New password (optional)</label>
                    <input type="password" name="password" class="rr-input">
                </div>
                <div>
                    <label class="rr-label">Confirm password</label>
                    <input type="password" name="password_confirmation" class="rr-input">
                </div>
            </div>

            <div class="flex gap-3">
                <button class="rr-btn-primary inline-flex items-center rounded-lg px-5 py-2.5 text-sm font-semibold">Save staff</button>
                <a href="{{ route('admin.staff.index') }}" class="rr-btn-secondary inline-flex items-center rounded-lg px-5 py-2.5 text-sm font-semibold">Back</a>
            </div>
        </form>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const roleSelect = document.getElementById('role');
        const permissionGrid = document.getElementById('permission-grid');
        const roleDefaults = @json($defaultPermissionsByRole);

        if (!roleSelect || !permissionGrid) return;

        const applyDefaults = (roleKey) => {
            const defaults = roleDefaults[roleKey] || [];
            const checkboxes = permissionGrid.querySelectorAll('input[type="checkbox"][name="permissions[]"]');
            checkboxes.forEach((box) => {
                box.checked = defaults.includes(box.value);
            });
        };

        roleSelect.addEventListener('change', function () {
            applyDefaults(roleSelect.value);
        });

        const customRoleWrapper = document.getElementById('custom-role-wrapper');
        const customRoleInput = document.getElementById('custom_role_name');
        const syncCustomRole = () => {
            const isCustom = roleSelect.value === '__custom__';
            if (customRoleWrapper) customRoleWrapper.style.display = isCustom ? '' : 'none';
            if (customRoleInput) customRoleInput.required = !!isCustom;
        };

        roleSelect.addEventListener('change', syncCustomRole);
        syncCustomRole();
    });
</script>
@endsection

