<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Mail\CustomerWelcomeMail;
use App\Models\Customer;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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

    public function store(StoreCustomerRequest $request)
    {
        $tenantId = $this->tenantId();
        abort_unless($tenantId, 403);

        $data = $request->validated();

        $data['tenant_id'] = $tenantId;

        Customer::create($data);

        $plainPassword = Str::password(12);
        $customerUser = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            // User model hashes this via casts()
            'password' => $plainPassword,
            'role' => 'customer',
            'tenant_id' => $tenantId,
        ]);

        $tenant = Auth::user()?->tenant ?: Tenant::find($tenantId);
        if ($tenant) {
            try {
                Mail::to($customerUser->email)->send(
                    new CustomerWelcomeMail($tenant, $customerUser, $plainPassword)
                );
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return redirect()->route('customers.index')->with('success', 'Customer created and login credentials were sent by email.');
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

