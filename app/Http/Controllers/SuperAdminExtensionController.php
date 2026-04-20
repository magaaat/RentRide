<?php

namespace App\Http\Controllers;

use App\Mail\PlanExtensionApprovedMail;
use App\Models\PlanExtensionRequest;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class SuperAdminExtensionController extends Controller
{
    public function index()
    {
        abort_unless(Auth::user()?->isSuperAdmin(), 403);

        $pending = PlanExtensionRequest::with(['tenant', 'plan'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        $recent = PlanExtensionRequest::with(['tenant', 'plan'])
            ->whereIn('status', ['approved', 'rejected'])
            ->latest()
            ->limit(20)
            ->get();

        return view('superadmin.extensions.index', compact('pending', 'recent'));
    }

    public function approve(PlanExtensionRequest $extension)
    {
        abort_unless(Auth::user()?->isSuperAdmin(), 403);

        if ($extension->status !== 'pending') {
            return back()->withErrors(['email' => 'This extension request is no longer pending.']);
        }

        $tenant = $extension->tenant;
        $plan = SubscriptionPlan::query()
            ->where('key', $extension->requested_plan)
            ->where('show_on_landing', true)
            ->where('is_active', true)
            ->first();

        if (! $plan) {
            return back()->withErrors([
                'email' => 'Requested plan is unavailable.',
            ]);
        }

        $tenant->subscription_plan = $plan->key;
        $tenant->subscription_expiry = now()->addMonth();
        $tenant->is_domain_active = true;
        $tenant->save();

        $extension->status = 'approved';
        $extension->reviewed_by = Auth::id();
        $extension->reviewed_at = now();
        $extension->save();

        if (! empty($tenant->email)) {
            Mail::to($tenant->email)->send(new PlanExtensionApprovedMail($extension->fresh(['tenant'])));
        }

        return redirect()->route('superadmin.extensions.index')->with('success', 'Extension approved. Tenant domain has been re-enabled.');
    }

    public function reject(Request $request, PlanExtensionRequest $extension)
    {
        abort_unless(Auth::user()?->isSuperAdmin(), 403);

        if ($extension->status !== 'pending') {
            return back()->withErrors(['email' => 'This extension request is no longer pending.']);
        }

        $data = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $extension->status = 'rejected';
        $extension->notes = $data['notes'] ?? null;
        $extension->reviewed_by = Auth::id();
        $extension->reviewed_at = now();
        $extension->save();

        return redirect()->route('superadmin.extensions.index')->with('success', 'Extension request rejected.');
    }
}

