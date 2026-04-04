@extends('layouts.app')

@section('title', 'My Profile - RentRide')

@section('content')
<div class="mx-auto max-w-2xl">
    <div class="mb-8">
        <h1 class="text-2xl font-semibold tracking-tight text-slate-50 sm:text-3xl">My profile</h1>
        <p class="mt-2 text-sm leading-relaxed text-slate-400">Your account details, password, and driver’s license photos.</p>
    </div>

    <div class="rr-panel-elevated p-6 sm:p-8">
        <form method="POST" action="{{ route('customer.profile.update') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="rr-label" for="name">Full name</label>
                <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required class="rr-input">
            </div>

            <div>
                <label class="rr-label" for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required class="rr-input">
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label class="rr-label" for="phone">Phone <span class="font-normal text-slate-500">(optional)</span></label>
                    <input id="phone" type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="rr-input">
                </div>
                <div>
                    <label class="rr-label" for="address">Address <span class="font-normal text-slate-500">(optional)</span></label>
                    <input id="address" type="text" name="address" value="{{ old('address', $user->address) }}" class="rr-input">
                </div>
            </div>

            <div class="rr-panel rounded-lg p-4 sm:p-5">
                <h2 class="text-sm font-semibold text-slate-200">Driver’s license</h2>
                <p class="mt-1 text-xs leading-relaxed text-slate-500">Upload front and back before renting. JPG or PNG, up to 5&nbsp;MB each.</p>
                <div class="mt-4 grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label class="rr-label" for="license_front">Front</label>
                        @if($user->driver_license_front_path)
                            <p class="mb-2 text-xs font-medium text-violet-300/90">On file</p>
                            <img src="{{ asset('storage/'.$user->driver_license_front_path) }}" alt="" class="mb-3 max-h-32 rounded-lg border border-slate-600 object-contain">
                        @endif
                        <input id="license_front" type="file" name="license_front" accept="image/*" class="rr-file">
                        @error('license_front')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="rr-label" for="license_back">Back</label>
                        @if($user->driver_license_back_path)
                            <p class="mb-2 text-xs font-medium text-violet-300/90">On file</p>
                            <img src="{{ asset('storage/'.$user->driver_license_back_path) }}" alt="" class="mb-3 max-h-32 rounded-lg border border-slate-600 object-contain">
                        @endif
                        <input id="license_back" type="file" name="license_back" accept="image/*" class="rr-file">
                        @error('license_back')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                @error('license')
                    <p class="text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label class="rr-label" for="password">New password <span class="font-normal text-slate-500">(optional)</span></label>
                    <input id="password" type="password" name="password" class="rr-input" autocomplete="new-password">
                </div>
                <div>
                    <label class="rr-label" for="password_confirmation">Confirm password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" class="rr-input" autocomplete="new-password">
                </div>
            </div>

            <div class="flex flex-wrap gap-3 pt-2">
                <button type="submit" class="rr-btn-primary inline-flex items-center rounded-lg px-5 py-2.5 text-sm font-semibold shadow-sm transition">
                    Save changes
                </button>
                <a href="{{ route('customer.dashboard') }}" class="inline-flex items-center rounded-lg border border-slate-600 px-5 py-2.5 text-sm font-semibold text-slate-200 transition hover:bg-slate-800">
                    Back to dashboard
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
