@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="mb-4 sm:mb-6">
    <h3 class="text-xl font-semibold">My Profile</h3>
    <p class="text-sm text-slate-300">Update your account, company details, and theme.</p>
</div>

<div class="grid grid-cols-1 gap-5 lg:grid-cols-2 lg:gap-6">
    <div class="rounded-xl border border-slate-800 bg-slate-900/60 p-4 sm:p-5">
        <h4 class="text-sm font-semibold text-slate-200 mb-3">Account</h4>
        <div class="text-xs text-slate-400 mb-4">Used to log in to your tenant dashboard.</div>

        <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium mb-1">Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                       class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-violet-500">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                       class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-violet-500">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">New Password (optional)</label>
                    <input type="password" name="password"
                           class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-violet-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Confirm New Password</label>
                    <input type="password" name="password_confirmation"
                           class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-violet-500">
                </div>
            </div>

            <div class="pt-2">
                <button class="inline-flex items-center rounded-lg rr-btn-primary px-4 py-2 text-sm font-semibold">
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    <div class="rounded-xl border border-slate-800 bg-slate-900/60 p-4 sm:p-5">
        <h4 class="text-sm font-semibold text-slate-200 mb-3">Company & Theme</h4>
        <div class="text-xs text-slate-400 mb-4">These settings affect your tenant workspace.</div>

        <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Company Name</label>
                    <input type="text" name="company_name" value="{{ old('company_name', $tenant->company_name) }}"
                           class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-violet-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Owner Name</label>
                    <input type="text" name="owner_name" value="{{ old('owner_name', $tenant->owner_name) }}"
                           class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-violet-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $tenant->phone) }}"
                           class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-violet-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Address</label>
                    <input type="text" name="address" value="{{ old('address', $tenant->address) }}"
                           class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-violet-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Theme</label>
                <select name="theme" class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-violet-500">
                    @foreach(['slate' => 'Slate (Default)', 'indigo' => 'Indigo', 'emerald' => 'Emerald', 'fuchsia' => 'Fuchsia'] as $key => $label)
                        <option value="{{ $key }}" @selected(old('theme', $tenant->theme ?? 'slate') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-slate-400">Safe colors that keep contrast readable.</p>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Company Logo</label>
                <input type="file" name="logo" accept="image/*" class="rr-file">
                @if(!empty($tenant->logo_path))
                    <img src="{{ asset('storage/' . $tenant->logo_path) }}" alt="Current company logo" class="mt-2 h-12 w-12 rounded-lg object-cover ring-1 ring-slate-600/70">
                @endif
                <p class="mt-1 text-xs text-slate-400">Optional. Shown in tenant header. Max 5MB.</p>
            </div>

            <!-- Hidden account fields so the shared update endpoint validates -->
            <input type="hidden" name="name" value="{{ old('name', $user->name) }}">
            <input type="hidden" name="email" value="{{ old('email', $user->email) }}">
            <input type="hidden" name="password" value="">
            <input type="hidden" name="password_confirmation" value="">

            <div class="pt-2">
                <button class="inline-flex items-center rounded-lg rr-btn-primary px-4 py-2 text-sm font-semibold">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

