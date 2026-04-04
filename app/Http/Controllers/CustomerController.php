<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends TenantControllerBase
{
    public function index()
    {
        $customers = Customer::where('tenant_id', $this->tenantId())
            ->latest()
            ->paginate(20);

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $data['tenant_id'] = $this->tenantId();

        Customer::create($data);

        return redirect()->route('customers.index')->with('success', 'Customer created.');
    }

    public function show(Customer $customer)
    {
        $this->authorizeTenantAccess($customer);

        $portalUser = null;
        if ($customer->email) {
            $portalUser = User::where('email', $customer->email)
                ->where('role', 'customer')
                ->first();
        }

        return view('customers.show', compact('customer', 'portalUser'));
    }

    public function destroy(Customer $customer)
    {
        $this->authorizeTenantAccess($customer);
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer deleted.');
    }
}

