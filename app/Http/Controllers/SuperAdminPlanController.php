<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use Illuminate\Http\Request;
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

    public function store(Request $request)
    {
        abort_unless(Auth::user()?->isSuperAdmin(), 403);

        $data = $request->validate([
            'key' => ['required', 'string', 'max:64', 'regex:/^[a-z0-9_\-]+$/', 'unique:subscription_plans,key'],
            'name' => ['required', 'string', 'max:255'],
            'tier' => ['required', 'string', 'max:64'],
            'feature_tier' => ['required', 'in:basic,standard,premium'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'billing_period' => ['required', 'string', 'max:32'],
            'currency' => ['required', 'string', 'size:3'],
            'discount_type' => ['required', 'in:none,percent,fixed'],
            'discount_value' => ['nullable', 'numeric', 'min:0'],
            'features_text' => ['nullable', 'string', 'max:10000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'enable_plan' => ['nullable', 'boolean'],
            'show_on_landing' => ['nullable', 'boolean'],
        ]);

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

    public function update(Request $request, SubscriptionPlan $plan)
    {
        abort_unless(Auth::user()?->isSuperAdmin(), 403);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'tier' => 'required|string|max:64',
            'feature_tier' => 'required|in:basic,standard,premium',
            'base_price' => 'required|numeric|min:0',
            'discount_type' => 'required|in:none,percent,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'sort_order' => 'nullable|integer|min:0|max:65535',
            'enable_plan' => 'nullable|boolean',
            'show_on_landing' => 'nullable|boolean',
            'features_text' => 'nullable|string|max:10000',
        ]);

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
