<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        return view('admin.subscriptions.upgrade');
    }

    public function upgrade(Request $request)
    {
        $tenant = Auth::user()->tenant;

        $data = $request->validate([
            'plan_name' => 'required|in:basic,standard,premium',
        ]);

        $prices = [
            'basic' => 249,
            'standard' => 449,
            'premium' => 699,
        ];

        $subscription = Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_name' => $data['plan_name'],
            'price' => $prices[$data['plan_name']],
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'status' => 'active',
        ]);

        $tenant->update([
            'subscription_plan' => $data['plan_name'],
            'subscription_expiry' => $subscription->end_date,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Subscription updated.');
    }
}

