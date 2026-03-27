@extends('layouts.app')

@section('title', 'My Profile - RentRide')

@section('content')
<div class="max-w-2xl">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold">My Profile</h1>
        <p class="mt-1 text-sm text-slate-400">Update your details and password.</p>
    </div>

    <div class="rounded-xl border border-slate-800 bg-slate-900/60 p-6">
        <form method="POST" action="{{ route('customer.profile.update') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium mb-1">Full name</label>
                <input
                    type="text"
                    name="name"
                    value="{{ old('name', $user->name) }}"
                    required
                    class="w-full rounded-lg border border-slate-600 bg-slate-900/70 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                >
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Email</label>
                <input
                    type="email"
                    name="email"
                    value="{{ old('email', $user->email) }}"
                    required
                    class="w-full rounded-lg border border-slate-600 bg-slate-900/70 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                >
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Phone (optional)</label>
                    <input
                        type="text"
                        name="phone"
                        value="{{ old('phone', $user->phone) }}"
                        class="w-full rounded-lg border border-slate-600 bg-slate-900/70 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                    >
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Address (optional)</label>
                    <input
                        type="text"
                        name="address"
                        value="{{ old('address', $user->address) }}"
                        class="w-full rounded-lg border border-slate-600 bg-slate-900/70 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                    >
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">New password (optional)</label>
                    <input
                        type="password"
                        name="password"
                        class="w-full rounded-lg border border-slate-600 bg-slate-900/70 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                    >
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Confirm password</label>
                    <input
                        type="password"
                        name="password_confirmation"
                        class="w-full rounded-lg border border-slate-600 bg-slate-900/70 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                    >
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button
                    type="submit"
                    class="inline-flex items-center rounded-lg bg-emerald-500 px-4 py-2 text-sm font-semibold text-slate-950 hover:bg-emerald-400"
                >
                    Save Changes
                </button>
                <a
                    href="{{ route('customer.dashboard') }}"
                    class="inline-flex items-center rounded-lg border border-slate-600 px-4 py-2 text-sm font-semibold text-slate-200 hover:bg-slate-800"
                >
                    Back to dashboard
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

