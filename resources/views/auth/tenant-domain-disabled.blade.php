@extends('layouts.app')

@section('title', 'Domain Disabled - RentRide')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-10">
    <div class="w-full max-w-4xl rr-surface border rr-border rounded-3xl shadow-2xl p-8 backdrop-blur">
        <div class="text-center">
            <div class="inline-flex items-center rounded-full border rr-border rr-surface-2 px-3 py-1 text-xs font-semibold text-slate-200">
                Tenant: {{ $tenant->company_name }}
            </div>
            <h2 class="mt-4 text-2xl font-semibold">Access unavailable</h2>
            @if(($disabledReason ?? 'maintenance') === 'expired')
                <p class="mt-2 text-sm text-slate-300">Your subscription has expired. You may request a plan extension.</p>
            @else
                <p class="mt-2 text-sm text-slate-300">Your tenant domain is temporarily unavailable for maintenance. Please contact Super Admin.</p>
            @endif
        </div>

        @if(($disabledReason ?? 'maintenance') === 'expired')
            @if($hasPending)
                <div class="mt-6 rounded-xl border rr-border rr-surface-2 p-4 text-sm text-slate-200 text-center">
                    Extension request pending. Please wait for Super Admin review.
                </div>
            @else
                <div class="mt-8 flex items-center justify-center">
                    <a href="{{ $extensionRequestUrl }}"
                       class="inline-flex items-center rounded-lg rr-btn-primary px-5 py-2.5 text-sm font-semibold">
                        Request extension
                    </a>
                </div>
            @endif
        @else
            <div class="mt-6 rounded-xl border rr-border rr-surface-2 p-4 text-sm text-slate-200 text-center">
                This domain is under maintenance. Extension requests are unavailable until service resumes.
            </div>
        @endif
    </div>
</div>
@endsection

