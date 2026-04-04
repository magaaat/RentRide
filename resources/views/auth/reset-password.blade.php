@extends('layouts.app')

@section('title', 'New password - RentRide')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-10">
    <div class="w-full max-w-md rr-panel-elevated p-8">
        <h2 class="text-2xl font-semibold text-center mb-2 text-slate-900">Reset password</h2>
        <p class="text-center text-sm text-slate-600 mb-1">Account</p>
        <p class="text-center text-sm font-medium text-slate-900 mb-6">{{ $email }}</p>

        <form method="POST" action="{{ url('/reset-password') }}" class="space-y-4">
            @csrf
            <div>
                <label class="rr-label" for="rp-password">Password</label>
                <input
                    id="rp-password"
                    type="password"
                    name="password"
                    required
                    autocomplete="new-password"
                    class="rr-input"
                >
            </div>
            <div>
                <label class="rr-label" for="rp-password-confirm">Confirm</label>
                <input
                    id="rp-password-confirm"
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    class="rr-input"
                >
            </div>
            <button type="submit" class="rr-btn-primary w-full inline-flex justify-center items-center rounded-lg py-2.5 text-sm font-semibold shadow-lg transition">
                Save
            </button>
        </form>

        @include('auth.partials.password-reset-nav', [
            'returnUrl' => $returnUrl,
            'secondaryHref' => url('/reset-password/verify'),
            'secondaryLabel' => 'Re-enter verification code',
        ])
    </div>
</div>
@endsection
