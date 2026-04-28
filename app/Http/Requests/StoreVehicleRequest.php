<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vehicle_name' => ['required', 'string', 'max:255'],
            'vehicle_type' => ['required', 'string', 'max:255'],
            'brand' => ['required', 'string', 'max:255'],
            'plate_number' => ['required', 'string', 'max:255', 'unique:vehicles,plate_number'],
            'price_per_day' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:available,rented,maintenance,inactive'],
            'description' => ['nullable', 'string'],
            'maintenance_issue' => ['nullable', 'required_if:status,maintenance', 'string', 'max:2000'],
            'maintenance_severity' => ['nullable', 'required_if:status,maintenance', 'in:low,medium,high,critical'],
            'maintenance_reported_at' => ['nullable', 'required_if:status,maintenance', 'date'],
            'maintenance_target_fix_at' => ['nullable', 'date', 'after_or_equal:maintenance_reported_at'],
            'maintenance_fixed_at' => ['nullable', 'date', 'after_or_equal:maintenance_reported_at'],
            'maintenance_cost_estimate' => ['nullable', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'max:2048'],
        ];
    }
}
