<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateTenantProfileRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class TenantController extends Controller
{
    public function profile()
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        return view('admin.tenant.profile', compact('tenant', 'user'));
    }

    public function updateProfile(UpdateTenantProfileRequest $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $data = $request->validated();

        $tenant->update([
            'company_name' => $data['company_name'],
            'owner_name' => $data['owner_name'],
            'phone' => $data['phone'],
            'address' => $data['address'] ?? null,
            'theme' => $data['theme'],
            'public_tagline' => $data['public_tagline'] ?? null,
            'website_url' => $data['website_url'] ?? null,
            'public_booking_notes' => $data['public_booking_notes'] ?? null,
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

