<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\User;
use App\Mail\TenantApprovedMail;
use App\Models\PlanExtensionRequest;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Stancl\Tenancy\Database\Models\Tenant as TenancyTenant;
use Stancl\Tenancy\Database\Models\Domain as TenancyDomain;
use Illuminate\Support\Facades\DB;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        abort_unless(Auth::user()?->isSuperAdmin(), 403);

        $totalTenants = Tenant::count();
        $activeSubscriptions = Subscription::where('status', 'active')->count();
        $platformRevenue = Subscription::where('status', 'active')->sum('price');

        return view('superadmin.dashboard', compact(
            'totalTenants',
            'activeSubscriptions',
            'platformRevenue'
        ));
    }

    public function tenantsIndex()
    {
        abort_unless(Auth::user()?->isSuperAdmin(), 403);

        // Auto-disable domains for expired subscriptions
        Tenant::whereNotNull('subscription_expiry')
            ->where('subscription_expiry', '<', now())
            ->where('is_domain_active', true)
            ->update(['is_domain_active' => false]);

        $tenants = Tenant::latest()->paginate(20);

        return view('superadmin.tenants.index', compact('tenants'));
    }

    public function createTenant()
    {
        abort_unless(Auth::user()?->isSuperAdmin(), 403);

        $plans = SubscriptionPlan::where('is_active', true)
            ->orderByRaw("FIELD(`key`, 'basic', 'standard', 'premium')")
            ->get();

        return view('superadmin.tenants.create', compact('plans'));
    }

    public function storeTenant(Request $request)
    {
        abort_unless(Auth::user()?->isSuperAdmin(), 403);

        $data = $request->validate([
            'company_name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenants,email|unique:users,email',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'subscription_plan' => 'required|in:basic,standard,premium',
            'domain' => 'nullable|string|max:255',
        ]);

        $temporaryPassword = $this->generateTemporaryPassword();

        $tenant = Tenant::create([
            'company_name' => $data['company_name'],
            'owner_name' => $data['owner_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'address' => $data['address'] ?? null,
            'subscription_plan' => $data['subscription_plan'],
            'status' => 'approved',
            'subscription_expiry' => now()->addMonth(),
            'is_domain_active' => true,
            'domain' => $data['domain'] ?: null,
        ]);

        if (! $tenant->domain) {
            $tenant->domain = 'tenant'.$tenant->id.'.rentride.test';
            $tenant->save();
        }

        User::create([
            'name' => $data['owner_name'],
            'email' => $data['email'],
            'password' => $temporaryPassword,
            'role' => 'admin',
            'tenant_id' => $tenant->id,
        ]);

        $this->provisionTenantInfrastructure($tenant, $temporaryPassword);

        return redirect()
            ->route('superadmin.tenants.show', $tenant)
            ->with('success', 'Tenant created.');
    }

    public function showTenant(Tenant $tenant)
    {
        abort_unless(Auth::user()?->isSuperAdmin(), 403);

        // Ensure approved tenants have an expiry date
        if ($tenant->status === 'approved' && ! $tenant->subscription_expiry) {
            $tenant->update(['subscription_expiry' => now()->addMonth()]);
            $tenant->refresh();
        }

        // Auto-disable if expired
        if ($tenant->subscription_expiry && now()->greaterThan($tenant->subscription_expiry) && $tenant->is_domain_active) {
            $tenant->update(['is_domain_active' => false]);
        }

        // Compute tenant DB usage (best-effort)
        $dbName = 'tenant_' . $tenant->id;
        $bytes = 0;
        try {
            $row = DB::selectOne(
                'SELECT COALESCE(SUM(data_length + index_length), 0) AS bytes
                 FROM information_schema.tables
                 WHERE table_schema = ?',
                [$dbName]
            );
            $bytes = (int) ($row->bytes ?? 0);
        } catch (\Throwable $e) {
            $bytes = 0;
        }

        $dataUsedMb = round($bytes / 1024 / 1024, 2);

        $pendingExtension = PlanExtensionRequest::where('tenant_id', $tenant->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        return view('superadmin.tenants.show', compact('tenant', 'dbName', 'dataUsedMb', 'pendingExtension'));
    }

    public function editTenant(Tenant $tenant)
    {
        abort_unless(Auth::user()?->isSuperAdmin(), 403);

        return view('superadmin.tenants.edit', compact('tenant'));
    }

    public function updateTenant(Request $request, Tenant $tenant)
    {
        $wasPending = $tenant->status === 'pending';

        $data = $request->validate([
            'company_name' => 'required|string|max:255',
            'subscription_plan' => 'required|in:basic,standard,premium',
            'subscription_expiry' => 'nullable|date',
            'domain' => 'nullable|string|max:255',
            'is_domain_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'status' => 'required|in:pending,approved',
        ]);

        $data['is_domain_active'] = $request->boolean('is_domain_active');
        $data['is_featured'] = $request->boolean('is_featured');

        $tenant->update($data);

        // Ensure approved tenants always have an expiry date
        if ($tenant->status === 'approved' && ! $tenant->subscription_expiry) {
            $tenant->subscription_expiry = now()->addMonth();
            $tenant->save();
        }

        if ($wasPending && $tenant->status === 'approved') {
            $tenant->subscription_expiry = now()->addMonth();
            $tenant->is_domain_active = true;

            if (! $tenant->domain) {
                $tenant->domain = 'tenant'.$tenant->id.'.rentride.test';
            }

            $tenant->save();

            $this->provisionTenantInfrastructure($tenant);
        }

        return redirect()->route('superadmin.tenants.index')->with('success', 'Tenant updated.');
    }

    public function destroyTenant(Tenant $tenant)
    {
        abort_unless(Auth::user()?->isSuperAdmin(), 403);

        $tid = (string) $tenant->id;

        // Remove Stancl routing rows — deleting App\Tenant does not remove `domains` (no FK cascade).
        TenancyDomain::where('tenant_id', $tid)->delete();

        // Optional: drop tenant MySQL database created on approval
        try {
            $dbName = 'tenant_' . $tenant->id;
            DB::statement("DROP DATABASE IF EXISTS `$dbName`");
        } catch (\Throwable $e) {
            // SQLite / permission / missing DB — ignore
        }

        $tenant->delete();

        return redirect()->route('superadmin.tenants.index')->with('success', 'Tenant deleted.');
    }

    public function profile()
    {
        abort_unless(Auth::user()?->isSuperAdmin(), 403);

        $user = Auth::user();
        return view('superadmin.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        abort_unless(Auth::user()?->isSuperAdmin(), 403);

        $user = Auth::user();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|confirmed|min:8',
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];
        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();

        return redirect()->route('superadmin.profile')->with('success', 'Profile updated.');
    }

    /**
     * Stancl domain + tenant DB + approval email (same as first-time approval in updateTenant).
     */
    protected function provisionTenantInfrastructure(Tenant $tenant, ?string $temporaryPassword = null): void
    {
        $domain = $tenant->domain;
        if (! $domain) {
            return;
        }

        $tenancyTenant = TenancyTenant::firstOrCreate(
            ['id' => (string) $tenant->id],
            ['data' => ['company_name' => $tenant->company_name]]
        );

        TenancyDomain::firstOrCreate([
            'tenant_id' => $tenancyTenant->id,
            'domain' => $domain,
        ]);

        $dbName = 'tenant_'.$tenant->id;

        try {
            DB::statement("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        } catch (\Throwable $e) {
            // SQLite / unsupported — skip
        }

        $centralDb = env('DB_DATABASE', 'rentride');
        $tablesToClone = ['users', 'vehicles', 'customers', 'bookings', 'payments'];

        foreach ($tablesToClone as $table) {
            try {
                DB::statement("CREATE TABLE IF NOT EXISTS `$dbName`.`$table` LIKE `$centralDb`.`$table`");
            } catch (\Throwable $e) {
                //
            }
        }

        $tenancyTenant->data = array_merge($tenancyTenant->data ?? [], [
            'database' => $dbName,
        ]);
        $tenancyTenant->save();

        Mail::to($tenant->email)->send(new TenantApprovedMail($tenant, $domain, $temporaryPassword));
    }

    protected function generateTemporaryPassword(): string
    {
        // Avoid ambiguous characters in emailed credentials.
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%';
        $password = '';
        $max = strlen($alphabet) - 1;

        for ($i = 0; $i < 12; $i++) {
            $password .= $alphabet[random_int(0, $max)];
        }

        return $password;
    }
}

