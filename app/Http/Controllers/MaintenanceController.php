<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;

class MaintenanceController extends TenantControllerBase
{
    public function index()
    {
        $vehicles = Vehicle::where('tenant_id', $this->tenantId())
            ->where('status', 'maintenance')
            ->latest()
            ->paginate(15);

        return view('vehicles.maintenance', compact('vehicles'));
    }
}
