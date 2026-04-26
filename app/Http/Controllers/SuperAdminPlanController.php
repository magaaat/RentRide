<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubscriptionPlanRequest;
use App\Http\Requests\UpdateSubscriptionPlanRequest;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;

class SuperAdminPlanController extends Controller
{
    public function index()
    {
        abort_unless(Auth::user()?->isSuperAdmin(), 403);

        $plans = SubscriptionPlan::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('superadmin.plans.index', compact('plans'));
    }

    public function create()
    {
        abort_unless(Auth::user()?->isSuperAdmin(), 403);

        return view('superadmin.plans.create');
    }

    public function store(StoreSubscriptionPlanRequest $request)
    {
        abort_unless(Auth::user()?->isSuperAdmin(), 403);

        $data = $request->validated();

        $features = [];
        if (! empty($data['features_text'])) {
            $lines = preg_split('/\r\n|\r|\n/', $data['features_text']);
            $features = array_values(array_filter(array_map('trim', $lines)));
        }

        SubscriptionPlan::create([
            'key' => $data['key'],
            'name' => $data['name'],
            'tier' => $data['tier'],
            'feature_tier' => $data['feature_tier'],
            'base_price' => $data['base_price'],
            'billing_period' => $data['billing_period'],
            'currency' => strtoupper($data['currency']),
            'discount_type' => $data['discount_type'],
            'discount_value' => $data['discount_type'] === 'none' ? 0 : (float) ($data['discount_value'] ?? 0),
            'features' => $features ?: null,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'is_active' => $request->boolean('enable_plan'),
            'show_on_landing' => $request->boolean('show_on_landing'),
        ]);

        return redirect()->route('superadmin.plans.index')->with('success', 'Plan created.');
    }

    public function edit(SubscriptionPlan $plan)
    {
        abort_unless(Auth::user()?->isSuperAdmin(), 403);

        return view('superadmin.plans.edit', compact('plan'));
    }

    public function update(UpdateSubscriptionPlanRequest $request, SubscriptionPlan $plan)
    {
        abort_unless(Auth::user()?->isSuperAdmin(), 403);

        $data = $request->validated();

        $data['discount_value'] = $data['discount_type'] === 'none' ? 0 : (float) ($data['discount_value'] ?? 0);
        $data['is_active'] = $request->boolean('enable_plan');
        $data['show_on_landing'] = $request->boolean('show_on_landing');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        $features = [];
        if (! empty($data['features_text'])) {
            $lines = preg_split('/\r\n|\r|\n/', $data['features_text']);
            $features = array_values(array_filter(array_map('trim', $lines)));
        }
        $data['features'] = $features ?: null;
        unset($data['features_text']);

        $plan->update($data);

        return redirect()->route('superadmin.plans.index')->with('success', 'Plan updated.');
    }

    public function destroy(SubscriptionPlan $plan)
    {
        abort_unless(Auth::user()?->isSuperAdmin(), 403);

        if (Tenant::where('subscription_plan', $plan->key)->exists()) {
            return redirect()
                ->route('superadmin.plans.index')
                ->withErrors(['delete' => 'In use by a tenant.']);
        }

        if (Subscription::where('plan_name', $plan->key)->exists()) {
            return redirect()
                ->route('superadmin.plans.index')
                ->withErrors(['delete' => 'In use by subscription history.']);
        }

        $plan->delete();

        return redirect()->route('superadmin.plans.index')->with('success', 'Plan deleted.');
    }
}
