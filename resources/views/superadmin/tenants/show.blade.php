@extends('layouts.app')

@section('title', 'Tenant Profile')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h3 class="text-xl font-semibold">Tenant Profile</h3>
    <form method="POST" action="{{ route('superadmin.tenants.destroy', $tenant) }}" id="delete-tenant-form">
        @csrf
        @method('DELETE')
        <button type="button" id="delete-tenant-button" class="inline-flex items-center rounded-lg bg-red-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-red-500">
            Delete Tenant
        </button>
    </form>
</div>

<div class="mb-4 rounded-xl border border-slate-800 bg-slate-900/70 p-4">
    <h5 class="text-lg font-semibold mb-2">{{ $tenant->company_name }}</h5>
    <p class="mb-1 text-sm"><strong>Owner:</strong> {{ $tenant->owner_name }}</p>
    <p class="mb-1 text-sm"><strong>Email:</strong> {{ $tenant->email }}</p>
    <p class="mb-1 text-sm">
        <strong>Status:</strong>
        @if($tenant->status === 'approved')
            <span class="inline-flex items-center rounded-full bg-emerald-500/10 px-2.5 py-0.5 text-xs font-semibold text-emerald-300">
                Approved
            </span>
        @else
            <span class="inline-flex items-center rounded-full bg-amber-400/10 px-2.5 py-0.5 text-xs font-semibold text-amber-300">
                Pending
            </span>
        @endif
    </p>
    <p class="mb-1 text-sm"><strong>Phone:</strong> {{ $tenant->phone }}</p>
    <p class="mb-1 text-sm"><strong>Address:</strong> {{ $tenant->address }}</p>
    <p class="mb-1 text-sm"><strong>Plan:</strong> {{ ucfirst($tenant->subscription_plan) }}</p>
    <p class="mb-1 text-sm"><strong>Subscription Expiry:</strong>
        {{ $tenant->subscription_expiry ? $tenant->subscription_expiry->format('Y-m-d') : '-' }}
    </p>
    <p class="mb-1 text-sm"><strong>Domain:</strong> {{ $tenant->domain ?? '-' }}</p>
    <p class="mb-0 text-sm"><strong>Domain Status:</strong>
        @if($tenant->is_domain_active)
            <span class="inline-flex items-center rounded-full bg-emerald-500/10 px-2.5 py-0.5 text-xs font-semibold text-emerald-300">
                Active
            </span>
        @else
            <span class="inline-flex items-center rounded-full bg-slate-500/20 px-2.5 py-0.5 text-xs font-semibold text-slate-300">
                Disabled
            </span>
        @endif
    </p>
    <p class="mt-2 mb-0 text-sm"><strong>Homepage featured:</strong>
        @if($tenant->is_featured ?? false)
            <span class="text-emerald-300">Yes</span>
        @else
            <span class="text-slate-400">No</span>
        @endif
    </p>
    <p class="mt-2 text-sm">
        <strong>Database Usage:</strong>
        <span class="text-slate-200">{{ number_format($dataUsedMb ?? 0, 2) }} MB</span>
        <span class="text-slate-400 text-xs">(DB: {{ $dbName ?? ('tenant_' . $tenant->id) }})</span>
    </p>
    @if(!empty($pendingExtension))
        <div class="mt-3 rounded-xl border border-amber-400/20 bg-amber-400/5 p-3 text-sm text-amber-200">
            <div class="font-semibold">Pending extension request</div>
            <div class="mt-1 text-xs text-amber-100/80">
                Requested plan: <strong>{{ ucfirst($pendingExtension->requested_plan) }}</strong>
                <span class="text-amber-100/60">({{ $pendingExtension->created_at->format('Y-m-d H:i') }})</span>
            </div>
            <div class="mt-2 flex items-center gap-2">
                <form method="POST" action="{{ route('superadmin.extensions.approve', $pendingExtension) }}" class="approve-form inline-block">
                    @csrf
                    <button class="inline-flex items-center rounded-lg bg-emerald-500 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-600">
                        Approve
                    </button>
                </form>
                <a href="{{ route('superadmin.extensions.index') }}" class="text-xs text-slate-200 underline hover:text-white">
                    View all requests
                </a>
            </div>
        </div>
    @endif
</div>

<h5 class="mb-3">Update Tenant Settings</h5>
<form method="POST" action="{{ route('superadmin.tenants.update', $tenant) }}">
    @csrf
    @method('PUT')
    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label">Company Name</label>
            <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $tenant->company_name) }}" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="pending" @selected($tenant->status === 'pending')>Pending</option>
                <option value="approved" @selected($tenant->status === 'approved')>Approved</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Subscription Plan</label>
            <select name="subscription_plan" class="form-select">
                @foreach(['basic','standard','premium'] as $plan)
                    <option value="{{ $plan }}" @selected($tenant->subscription_plan === $plan)>{{ ucfirst($plan) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Subscription Expiry</label>
            <input type="date" name="subscription_expiry" class="form-control" value="{{ old('subscription_expiry', optional($tenant->subscription_expiry)->format('Y-m-d')) }}">
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label">Domain</label>
            <input type="text" name="domain" class="form-control" value="{{ old('domain', $tenant->domain) }}" placeholder="e.g. tenant1.rentride.test">
        </div>
        <div class="col-md-6 d-flex align-items-center">
            <div class="form-check mt-4">
                <input class="form-check-input" type="checkbox" name="is_domain_active" id="is_domain_active" value="1" @checked($tenant->is_domain_active)>
                <label class="form-check-label" for="is_domain_active">
                    Domain enabled
                </label>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" @checked(old('is_featured', $tenant->is_featured ?? false))>
                <label class="form-check-label" for="is_featured">
                    Featured on public homepage (Premium listings — shows company on landing page)
                </label>
            </div>
        </div>
    </div>
    <button class="btn btn-primary">Save Changes</button>
    <a href="{{ route('superadmin.tenants.index') }}" class="btn btn-secondary">Back to List</a>
</form>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const deleteBtn = document.getElementById('delete-tenant-button');
            const deleteForm = document.getElementById('delete-tenant-form');

            if (deleteBtn && deleteForm && window.Swal) {
                deleteBtn.addEventListener('click', function () {
                    Swal.fire({
                        title: 'Delete tenant?',
                        text: 'This will permanently remove this tenant and their data.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, delete',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            deleteForm.submit();
                        }
                    });
                });
            }

            document.querySelectorAll('.approve-form').forEach(function (form) {
                form.addEventListener('submit', function (e) {
                    if (!window.Swal) return;
                    e.preventDefault();
                    Swal.fire({
                        icon: 'question',
                        title: 'Approve extension?',
                        text: 'This will re-enable the tenant domain and reset expiry to 1 month from today.',
                        showCancelButton: true,
                        confirmButtonText: 'Approve',
                        confirmButtonColor: '#10b981',
                    }).then((r) => { if (r.isConfirmed) form.submit(); });
                });
            });
        });
    </script>
@endpush
@endsection
