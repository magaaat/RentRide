<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateTenantUserProfileRequest;
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

    public function update(UpdateTenantUserProfileRequest $request)
    {
        $user = Auth::user();
        if ($user && $user->isAdmin()) {
            return redirect()->route('admin.profile');
        }
        abort_unless($user && $user->isStaff(), 403);

        $data = $request->validated();

        $user->name = $data['name'];
        $user->email = $data['email'];
        if (! empty($data['password'])) {
            $user->password = $data['password'];
        }
        $user->save();

        return back()->with('success', 'Profile updated.');
    }
}
