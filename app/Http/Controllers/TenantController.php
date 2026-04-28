<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateTenantProfileRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class TenantController extends Controller
{
    private const DEFAULT_TENANT_NAV_SEQUENCE = [
        'dashboard',
        'vehicles',
        'maintenance',
        'customers',
        'reports',
        'analytics',
        'bookings',
        'calendar',
        'payments',
        'staff',
        'updated_module',
        'about',
    ];

    private const TENANT_NAV_LABELS = [
        'dashboard' => 'Dashboard',
        'vehicles' => 'Vehicles',
        'maintenance' => 'Maintenance',
        'customers' => 'Customers',
        'reports' => 'Reports',
        'analytics' => 'Analytics',
        'bookings' => 'Bookings',
        'calendar' => 'Calendar',
        'payments' => 'Payments',
        'staff' => 'Staff',
        'updated_module' => 'Updated module',
        'about' => 'About',
    ];

    public function profile()
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        $tenantNavLabels = self::TENANT_NAV_LABELS;
        $tenantNavDefaultSequence = self::DEFAULT_TENANT_NAV_SEQUENCE;

        return view('admin.tenant.profile', compact('tenant', 'user', 'tenantNavLabels', 'tenantNavDefaultSequence'));
    }

    public function updateProfile(UpdateTenantProfileRequest $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $data = $request->validated();
        $requestedSequence = array_values(array_filter(
            $data['navbar_sequence'] ?? [],
            fn ($key) => is_string($key) && $key !== ''
        ));
        $navbarSequence = [];
        foreach ($requestedSequence as $key) {
            if (in_array($key, self::DEFAULT_TENANT_NAV_SEQUENCE, true) && ! in_array($key, $navbarSequence, true)) {
                $navbarSequence[] = $key;
            }
        }
        foreach (self::DEFAULT_TENANT_NAV_SEQUENCE as $defaultKey) {
            if (! in_array($defaultKey, $navbarSequence, true)) {
                $navbarSequence[] = $defaultKey;
            }
        }

        $tenant->update([
            'company_name' => $data['company_name'],
            'owner_name' => $data['owner_name'],
            'phone' => $data['phone'],
            'address' => $data['address'] ?? null,
            'theme' => $data['theme'],
            'public_tagline' => $data['public_tagline'] ?? null,
            'website_url' => $data['website_url'] ?? null,
            'public_booking_notes' => $data['public_booking_notes'] ?? null,
            'navbar_sequence' => $navbarSequence,
        ]);

        if ($request->hasFile('logo')) {
            if ($tenant->logo_path) {
                Storage::disk('public')->delete($tenant->logo_path);
            }
            $tenant->logo_path = $request->file('logo')->store('tenant-logos', 'public');
            $tenant->save();
        }

        $user->name = $data['name'];
        $user->email = $data['email'];
        if (! empty($data['new_password'])) {
            $user->password = Hash::make($data['new_password']);
        }
        $user->save();

        return back()->with('success', 'Profile updated.');
    }
}

