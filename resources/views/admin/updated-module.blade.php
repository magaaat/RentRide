@extends('layouts.app')

@section('title', 'Updated Module')

@section('content')
<div class="mx-auto max-w-4xl">
    <h1 class="text-2xl font-semibold tracking-tight text-slate-900 sm:text-3xl">Updated module</h1>
    <p class="mt-2 text-sm text-slate-600">
        This page is visible only to tenants whose applied version is at least <span class="font-semibold">{{ $minimumVersion }}</span>.
    </p>
    <div class="mt-4 rounded-lg border border-violet-300 bg-violet-50 px-4 py-3 text-sm text-violet-900">
        Release test marker: module UI updated in test branch.
    </div>

    <div class="mt-6 rounded-xl border rr-border rr-surface p-6 shadow-rr">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-600">Tenant module access</h2>
        <dl class="mt-3 space-y-2 text-sm text-slate-700">
            <div class="flex items-center justify-between gap-4">
                <dt class="text-slate-500">Tenant</dt>
                <dd class="font-semibold">{{ $tenant->company_name }}</dd>
            </div>
            <div class="flex items-center justify-between gap-4">
                <dt class="text-slate-500">Applied version</dt>
                <dd class="font-semibold">{{ $runtimeVersion ?: 'N/A' }}</dd>
            </div>
            <div class="flex items-center justify-between gap-4">
                <dt class="text-slate-500">Applied at</dt>
                <dd class="font-semibold">
                    {{ $runtimeAppliedAt ? \Illuminate\Support\Carbon::parse($runtimeAppliedAt)->format('M d, Y h:i A') : 'N/A' }}
                </dd>
            </div>
        </dl>
    </div>
</div>
@endsection
