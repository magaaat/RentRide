@extends('layouts.app')

@section('title', 'Add Staff')

@section('content')
<div class="mx-auto max-w-3xl">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold">Add staff</h1>
        <p class="text-sm text-slate-500">Create a tenant staff account and set role permissions.</p>
    </div>

    <div class="rr-panel-elevated p-6">
        <form method="POST" action="{{ route('admin.staff.store') }}" class="space-y-5">
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

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="rr-label">Password</label>
                    <input type="password" name="password" required class="rr-input" minlength="8" autocomplete="new-password">
                    <p class="mt-1 text-xs text-slate-500">Minimum 8 characters.</p>
                </div>
                <div>
                    <label class="rr-label">Confirm password</label>
                    <input type="password" name="password_confirmation" required class="rr-input" minlength="8" autocomplete="new-password">
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="rr-label">Staff role</label>
                    <select name="role" id="role" class="rr-input">
                        @foreach($roleLabels as $key => $label)
                            <option value="{{ $key }}" @selected(old('role') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
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
        const roleDefaults = @json($defaultPermissionsByRole);
        const hasOldPermissions = @json($hasOldPermissions);

        if (!roleSelect || !permissionGrid) return;

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
    });
</script>
@endsection

