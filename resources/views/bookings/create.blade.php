@extends('layouts.app')

@section('title', 'Create Booking')

@section('content')
<h3 class="mb-3">Create Booking</h3>
<form method="POST" action="{{ route('bookings.store') }}">
    @csrf
    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label">Vehicle</label>
            <select name="vehicle_id" class="form-select" required>
                <option value="">Select vehicle</option>
                @foreach($vehicles as $vehicle)
                    <option value="{{ $vehicle->id }}" @selected(old('vehicle_id') == $vehicle->id)>
                        {{ $vehicle->vehicle_name }} ({{ $vehicle->plate_number }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Customer</label>
            <select name="customer_id" class="form-select" required>
                <option value="">Select customer</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>
                        {{ $customer->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label">Start Date</label>
            <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">End Date</label>
            <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}" required>
        </div>
    </div>
    <button class="btn btn-primary">Create</button>
    <a href="{{ route('bookings.index') }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection

