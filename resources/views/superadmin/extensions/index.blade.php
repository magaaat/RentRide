@extends('layouts.app')

@section('title', 'Extension Requests - RentRide')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold">Plan Extension Requests</h1>
            <p class="text-sm text-slate-300">Review tenant requests to extend/renew access.</p>
        </div>
    </div>

    <div class="rr-surface border rr-border rounded-2xl overflow-hidden">
        <div class="px-5 py-4 border-b rr-border">
            <h2 class="text-sm font-semibold text-slate-200">Pending</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="rr-table-head">
                    <tr class="text-left text-slate-200">
                        <th class="px-5 py-3 font-semibold">Tenant</th>
                        <th class="px-5 py-3 font-semibold">Requested Plan</th>
                        <th class="px-5 py-3 font-semibold">Requested At</th>
                        <th class="px-5 py-3 font-semibold">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y rr-border">
                    @forelse($pending as $req)
                        <tr class="rr-row-hover">
                            <td class="px-5 py-3">
                                <div class="font-semibold">{{ $req->tenant->company_name }}</div>
                                <div class="text-xs text-slate-400">{{ $req->tenant->email }}</div>
                            </td>
                            <td class="px-5 py-3">{{ ucfirst($req->requested_plan) }}</td>
                            <td class="px-5 py-3 text-slate-300">{{ $req->created_at->format('Y-m-d H:i') }}</td>
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-2">
                                    <form method="POST" action="{{ route('superadmin.extensions.approve', $req) }}" class="approve-form">
                                        @csrf
                                        <button class="inline-flex items-center rounded-lg rr-btn-primary px-3 py-1.5 text-xs font-semibold">
                                            Approve
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('superadmin.extensions.reject', $req) }}" class="reject-form">
                                        @csrf
                                        <input type="hidden" name="notes" value="">
                                        <button class="inline-flex items-center rounded-lg rr-btn-secondary px-3 py-1.5 text-xs font-semibold">
                                            Reject
                                        </button>
                                    </form>
                                    <a href="{{ route('superadmin.tenants.show', $req->tenant) }}" class="text-xs text-slate-300 hover:text-slate-100 underline">
                                        View tenant
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-6 text-slate-300">No pending extension requests.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="rr-surface border rr-border rounded-2xl overflow-hidden">
        <div class="px-5 py-4 border-b rr-border">
            <h2 class="text-sm font-semibold text-slate-200">Recent decisions</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="rr-table-head">
                    <tr class="text-left text-slate-200">
                        <th class="px-5 py-3 font-semibold">Tenant</th>
                        <th class="px-5 py-3 font-semibold">Plan</th>
                        <th class="px-5 py-3 font-semibold">Status</th>
                        <th class="px-5 py-3 font-semibold">Reviewed At</th>
                    </tr>
                </thead>
                <tbody class="divide-y rr-border">
                    @forelse($recent as $req)
                        <tr class="rr-row-hover">
                            <td class="px-5 py-3">
                                <div class="font-semibold">{{ $req->tenant->company_name }}</div>
                                <div class="text-xs text-slate-400">{{ $req->tenant->email }}</div>
                            </td>
                            <td class="px-5 py-3">{{ ucfirst($req->requested_plan) }}</td>
                            <td class="px-5 py-3">
                                @if($req->status === 'approved')
                                    <span class="inline-flex items-center rounded-full bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 px-2 py-0.5 text-xs font-semibold">Approved</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-rose-500/15 text-rose-300 border border-rose-500/30 px-2 py-0.5 text-xs font-semibold">Rejected</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-slate-300">{{ optional($req->reviewed_at)->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-6 text-slate-300">No recent decisions yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
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
                confirmButtonColor: 'var(--rr-accent)',
            }).then((r) => { if (r.isConfirmed) form.submit(); });
        });
    });

    document.querySelectorAll('.reject-form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (!window.Swal) return;
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Reject extension?',
                input: 'text',
                inputLabel: 'Optional reason',
                inputPlaceholder: 'e.g., Please update payment first',
                showCancelButton: true,
                confirmButtonText: 'Reject',
                confirmButtonColor: '#ef4444',
            }).then((r) => {
                if (!r.isConfirmed) return;
                const notes = form.querySelector('input[name="notes"]');
                if (notes) notes.value = (r.value || '').toString();
                form.submit();
            });
        });
    });
});
</script>
@endpush
@endsection

