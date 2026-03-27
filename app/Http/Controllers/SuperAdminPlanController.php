<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdminPlanController extends Controller
{
    public function index()
    {
        abort_unless(Auth::user()?->isSuperAdmin(), 403);

        $plans = SubscriptionPlan::orderByRaw("FIELD(`key`, 'basic', 'standard', 'premium')")->get();

        return view('superadmin.plans.index', compact('plans'));
    }

    public function edit(SubscriptionPlan $plan)
    {
        abort_unless(Auth::user()?->isSuperAdmin(), 403);

        return view('superadmin.plans.edit', compact('plan'));
    }

    public function update(Request $request, SubscriptionPlan $plan)
    {
        abort_unless(Auth::user()?->isSuperAdmin(), 403);

        $data = $request->validate([
            'base_price' => 'required|numeric|min:0',
            'discount_type' => 'required|in:none,percent,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $data['discount_value'] = $data['discount_type'] === 'none' ? 0 : (float) ($data['discount_value'] ?? 0);
        $data['is_active'] = $request->boolean('is_active');

        $plan->update($data);

        return redirect()->route('superadmin.plans.index')->with('success', 'Plan updated.');
    }
}

