@extends('layouts.app')

@section('title', $vehicle->vehicle_name)

@section('content')
<div class="mb-6">
    <a href="{{ route('customer.tenants.vehicles', $tenant) }}" class="text-sm text-violet-300 hover:text-violet-200">{{ $tenant->company_name }} vehicles</a>
</div>

<div class="grid gap-8 lg:grid-cols-2">
    <div class="rounded-xl border border-slate-800 bg-slate-900/60 overflow-hidden">
        <div class="aspect-video bg-slate-800 flex items-center justify-center text-slate-500">
            @if($vehicle->image)
                <img src="{{ asset('storage/'.$vehicle->image) }}" alt="" class="w-full h-full object-cover">
            @else
                No image
            @endif
        </div>
    </div>
    <div>
        <h1 class="text-2xl font-semibold">{{ $vehicle->vehicle_name }}</h1>
        <p class="text-slate-400 mt-1">{{ $vehicle->brand }} · {{ $vehicle->vehicle_type }} · {{ $vehicle->plate_number }}</p>
        <p class="mt-4 text-3xl font-bold text-violet-300">₱{{ number_format($vehicle->price_per_day, 2) }}<span class="text-base font-normal text-slate-400"> / day</span></p>
        @if($vehicle->description)
            <p class="mt-4 text-sm text-slate-300 leading-relaxed">{{ $vehicle->description }}</p>
        @endif
        <div class="mt-4 rounded-lg border border-slate-700 bg-slate-900/50 p-4 text-sm text-slate-400">
            <div class="font-semibold text-slate-200 mb-1">Rental company</div>
            <div>{{ $tenant->company_name }}</div>
            @if($tenant->public_tagline)
                <p class="mt-1 text-violet-200/90">{{ $tenant->public_tagline }}</p>
            @endif
            <div class="mt-2">{{ $tenant->address ?? '—' }}</div>
            @if($tenant->phone)
                <div class="mt-1">{{ $tenant->phone }}</div>
            @endif
            @if($tenant->website_url)
                <a href="{{ $tenant->website_url }}" target="_blank" rel="noopener noreferrer" class="mt-2 inline-block text-sm font-semibold text-violet-300 hover:text-violet-200">Visit website</a>
            @endif
        </div>

        @if($tenant->public_booking_notes)
            <div class="mt-4 rounded-lg border border-slate-700 bg-slate-900/50 p-4 text-sm text-slate-300">
                <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">From the rental company</div>
                <div class="mt-2 whitespace-pre-line leading-relaxed">{{ $tenant->public_booking_notes }}</div>
            </div>
        @endif

        @if($vehicle->status !== 'available')
            <p class="mt-6 text-amber-200 text-sm">This vehicle is not available for new bookings right now.</p>
        @elseif(!$canRent)
            <div class="mt-8 rounded-lg border border-amber-500/40 bg-amber-500/10 p-4 text-sm text-amber-100">
                <p class="font-semibold text-amber-50">Driver’s license required</p>
                <p class="mt-1 text-amber-200/90">Upload clear photos of the front and back of your license in your profile to book a vehicle.</p>
                <a href="{{ route('customer.profile') }}" class="mt-3 inline-block text-sm font-semibold text-violet-300 hover:text-violet-200">Go to profile</a>
            </div>
        @else
            <h2 class="mt-8 text-lg font-semibold">Request a reservation</h2>
            <p class="text-sm text-slate-400 mb-4">Submit dates — the rental company confirms after they receive payment (for example cash at pickup). Estimated total is the daily rate × number of calendar days.</p>
            <form method="POST" action="{{ route('customer.vehicles.book', [$tenant, $vehicle]) }}" class="space-y-4 max-w-md">
                @csrf
                <div>
                    <label class="block text-sm font-medium mb-1">Pick-up date</label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}" required min="{{ now()->toDateString() }}"
                        class="w-full rounded-lg border border-slate-600 bg-slate-900/70 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Return date</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}" required min="{{ now()->toDateString() }}"
                        class="w-full rounded-lg border border-slate-600 bg-slate-900/70 px-3 py-2 text-sm">
                </div>
                @error('date')
                    <p class="text-sm text-red-400">{{ $message }}</p>
                @enderror
                @error('vehicle')
                    <p class="text-sm text-red-400">{{ $message }}</p>
                @enderror
                <button type="submit" class="rr-btn-primary w-full rounded-lg py-2.5 text-sm font-semibold">
                    Submit booking request
                </button>
            </form>
        @endif
    </div>
</div>
@endsection
