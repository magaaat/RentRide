@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
@php($rr = config('rentride'))
<div class="mb-4 sm:mb-6">
    <h3 class="text-xl font-semibold">My Profile</h3>
    <p class="text-sm text-slate-300">Update your account, company details, theme, and what customers see on your RentRide listing.</p>
</div>

<form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data" class="space-y-5">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-2 lg:items-start lg:gap-6">
        <div class="rounded-xl border border-slate-800 bg-slate-900/60 p-4 sm:p-5">
            <h4 class="text-sm font-semibold text-slate-200 mb-3">Account</h4>
            <div class="text-xs text-slate-400 mb-4">Used to log in to your tenant dashboard.</div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-violet-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-violet-500">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">New Password (optional)</label>
                        <input type="password" name="new_password" autocomplete="new-password"
                               class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-violet-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Confirm New Password</label>
                        <input type="password" name="new_password_confirmation" autocomplete="new-password"
                               class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-violet-500">
                    </div>
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="inline-flex items-center rounded-lg rr-btn-primary px-4 py-2 text-sm font-semibold">
                    Save Changes
                </button>
            </div>
        </div>

        <div class="rounded-xl border border-slate-800 bg-slate-900/60 p-4 sm:p-5">
            <h4 class="text-sm font-semibold text-slate-200 mb-3">Company & Theme</h4>
            <div class="text-xs text-slate-400 mb-4">These settings affect your tenant workspace and public listing.</div>

            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Company Name</label>
                        <input type="text" name="company_name" value="{{ old('company_name', $tenant->company_name) }}" required
                               class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-violet-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Owner Name</label>
                        <input type="text" name="owner_name" value="{{ old('owner_name', $tenant->owner_name) }}" required
                               class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-violet-500">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $tenant->phone) }}" required
                               class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-violet-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Address</label>
                        <input type="text" name="address" value="{{ old('address', $tenant->address) }}"
                               class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-violet-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Background Theme</label>
                    <select name="theme" class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-violet-500">
                        @foreach([
                            'slate' => 'Slate Mist',
                            'indigo' => 'Indigo Dawn',
                            'emerald' => 'Emerald Breeze',
                            'fuchsia' => 'Rose Bloom',
                        ] as $key => $label)
                            <option value="{{ $key }}" @selected(old('theme', $tenant->theme ?? 'slate') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-slate-400">Applies to the full workspace background, cards, and UI accents.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Company Logo</label>
                    <input type="file" name="logo" accept="image/*" class="rr-file">
                    @if(!empty($tenant->logo_path))
                        <img src="{{ asset('storage/' . $tenant->logo_path) }}" alt="Current company logo" class="mt-2 h-12 w-12 rounded-lg object-cover ring-1 ring-slate-600/70">
                    @endif
                    <p class="mt-1 text-xs text-slate-400">Optional. Shown in tenant header. Max 5MB.</p>
                </div>

                <div class="border-t border-slate-700/80 pt-5 mt-5">
                    <h4 class="text-sm font-semibold text-slate-200 mb-1">Customer-facing (RentRide)</h4>
                    <p class="text-xs text-slate-400 mb-4">Shown to customers on your vehicle list and booking pages.</p>

                    <div>
                        <label class="block text-sm font-medium mb-1">Short tagline</label>
                        <input type="text" name="public_tagline" value="{{ old('public_tagline', $tenant->public_tagline) }}" maxlength="255"
                               placeholder="e.g. Reliable rentals · Metro pickup"
                               class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-violet-500">
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium mb-1">Company website</label>
                        <input type="url" name="website_url" value="{{ old('website_url', $tenant->website_url) }}"
                               placeholder="https://example.com"
                               class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-violet-500">
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium mb-1">Booking & pickup notes</label>
                        <textarea name="public_booking_notes" rows="4" maxlength="5000"
                                  placeholder="Hours, payment at pickup, ID requirements…"
                                  class="w-full min-h-[5rem] rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-violet-500">{{ old('public_booking_notes', $tenant->public_booking_notes) }}</textarea>
                        <p class="mt-1 text-xs text-slate-400">Plain text.</p>
                    </div>
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="inline-flex items-center rounded-lg rr-btn-primary px-4 py-2 text-sm font-semibold">
                    Save Changes
                </button>
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-slate-800 bg-slate-900/60 p-4 sm:p-5">
        <h4 class="text-sm font-semibold text-slate-200 mb-2">Platform</h4>
        <p class="text-xs text-slate-400 mb-4">About page includes platform version, release notes, and support contact details.</p>
        <div class="text-sm text-slate-300 space-y-1">
            <p><span class="text-slate-500">Version:</span> {{ $rr['version'] ?? '—' }}</p>
            @if(!empty($rr['git_sha']))
                <p><span class="text-slate-500">Build:</span> <span class="font-mono text-xs">{{ $rr['git_sha'] }}</span></p>
            @endif
        </div>
        <a href="{{ route('admin.about') }}" class="mt-4 inline-flex items-center rounded-lg border border-slate-600 px-4 py-2 text-sm font-semibold text-slate-200 hover:bg-slate-800/80">
            Open About page
        </a>
    </div>
</form>
@endsection
