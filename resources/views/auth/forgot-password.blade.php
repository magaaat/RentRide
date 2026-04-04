@extends('layouts.app')

@section('title', 'Forgot Password - RentRide')

@section('content')
@php
    $pr = session('password_reset_return', []);
@endphp
<div class="min-h-[70vh] flex items-center justify-center py-10">
    <div class="w-full max-w-md rr-panel-elevated p-8">
        <h2 class="text-2xl font-semibold text-center mb-2 text-slate-900">Forgot password</h2>
        <p class="text-center text-sm text-slate-600 mb-6">We’ll email you a reset code.</p>

        <form method="POST" action="{{ url('/forgot-password') }}" class="space-y-4">
            @csrf
            @if(!empty($pr['from']))
                <input type="hidden" name="from" value="{{ $pr['from'] }}">
            @endif
            @if(isset($pr['tenant']) && $pr['tenant'] !== null && $pr['tenant'] !== '')
                <input type="hidden" name="tenant" value="{{ $pr['tenant'] }}">
            @endif
            <div>
                <label class="rr-label" for="fp-email">Email</label>
                <input
                    id="fp-email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autocomplete="email"
                    class="rr-input"
                >
            </div>
            <button type="submit" class="rr-btn-primary w-full inline-flex justify-center items-center rounded-lg py-2.5 text-sm font-semibold shadow-lg transition">
                Send code
            </button>
        </form>

        @include('auth.partials.password-reset-nav', ['returnUrl' => $returnUrl])
    </div>
</div>
@endsection
