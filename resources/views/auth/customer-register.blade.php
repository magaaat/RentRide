@extends('layouts.app')

@section('title', 'Register - Rent a Car')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-10">
    <div class="w-full max-w-lg bg-slate-800/70 border border-slate-700 rounded-2xl shadow-2xl p-8 backdrop-blur">
        <h2 class="text-2xl font-semibold text-center mb-2">Create customer account</h2>
        <p class="text-center text-sm text-slate-400 mb-6">No approval needed — start browsing and booking right away.</p>
        <form method="POST" action="{{ route('customer.register.post') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1">Full name</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full rounded-lg border border-slate-600 bg-slate-900/70 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full rounded-lg border border-slate-600 bg-slate-900/70 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Phone <span class="text-slate-500">(optional)</span></label>
                <input type="text" name="phone" value="{{ old('phone') }}"
                    class="w-full rounded-lg border border-slate-600 bg-slate-900/70 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Address <span class="text-slate-500">(optional)</span></label>
                <textarea name="address" rows="2"
                    class="w-full rounded-lg border border-slate-600 bg-slate-900/70 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent">{{ old('address') }}</textarea>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Password</label>
                    <input type="password" name="password" required
                        class="w-full rounded-lg border border-slate-600 bg-slate-900/70 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Confirm password</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full rounded-lg border border-slate-600 bg-slate-900/70 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent">
                </div>
            </div>
            <button type="submit" class="rr-btn-primary w-full inline-flex justify-center items-center rounded-lg py-2.5 text-sm font-semibold shadow-lg transition">
                Register
            </button>
        </form>
        <p class="mt-6 text-center text-sm text-slate-400">
            Already have an account?
            <a href="{{ route('customer.login') }}" class="rr-link-accent font-semibold">Sign in</a>
        </p>
        <p class="mt-2 text-center text-sm">
            <a href="{{ url('/') }}" class="text-slate-500 hover:text-slate-300">← Back to home</a>
        </p>
    </div>
</div>
@endsection
