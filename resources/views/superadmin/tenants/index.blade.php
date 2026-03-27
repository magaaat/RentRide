@extends('layouts.app')

@section('title', 'Tenants')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h3 class="text-xl font-semibold">Tenants</h3>
    <a href="{{ route('superadmin.tenants.create') }}" class="inline-flex items-center rounded-lg bg-emerald-500 px-4 py-2 text-sm font-semibold text-slate-950 hover:bg-emerald-400">
        Add tenant
    </a>
</div>

<div class="overflow-hidden rounded-xl border border-slate-800 bg-slate-900/60">
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
                            <span class="inline-flex items-center rounded-full bg-emerald-500/10 px-2.5 py-0.5 text-xs font-semibold text-emerald-300">
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
                    <td class="px-4 py-3">{{ $tenant->domain ?? '-' }}</td>
                    <td class="px-4 py-3">
                        @if($tenant->status !== 'approved' || ! $tenant->domain)
                            <span class="text-slate-400">-</span>
                        @elseif($tenant->is_domain_active)
                            <span class="inline-flex items-center rounded-full bg-emerald-500/10 px-2.5 py-0.5 text-xs font-semibold text-emerald-300">
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

