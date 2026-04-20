<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SubscriptionController extends TenantControllerBase
{
    public function index()
    {
        $subscriptions = Subscription::where('tenant_id', $this->tenantId())
            ->latest()
            ->get();

        return view('admin.subscriptions.index', compact('subscriptions'));
    }

    public function upgradeForm()
    {
        $plans = SubscriptionPlan::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('admin.subscriptions.upgrade', compact('plans'));
    }

    public function upgrade(Request $request)
    {
        $tenant = Auth::user()->tenant;

        $data = $request->validate([
            'plan_key' => [
                'required',
                'string',
                Rule::exists('subscription_plans', 'key')->where('is_active', 1),
            ],
        ]);

        $plan = SubscriptionPlan::where('key', $data['plan_key'])->firstOrFail();
        $price = $plan->discountedPrice();

        $subscription = Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_name' => $plan->key,
            'price' => $price,
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'status' => 'active',
        ]);

        $tenant->update([
            'subscription_plan' => $plan->key,
            'subscription_expiry' => $subscription->end_date,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Subscription updated.');
    }
}
