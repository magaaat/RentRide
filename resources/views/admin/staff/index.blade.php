@extends('layouts.app')

@section('title', 'Staff Management')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-semibold">Staff</h1>
        <p class="text-sm text-slate-500">Manage tenant staff roles, status, and permissions (RBAC).</p>
    </div>
    <a href="{{ route('admin.staff.create') }}" class="rr-btn-primary inline-flex items-center rounded-lg px-4 py-2 text-sm font-semibold">Add staff</a>
</div>

<div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr>
                    <th class="px-4 py-3 text-left font-semibold">Name</th>
                    <th class="px-4 py-3 text-left font-semibold">Email</th>
                    <th class="px-4 py-3 text-left font-semibold">Role</th>
                    <th class="px-4 py-3 text-left font-semibold">Status</th>
                    <th class="px-4 py-3 text-left font-semibold">Permissions</th>
                    <th class="px-4 py-3 text-right font-semibold">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($staff as $s)
                    @php($staffRoleKey = $s->staff_role ?: $s->role)
                    <tr>
                        <td class="px-4 py-3">{{ $s->name }}</td>
                        <td class="px-4 py-3">{{ $s->email }}</td>
                        <td class="px-4 py-3">{{ $roleLabels[$staffRoleKey] ?? ucfirst(str_replace('_', ' ', $staffRoleKey)) }}</td>
                        <td class="px-4 py-3">
                            @if($s->is_active)
                                <span class="rr-chip-accent inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold">Active</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-600">Disabled</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-600">
                            {{ collect($s->permissions ?? [])->map(fn($p) => $permissionLabels[$p] ?? $p)->implode(', ') ?: 'None' }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="inline-flex items-center gap-2">
                                <a href="{{ route('admin.staff.edit', $s) }}" class="inline-flex items-center rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                    Edit
                                </a>
                                <form
                                    method="POST"
                                    action="{{ route('admin.staff.destroy', $s) }}"
                                    data-confirm
                                    data-confirm-title="Delete staff account?"
                                    data-confirm-text="This staff user will be permanently deleted."
                                    data-confirm-button="Yes, delete staff"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center rounded-lg border border-rose-300 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-50">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-slate-500">No staff yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-8">
    <h2 class="text-lg font-semibold">Staff type permissions</h2>
    <p class="mt-1 text-sm text-slate-500">Update permissions by staff type and apply to all staff of that type at once.</p>
</div>

<div class="mt-4 grid gap-4">
    @foreach($roleLabels as $roleKey => $roleLabel)
        <form
            method="POST"
            action="{{ route('admin.staff.role-permissions.update') }}"
            class="rr-panel-elevated p-4"
            data-confirm
            data-confirm-icon="question"
            data-confirm-color="#7c3aed"
            data-confirm-title="Apply role permissions?"
            data-confirm-text="This will update all staff under this role."
            data-confirm-button="Apply to all"
        >
            @csrf
            <input type="hidden" name="role" value="{{ $roleKey }}">
            <div class="mb-3 flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                <h3 class="font-semibold">{{ $roleLabel }}</h3>
                <p class="text-xs text-slate-500">Staff count: {{ $staffCountByRole[$roleKey] ?? 0 }}</p>
            </div>
            <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3">
                @php($selected = $roleTemplates[$roleKey] ?? [])
                @foreach($permissionLabels as $perm => $permLabel)
                    <label class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm">
                        <input
                            type="checkbox"
                            name="permissions[]"
                            value="{{ $perm }}"
                            @checked(in_array($perm, $selected, true))
                            class="size-4 rounded border-slate-300 text-violet-600 focus:ring-violet-500"
                        >
                        {{ $permLabel }}
                    </label>
                @endforeach
            </div>
            <div class="mt-3">
                <button type="submit" class="rr-btn-primary inline-flex items-center rounded-lg px-4 py-2 text-xs font-semibold">Apply to all {{ $roleLabel }}</button>
            </div>
        </form>
    @endforeach
</div>
@endsection

