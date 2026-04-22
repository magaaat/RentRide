@extends('layouts.app')

@section('title', 'Super Admin Login - RentRide')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-10">
    <div class="w-full max-w-md rr-panel-elevated p-8">
        <h2 class="text-2xl font-semibold text-center mb-6 text-slate-900">Super Admin Login</h2>
        <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
            @csrf
            <div>
                <label class="rr-label" for="sa-email">Email</label>
                <input
                    id="sa-email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    class="rr-input"
                >
            </div>
            <div>
                <label class="rr-label" for="sa-password">Password</label>
                <input
                    id="sa-password"
                    type="password"
                    name="password"
                    required
                    class="rr-input"
                >
            </div>
            <div class="flex items-center justify-between text-sm">
                <label class="inline-flex items-center gap-2 text-slate-600">
                    <input type="checkbox" name="remember" id="remember" class="rounded border-slate-300 text-sky-500 focus:ring-sky-500">
                    <span>Remember me</span>
                </label>
                <a href="{{ url('/forgot-password?from=superadmin') }}" class="rr-link-accent font-medium">Forgot password?</a>
            </div>
            @if(!empty(config('services.recaptcha.site_key')))
                <div class="flex justify-center">
                    <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                </div>
            @endif
            <button type="submit" class="rr-btn-primary w-full inline-flex justify-center items-center rounded-lg py-2.5 text-sm font-semibold shadow-lg transition">
                Login
            </button>
        </form>
        <p class="mt-5 text-center text-sm">
            <a href="{{ url('/') }}" class="text-slate-500 hover:text-slate-800 transition">Back to home</a>
        </p>
    </div>
</div>
@endsection

@if(!empty(config('services.recaptcha.site_key')))
    @push('scripts')
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endpush
@endif
