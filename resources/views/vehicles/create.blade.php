@extends('layouts.app')

@section('title', 'Add Vehicle')

@section('content')
<h3 class="mb-3">Add Vehicle</h3>
<form method="POST" action="{{ route('vehicles.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Vehicle Name</label>
            <input type="text" name="vehicle_name" class="form-control" value="{{ old('vehicle_name') }}" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Vehicle Type</label>
            <input type="text" name="vehicle_type" class="form-control" value="{{ old('vehicle_type') }}" required>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 mb-3">
            <label class="form-label">Brand</label>
            <input type="text" name="brand" class="form-control" value="{{ old('brand') }}" required>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Plate Number</label>
            <input type="text" name="plate_number" class="form-control" value="{{ old('plate_number') }}" required>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Price Per Day</label>
            <input type="number" step="0.01" name="price_per_day" class="form-control" value="{{ old('price_per_day') }}" required>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="available">Available</option>
                <option value="rented">Rented</option>
                <option value="maintenance">Maintenance</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Image</label>
            <input type="file" name="image" class="form-control">
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
    </div>
    <button class="inline-flex items-center rounded-lg rr-btn-primary px-4 py-2 text-sm font-semibold">Save</button>
    <a href="{{ route('vehicles.index') }}" class="inline-flex items-center rounded-lg rr-btn-secondary px-4 py-2 text-sm font-semibold">Cancel</a>
</form>
@endsection

