<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;
use App\Models\Vehicle;
use App\Support\PlanLimits;
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

    public function store(StoreVehicleRequest $request)
    {
        $tenantId = $this->tenantId();
        $tenant = Auth::user()->tenant;
        $vehicleCount = Vehicle::where('tenant_id', $tenantId)->count();

        if (! PlanLimits::canCreateVehicle($tenant->subscription_plan ?? 'basic', $vehicleCount)) {
            return redirect()->route('vehicles.index')->withErrors([
                'limit' => 'You have reached your vehicle limit for the current plan.',
            ]);
        }

        $data = $request->validated();

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

    public function update(UpdateVehicleRequest $request, Vehicle $vehicle)
    {
        $this->authorizeTenantAccess($vehicle);

        $data = $request->validated();

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

