@extends('layouts.app')

@section('title', 'Add Vehicle')

@section('content')
<div class="mx-auto max-w-3xl">
    <div class="mb-8">
        <h1 class="text-2xl font-semibold tracking-tight text-slate-50">Add vehicle</h1>
        <p class="mt-1 text-sm text-slate-400">Add a vehicle to your fleet with pricing and status.</p>
    </div>

    <div class="rr-panel-elevated p-6 sm:p-8">
        <form method="POST" action="{{ route('vehicles.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label class="rr-label" for="vehicle_name">Vehicle name</label>
                    <input id="vehicle_name" type="text" name="vehicle_name" value="{{ old('vehicle_name') }}" required class="rr-input">
                </div>
                <div>
                    <label class="rr-label" for="vehicle_type">Vehicle type</label>
                    <input id="vehicle_type" type="text" name="vehicle_type" value="{{ old('vehicle_type') }}" required class="rr-input">
                </div>
            </div>
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                <div>
                    <label class="rr-label" for="brand">Brand</label>
                    <input id="brand" type="text" name="brand" value="{{ old('brand') }}" required class="rr-input">
                </div>
                <div>
                    <label class="rr-label" for="plate_number">Plate number</label>
                    <input id="plate_number" type="text" name="plate_number" value="{{ old('plate_number') }}" required class="rr-input">
                </div>
                <div>
                    <label class="rr-label" for="price_per_day">Price per day (₱)</label>
                    <input id="price_per_day" type="number" step="0.01" name="price_per_day" value="{{ old('price_per_day') }}" required class="rr-input">
                </div>
            </div>
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label class="rr-label" for="status">Status</label>
                    <select id="status" name="status" class="rr-input">
                        <option value="available">Available</option>
                        <option value="rented">Rented</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div>
                    <label class="rr-label" for="image">Image</label>
                    <input id="image" type="file" name="image" class="rr-file">
                </div>
            </div>
            <div>
                <label class="rr-label" for="description">Description</label>
                <textarea id="description" name="description" rows="3" class="rr-textarea">{{ old('description') }}</textarea>
            </div>
            <div id="maintenance-details-section" class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-700">Maintenance details</h3>
                <p class="mt-1 text-xs text-slate-500">Fill these if this vehicle is currently in maintenance.</p>
                <div class="mt-3 grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="rr-label" for="maintenance_issue">Issue/Problem</label>
                        <textarea id="maintenance_issue" name="maintenance_issue" rows="2" class="rr-textarea" data-maintenance-required="1">{{ old('maintenance_issue') }}</textarea>
                    </div>
                    <div>
                        <label class="rr-label" for="maintenance_severity">Severity</label>
                        <select id="maintenance_severity" name="maintenance_severity" class="rr-input" data-maintenance-required="1">
                            <option value="">Select severity</option>
                            @foreach(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'critical' => 'Critical'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('maintenance_severity') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="rr-label" for="maintenance_cost_estimate">Estimated cost (₱)</label>
                        <input id="maintenance_cost_estimate" type="number" step="0.01" min="0" name="maintenance_cost_estimate" value="{{ old('maintenance_cost_estimate') }}" class="rr-input">
                    </div>
                    <div>
                        <label class="rr-label" for="maintenance_reported_at">Reported date</label>
                        <input id="maintenance_reported_at" type="date" name="maintenance_reported_at" value="{{ old('maintenance_reported_at') }}" class="rr-input" data-maintenance-required="1">
                    </div>
                    <div>
                        <label class="rr-label" for="maintenance_target_fix_at">Target fix date</label>
                        <input id="maintenance_target_fix_at" type="date" name="maintenance_target_fix_at" value="{{ old('maintenance_target_fix_at') }}" class="rr-input">
                    </div>
                    <div>
                        <label class="rr-label" for="maintenance_fixed_at">Actual fixed date</label>
                        <input id="maintenance_fixed_at" type="date" name="maintenance_fixed_at" value="{{ old('maintenance_fixed_at') }}" class="rr-input">
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap gap-3 pt-2">
                <button type="submit" class="inline-flex items-center rounded-lg rr-btn-primary px-5 py-2.5 text-sm font-semibold shadow-sm">Save</button>
                <a href="{{ route('vehicles.index') }}" class="inline-flex items-center rounded-lg rr-btn-secondary px-5 py-2.5 text-sm font-semibold">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const statusField = document.getElementById('status');
        const section = document.getElementById('maintenance-details-section');
        if (!statusField || !section) return;

        const requiredFields = section.querySelectorAll('[data-maintenance-required="1"]');

        const updateSectionVisibility = function () {
            const show = statusField.value === 'maintenance';
            section.classList.toggle('hidden', !show);
            requiredFields.forEach((field) => {
                field.required = show;
            });
        };

        updateSectionVisibility();
        statusField.addEventListener('change', updateSectionVisibility);
    });
</script>
@endpush
