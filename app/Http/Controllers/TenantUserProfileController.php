<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TenantUserProfileController extends Controller
{
    /**
     * Staff-only: update own name, email, password (not company settings).
     */
    public function edit()
    {
        $user = Auth::user();
        if ($user && $user->isAdmin()) {
            return redirect()->route('admin.profile');
        }
        abort_unless($user && $user->isStaff(), 403);

        return view('admin.staff.my-profile', ['user' => $user]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        if ($user && $user->isAdmin()) {
            return redirect()->route('admin.profile');
        }
        abort_unless($user && $user->isStaff(), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', 'min:8'],
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];
        if (! empty($data['password'])) {
            $user->password = $data['password'];
        }
        $user->save();

        return back()->with('success', 'Profile updated.');
    }
}
