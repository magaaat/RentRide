@extends('layouts.app')

@section('title', 'Create Booking')

@section('content')
<div class="mx-auto max-w-3xl">
    <div class="mb-8">
        <h1 class="text-2xl font-semibold tracking-tight text-slate-50">Create booking</h1>
        <p class="mt-1 text-sm text-slate-400">Assign a vehicle and customer for the selected dates.</p>
    </div>

    <div class="rr-panel-elevated p-6 sm:p-8">
        <form method="POST" action="{{ route('bookings.store') }}" class="space-y-5">
            @csrf
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label class="rr-label" for="vehicle_id">Vehicle</label>
                    <select id="vehicle_id" name="vehicle_id" required class="rr-input">
                        <option value="">Select vehicle</option>
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}" @selected(old('vehicle_id') == $vehicle->id)>
                                {{ $vehicle->vehicle_name }} ({{ $vehicle->plate_number }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="rr-label" for="customer_id">Customer</label>
                    <select id="customer_id" name="customer_id" required class="rr-input">
                        <option value="">Select customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="rr-label" for="start_date">Start date</label>
                    <input id="start_date" type="date" name="start_date" value="{{ old('start_date') }}" required class="rr-input">
                </div>
                <div>
                    <label class="rr-label" for="end_date">End date</label>
                    <input id="end_date" type="date" name="end_date" value="{{ old('end_date') }}" required class="rr-input">
                </div>
            </div>
            <div class="flex flex-wrap gap-3 pt-2">
                <button type="submit" class="inline-flex items-center rounded-lg rr-btn-primary px-5 py-2.5 text-sm font-semibold shadow-sm">Create</button>
                <a href="{{ route('bookings.index') }}" class="inline-flex items-center rounded-lg rr-btn-secondary px-5 py-2.5 text-sm font-semibold">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
