@extends('layouts.app')

@section('title', 'Edit Vehicle')

@section('content')
<div class="mx-auto max-w-3xl">
    <div class="mb-8">
        <h1 class="text-2xl font-semibold tracking-tight text-slate-50">Edit vehicle</h1>
        <p class="mt-1 text-sm text-slate-400">{{ $vehicle->vehicle_name }}</p>
    </div>

    <div class="rr-panel-elevated p-6 sm:p-8">
        <form method="POST" action="{{ route('vehicles.update', $vehicle) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label class="rr-label" for="vehicle_name">Vehicle name</label>
                    <input id="vehicle_name" type="text" name="vehicle_name" value="{{ old('vehicle_name', $vehicle->vehicle_name) }}" required class="rr-input">
                </div>
                <div>
                    <label class="rr-label" for="vehicle_type">Vehicle type</label>
                    <input id="vehicle_type" type="text" name="vehicle_type" value="{{ old('vehicle_type', $vehicle->vehicle_type) }}" required class="rr-input">
                </div>
            </div>
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                <div>
                    <label class="rr-label" for="brand">Brand</label>
                    <input id="brand" type="text" name="brand" value="{{ old('brand', $vehicle->brand) }}" required class="rr-input">
                </div>
                <div>
                    <label class="rr-label" for="plate_number">Plate number</label>
                    <input id="plate_number" type="text" name="plate_number" value="{{ old('plate_number', $vehicle->plate_number) }}" required class="rr-input">
                </div>
                <div>
                    <label class="rr-label" for="price_per_day">Price per day (₱)</label>
                    <input id="price_per_day" type="number" step="0.01" name="price_per_day" value="{{ old('price_per_day', $vehicle->price_per_day) }}" required class="rr-input">
                </div>
            </div>
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label class="rr-label" for="status">Status</label>
                    <select id="status" name="status" class="rr-input">
                        @foreach(['available','rented','maintenance','inactive'] as $status)
                            <option value="{{ $status }}" @selected($vehicle->status === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="rr-label" for="image">New image</label>
                    <input id="image" type="file" name="image" class="rr-file">
                    @if($vehicle->image)
                        <p class="mt-1.5 text-xs text-slate-500">Current file: {{ $vehicle->image }}</p>
                    @endif
                </div>
            </div>
            <div>
                <label class="rr-label" for="description">Description</label>
                <textarea id="description" name="description" rows="3" class="rr-textarea">{{ old('description', $vehicle->description) }}</textarea>
            </div>
            <div class="flex flex-wrap gap-3 pt-2">
                <button type="submit" class="inline-flex items-center rounded-lg rr-btn-primary px-5 py-2.5 text-sm font-semibold shadow-sm">Update</button>
                <a href="{{ route('vehicles.index') }}" class="inline-flex items-center rounded-lg rr-btn-secondary px-5 py-2.5 text-sm font-semibold">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
