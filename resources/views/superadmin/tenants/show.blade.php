@extends('layouts.app')

@section('title', 'Tenant Profile')

@section('content')
<div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-slate-50">Tenant profile</h1>
        <p class="mt-1 text-sm text-slate-400">Review details and manage subscription & domain.</p>
    </div>
    <form method="POST" action="{{ route('superadmin.tenants.destroy', $tenant) }}" id="delete-tenant-form">
        @csrf
        @method('DELETE')
        <button type="button" id="delete-tenant-button" class="inline-flex items-center rounded-lg bg-red-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-red-500">
            Delete Tenant
        </button>
    </form>
</div>

<div class="rr-panel mb-6 p-5 sm:p-6">
    <h5 class="text-lg font-semibold mb-2">{{ $tenant->company_name }}</h5>
    <p class="mb-1 text-sm"><strong>Owner:</strong> {{ $tenant->owner_name }}</p>
    <p class="mb-1 text-sm"><strong>Email:</strong> {{ $tenant->email }}</p>
    <p class="mb-1 text-sm">
        <strong>Status:</strong>
        @if($tenant->status === 'approved')
            <span class="rr-chip-accent inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold">
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
    <p class="mb-1 text-sm">
        <strong>Domain:</strong>
        @if($tenant->domain && ($tenantLoginUrl = $tenant->tenantLoginUrl()))
            <a href="{{ $tenantLoginUrl }}" target="_blank" rel="noopener noreferrer" class="rr-link-accent font-medium underline underline-offset-2">{{ $tenant->domain }}</a>
            <span class="ml-1 text-xs text-slate-500">(tenant login)</span>
        @elseif($tenant->domain)
            {{ $tenant->domain }}
        @else
            -
        @endif
    </p>
    <p class="mb-0 text-sm"><strong>Domain Status:</strong>
        @if($tenant->is_domain_active)
            <span class="rr-chip-accent inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold">
                Active
            </span>
        @else
            <span class="inline-flex items-center rounded-full bg-slate-500/20 px-2.5 py-0.5 text-xs font-semibold text-slate-300">
                Disabled
            </span>
        @endif
    </p>
    @if($tenant->subscription_plan === 'premium')
        <p class="mt-2 mb-0 text-sm"><strong>Homepage featured:</strong>
            @if($tenant->is_featured ?? false)
                <span class="text-violet-300">Yes</span>
            @else
                <span class="text-slate-400">No</span>
            @endif
        </p>
    @endif
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
                    <button class="rr-btn-primary inline-flex items-center rounded-lg px-3 py-1.5 text-xs font-semibold">
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

<h2 class="mb-4 text-lg font-semibold text-slate-100">Update tenant settings</h2>
<div class="rr-panel-elevated p-6 sm:p-8">
<form method="POST" action="{{ route('superadmin.tenants.update', $tenant) }}">
    @csrf
    @method('PUT')
    <div class="mb-5 grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
        <div class="sm:col-span-2 xl:col-span-2">
            <label class="rr-label" for="company_name">Company name</label>
            <input id="company_name" type="text" name="company_name" value="{{ old('company_name', $tenant->company_name) }}" required class="rr-input">
        </div>
        <div>
            <label class="rr-label" for="status">Status</label>
            <select id="status" name="status" class="rr-input">
                <option value="pending" @selected($tenant->status === 'pending')>Pending</option>
                <option value="approved" @selected($tenant->status === 'approved')>Approved</option>
            </select>
        </div>
        <div>
            <label class="rr-label" for="subscription_plan">Subscription plan</label>
            <select id="subscription_plan" name="subscription_plan" class="rr-input">
                @foreach(['basic','standard','premium'] as $plan)
                    <option value="{{ $plan }}" @selected($tenant->subscription_plan === $plan)>{{ ucfirst($plan) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="rr-label" for="subscription_expiry">Subscription expiry</label>
            <input id="subscription_expiry" type="date" name="subscription_expiry" value="{{ old('subscription_expiry', optional($tenant->subscription_expiry)->format('Y-m-d')) }}" class="rr-input">
        </div>
    </div>
    <div class="mb-5 grid grid-cols-1 gap-5 lg:grid-cols-2">
        <div>
            <label class="rr-label" for="domain">Domain</label>
            <input id="domain" type="text" name="domain" value="{{ old('domain', $tenant->domain) }}" placeholder="e.g. company.localhost" class="rr-input">
            <p class="mt-1 text-xs text-slate-500">Use hostname only (no http:// and no :port). Example: company.localhost</p>
        </div>
        <div class="flex items-end pb-0.5">
            <label class="flex cursor-pointer items-center gap-2.5" for="is_domain_active">
                <input type="checkbox" name="is_domain_active" id="is_domain_active" value="1" @checked($tenant->is_domain_active)
                    class="size-4 rounded border-slate-600 bg-slate-900 text-violet-500 focus:ring-violet-500">
                <span class="text-sm text-slate-300">Domain enabled</span>
            </label>
        </div>
    </div>
    <div id="featured-homepage-settings" class="mb-6 @unless(old('subscription_plan', $tenant->subscription_plan) === 'premium') hidden @endunless">
        <label class="flex cursor-pointer items-start gap-3" for="is_featured">
            <input type="checkbox" name="is_featured" id="is_featured" value="1" @checked(old('is_featured', ($tenant->subscription_plan === 'premium') && ($tenant->is_featured ?? false)))
                class="mt-0.5 size-4 rounded border-slate-600 bg-slate-900 text-violet-500 focus:ring-violet-500">
            <span class="text-sm leading-relaxed text-slate-300">Featured on public homepage (Premium only — shows company on landing page)</span>
        </label>
    </div>
    <div class="flex flex-wrap gap-3">
        <button type="submit" class="inline-flex items-center rounded-lg rr-btn-primary px-5 py-2.5 text-sm font-semibold shadow-sm">Save changes</button>
        <a href="{{ route('superadmin.tenants.index') }}" class="inline-flex items-center rounded-lg rr-btn-secondary px-5 py-2.5 text-sm font-semibold">Back to list</a>
    </div>
</form>
</div>

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

            const planSelect = document.getElementById('subscription_plan');
            const featuredBlock = document.getElementById('featured-homepage-settings');
            const featuredCb = document.getElementById('is_featured');
            function syncFeaturedHomepageVisibility() {
                if (!planSelect || !featuredBlock) return;
                const premium = planSelect.value === 'premium';
                featuredBlock.classList.toggle('hidden', !premium);
                if (!premium && featuredCb) {
                    featuredCb.checked = false;
                }
            }
            planSelect?.addEventListener('change', syncFeaturedHomepageVisibility);
            syncFeaturedHomepageVisibility();

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
