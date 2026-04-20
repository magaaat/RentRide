<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $data = $request->validate([
            'company_name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'theme' => 'required|in:slate,indigo,emerald,fuchsia',
            'logo' => 'nullable|image|max:5120',
            'public_tagline' => 'nullable|string|max:255',
            'website_url' => 'nullable|url|max:512',
            'public_booking_notes' => 'nullable|string|max:5000',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'new_password' => 'nullable|confirmed|min:8',
        ]);

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

