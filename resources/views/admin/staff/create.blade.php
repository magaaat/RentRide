@extends('layouts.app')

@section('title', 'Add Staff')

@section('content')
<div class="mx-auto max-w-3xl">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold">Add staff</h1>
        <p class="text-sm text-slate-500">Create a tenant staff account and set role permissions.</p>
    </div>

    <div class="rr-panel-elevated p-6">
        <form method="POST" action="{{ route('admin.staff.store') }}" class="space-y-5" id="staff-create-form">
            @csrf
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="rr-label">Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="rr-input">
                </div>
                <div>
                    <label class="rr-label">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="rr-input">
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-slate-50/80 p-4">
                <input type="hidden" name="generate_password" value="0">
                <label class="inline-flex cursor-pointer items-start gap-3">
                    <input type="checkbox" name="generate_password" value="1" id="generate_password" class="mt-1 size-4 rounded border-slate-300 text-violet-600 focus:ring-violet-500" @checked(old('generate_password', '1') == '1')>
                    <span>
                        <span class="block text-sm font-semibold text-slate-800">Generate secure password automatically</span>
                        <span class="block text-xs text-slate-500">A random password is created and emailed to the staff member (recommended).</span>
                    </span>
                </label>
            </div>

            <div id="manual-password-fields" class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="rr-label">Password</label>
                    <input type="password" name="password" id="staff_password" class="rr-input" minlength="8" autocomplete="new-password">
                    <p class="mt-1 text-xs text-slate-500">Minimum 8 characters when not using auto-generate.</p>
                </div>
                <div>
                    <label class="rr-label">Confirm password</label>
                    <input type="password" name="password_confirmation" id="staff_password_confirmation" class="rr-input" minlength="8" autocomplete="new-password">
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="rr-label">Staff role</label>
                    <select name="role" id="role" class="rr-input">
                        @foreach($roleLabels as $key => $label)
                            <option value="{{ $key }}" @selected(old('role') === $key)>{{ $label }}</option>
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
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true)) class="size-4 rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                        Active
                    </label>
                </div>
            </div>

            <div>
                <label class="rr-label">Permissions</label>
                <div id="permission-grid" class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                    @foreach($permissionLabels as $perm => $label)
                        <label class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm">
                            <input type="checkbox" name="permissions[]" value="{{ $perm }}" @checked(in_array($perm, old('permissions', []), true)) class="size-4 rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
                <p class="mt-2 text-xs text-slate-500">Tip: Role defaults apply if you leave permissions unchecked.</p>
            </div>

            <div class="flex gap-3">
                <button class="rr-btn-primary inline-flex items-center rounded-lg px-5 py-2.5 text-sm font-semibold">Create staff</button>
                <a href="{{ route('admin.staff.index') }}" class="rr-btn-secondary inline-flex items-center rounded-lg px-5 py-2.5 text-sm font-semibold">Cancel</a>
            </div>
        </form>
    </div>
</div>
@php($hasOldPermissions = is_array(old('permissions')) && count(old('permissions')) > 0)
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const roleSelect = document.getElementById('role');
        const permissionGrid = document.getElementById('permission-grid');
        const genCheckbox = document.getElementById('generate_password');
        const manualBlock = document.getElementById('manual-password-fields');
        const pwd = document.getElementById('staff_password');
        const pwd2 = document.getElementById('staff_password_confirmation');

        const roleDefaults = @json($defaultPermissionsByRole);
        const hasOldPermissions = @json($hasOldPermissions);

        if (roleSelect && permissionGrid) {
            const applyDefaults = (roleKey) => {
                const defaults = roleDefaults[roleKey] || [];
                const checkboxes = permissionGrid.querySelectorAll('input[type="checkbox"][name="permissions[]"]');
                checkboxes.forEach((box) => {
                    box.checked = defaults.includes(box.value);
                });
            };

            if (!hasOldPermissions) {
                applyDefaults(roleSelect.value);
            }

            roleSelect.addEventListener('change', function () {
                applyDefaults(roleSelect.value);
            });
        }

        const customRoleWrapper = document.getElementById('custom-role-wrapper');
        const customRoleInput = document.getElementById('custom_role_name');
        const syncCustomRole = () => {
            const isCustom = roleSelect && roleSelect.value === '__custom__';
            if (customRoleWrapper) customRoleWrapper.style.display = isCustom ? '' : 'none';
            if (customRoleInput) customRoleInput.required = !!isCustom;
        };
        if (roleSelect) {
            roleSelect.addEventListener('change', syncCustomRole);
            syncCustomRole();
        }

        const syncPasswordFields = () => {
            const auto = genCheckbox && genCheckbox.checked;
            if (manualBlock) {
                manualBlock.style.display = auto ? 'none' : '';
            }
            if (pwd && pwd2) {
                pwd.required = !auto;
                pwd2.required = !auto;
                if (auto) {
                    pwd.value = '';
                    pwd2.value = '';
                }
            }
        };

        if (genCheckbox) {
            genCheckbox.addEventListener('change', syncPasswordFields);
            syncPasswordFields();
        }
    });
</script>
@endsection
