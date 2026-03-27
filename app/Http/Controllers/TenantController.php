<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|confirmed|min:8',
        ]);

        $tenant->update([
            'company_name' => $data['company_name'],
            'owner_name' => $data['owner_name'],
            'phone' => $data['phone'],
            'address' => $data['address'] ?? null,
            'theme' => $data['theme'],
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];
        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();

        return back()->with('success', 'Profile updated.');
    }
}

