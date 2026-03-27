<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Support\PlanLimits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VehicleController extends TenantControllerBase
{
    public function index()
    {
        $vehicles = Vehicle::where('tenant_id', $this->tenantId())
            ->latest()
            ->paginate(15);

        return view('vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        $tenant = Auth::user()->tenant;
        $vehicleCount = Vehicle::where('tenant_id', $tenant->id)->count();

        if (! PlanLimits::canCreateVehicle($tenant->subscription_plan ?? 'basic', $vehicleCount)) {
            return redirect()->route('vehicles.index')->withErrors([
                'limit' => 'You have reached your vehicle limit for the current plan.',
            ]);
        }

        return view('vehicles.create');
    }

    public function store(Request $request)
    {
        $tenantId = $this->tenantId();
        $tenant = Auth::user()->tenant;
        $vehicleCount = Vehicle::where('tenant_id', $tenantId)->count();

        if (! PlanLimits::canCreateVehicle($tenant->subscription_plan ?? 'basic', $vehicleCount)) {
            return redirect()->route('vehicles.index')->withErrors([
                'limit' => 'You have reached your vehicle limit for the current plan.',
            ]);
        }

        $data = $request->validate([
            'vehicle_name' => 'required|string|max:255',
            'vehicle_type' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'plate_number' => 'required|string|max:255|unique:vehicles,plate_number',
            'price_per_day' => 'required|numeric|min:0',
            'status' => 'required|in:available,rented,maintenance,inactive',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('vehicles', 'public');
        }

        $data['tenant_id'] = $tenantId;

        Vehicle::create($data);

        return redirect()->route('vehicles.index')->with('success', 'Vehicle added.');
    }

    public function edit(Vehicle $vehicle)
    {
        $this->authorizeTenantAccess($vehicle);

        return view('vehicles.edit', compact('vehicle'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $this->authorizeTenantAccess($vehicle);

        $data = $request->validate([
            'vehicle_name' => 'required|string|max:255',
            'vehicle_type' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'plate_number' => 'required|string|max:255|unique:vehicles,plate_number,' . $vehicle->id,
            'price_per_day' => 'required|numeric|min:0',
            'status' => 'required|in:available,rented,maintenance,inactive',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('vehicles', 'public');
        }

        $vehicle->update($data);

        return redirect()->route('vehicles.index')->with('success', 'Vehicle updated.');
    }

    public function destroy(Vehicle $vehicle)
    {
        $this->authorizeTenantAccess($vehicle);
        $vehicle->delete();

        return redirect()->route('vehicles.index')->with('success', 'Vehicle deleted.');
    }
}

