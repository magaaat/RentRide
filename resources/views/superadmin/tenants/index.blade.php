@extends('layouts.app')

@section('title', 'Tenants')

@section('content')
<div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-slate-50 sm:text-3xl">Tenants</h1>
        <p class="mt-1 text-sm text-slate-400">Approve applications and manage rental companies.</p>
    </div>
    <a href="{{ route('superadmin.tenants.create') }}" class="rr-btn-primary inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm font-semibold shadow-sm transition">
        Add tenant
    </a>
</div>

<div class="overflow-hidden rounded-xl border border-slate-700/80 bg-slate-900/50 shadow-rr">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-800/80 text-slate-200">
            <tr>
                <th class="px-4 py-3 text-left font-semibold">Company</th>
                <th class="px-4 py-3 text-left font-semibold">Owner</th>
                <th class="px-4 py-3 text-left font-semibold">Email</th>
                <th class="px-4 py-3 text-left font-semibold">Status</th>
                <th class="px-4 py-3 text-left font-semibold">Plan</th>
                <th class="px-4 py-3 text-left font-semibold">Domain</th>
                <th class="px-4 py-3 text-left font-semibold">Domain Status</th>
                <th class="px-4 py-3 text-right font-semibold">Actions</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
            @forelse($tenants as $tenant)
                <tr class="hover:bg-slate-800/60">
                    <td class="px-4 py-3">{{ $tenant->company_name }}</td>
                    <td class="px-4 py-3">{{ $tenant->owner_name }}</td>
                    <td class="px-4 py-3">{{ $tenant->email }}</td>
                    <td class="px-4 py-3">
                        @if($tenant->status === 'approved')
                            <span class="rr-chip-accent inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold">
                                Approved
                            </span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-amber-400/10 px-2.5 py-0.5 text-xs font-semibold text-amber-300">
                                Pending
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 capitalize">
                        {{ $tenant->subscription_plan }}
                    </td>
                    <td class="px-4 py-3">
                        @if($tenant->domain && ($u = $tenant->tenantLoginUrl()))
                            <a href="{{ $u }}" target="_blank" rel="noopener noreferrer" class="rr-link-accent underline underline-offset-2">{{ $tenant->domain }}</a>
                        @else
                            {{ $tenant->domain ?? '-' }}
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if($tenant->status !== 'approved' || ! $tenant->domain)
                            <span class="text-slate-400">-</span>
                        @elseif($tenant->is_domain_active)
                            <span class="rr-chip-accent inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold">
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-slate-500/20 px-2.5 py-0.5 text-xs font-semibold text-slate-300">
                                Disabled
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('superadmin.tenants.show', $tenant) }}"
                           class="inline-flex items-center rounded-lg bg-slate-800 px-3 py-1.5 text-xs font-semibold text-slate-100 hover:bg-slate-700">
                            View
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-6 text-center text-slate-400">
                        No tenants found.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $tenants->links() }}
</div>
@endsection

