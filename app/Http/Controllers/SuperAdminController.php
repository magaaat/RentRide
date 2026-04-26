<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSuperAdminTenantRequest;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\User;
use App\Mail\TenantApprovedMail;
use App\Mail\TenantDomainUpdatedMail;
use App\Models\PlanExtensionRequest;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Support\TenantDatabaseName;
use Stancl\Tenancy\Database\Models\Tenant as TenancyTenant;
use Stancl\Tenancy\Database\Models\Domain as TenancyDomain;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Carbon;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        abort_unless(Auth::user()?->isSuperAdmin(), 403);

        $totalTenants = Tenant::count();
        $activeSubscriptions = Subscription::where('status', 'active')->count();
        $platformRevenue = Tenant::query()
            ->where('status', 'approved')
            ->join('subscription_plans', 'subscription_plans.key', '=', 'tenants.subscription_plan')
            ->sum('subscription_plans.base_price');

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
            ->orderBy('sort_order')
            ->orderBy('id')
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
            'subscription_plan' => ['required', Rule::exists('subscription_plans', 'key')],
            'domain' => 'nullable|string|max:255|unique:tenants,domain',
        ]);

        $data['domain'] = $this->normalizeDomainInput($data['domain'] ?? null);

        $planCheck = SubscriptionPlan::where('key', $data['subscription_plan'])->first();
        if (! $planCheck || ! $planCheck->is_active) {
            return back()
                ->withErrors(['subscription_plan' => 'Selected plan is not available.'])
                ->withInput();
        }

        $temporaryPassword = $this->generateTemporaryPassword();

        $tenant = Tenant::create([
            'company_name' => $data['company_name'],
            'slug' => $this->uniqueTenantSlug($data['company_name']),
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
            $tenant->domain = $this->defaultTenantDomain($tenant);
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

        $pendingExtension = PlanExtensionRequest::where('tenant_id', $tenant->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        $subscriptionPlans = SubscriptionPlan::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $planDisplayName = SubscriptionPlan::where('key', $tenant->subscription_plan)->value('name');
        $tenantDatabaseName = TenantDatabaseName::fromTenancyData(
            TenancyTenant::query()->find((string) $tenant->id)
        );

        return view('superadmin.tenants.show', compact(
            'tenant',
            'pendingExtension',
            'subscriptionPlans',
            'planDisplayName',
            'tenantDatabaseName'
        ));
    }

    public function editTenant(Tenant $tenant)
    {
        abort_unless(Auth::user()?->isSuperAdmin(), 403);

        return view('superadmin.tenants.edit', compact('tenant'));
    }

    public function updateTenant(UpdateSuperAdminTenantRequest $request, Tenant $tenant)
    {
        $wasPending = $tenant->status === 'pending';
        $previousDomain = $tenant->domain;

        $data = $request->validated();

        $manuallyDisabled = $request->boolean('manual_domain_disabled');
        $expiryValue = $data['subscription_expiry'] ?? $tenant->subscription_expiry;
        $expiredByDate = $expiryValue ? now()->greaterThan(Carbon::parse($expiryValue)) : false;
        $data['is_domain_active'] = ! $manuallyDisabled && ! $expiredByDate;
        $planRow = SubscriptionPlan::where('key', $data['subscription_plan'])->first();
        $data['is_featured'] = ($planRow && ($planRow->feature_tier ?? $planRow->tier) === 'premium' && $request->boolean('is_featured'));
        $data['domain'] = $this->normalizeDomainInput($data['domain'] ?? null);

        $tenant->update($data);
        $tenant->slug = $this->uniqueTenantSlug($tenant->company_name, $tenant->id);
        $tenant->save();
        $domainChanged = $previousDomain !== $tenant->domain;

        // Ensure approved tenants always have an expiry date
        if ($tenant->status === 'approved' && ! $tenant->subscription_expiry) {
            $tenant->subscription_expiry = now()->addMonth();
            $tenant->save();
        }

        if ($wasPending && $tenant->status === 'approved') {
            $tenant->subscription_expiry = now()->addMonth();
            $tenant->is_domain_active = true;

            if (! $tenant->domain) {
                $tenant->domain = $this->defaultTenantDomain($tenant);
            }

            $tenant->save();

            $this->provisionTenantInfrastructure($tenant);
        }

        if ($tenant->status === 'approved' && ($domainChanged || ($wasPending && $tenant->status === 'approved'))) {
            $this->syncTenantDomainRouting($tenant, $previousDomain);
        }

        if (
            $domainChanged
            && ! $wasPending
            && $tenant->status === 'approved'
            && ! empty($tenant->domain)
        ) {
            Mail::to($tenant->email)->send(new TenantDomainUpdatedMail($tenant, $previousDomain));
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
            $dbName = TenantDatabaseName::forTenantId((int) $tenant->id);
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

        $dbName = TenantDatabaseName::forTenantId((int) $tenant->id);

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

        $this->applyTenantMirrorEncryptionSchema($dbName);

        TenantDatabaseName::setOnTenancyTenant($tenancyTenant, $dbName);
        $tenancyTenant->save();

        Mail::to($tenant->email)->send(new TenantApprovedMail($tenant, $domain, $temporaryPassword));
    }

    protected function applyTenantMirrorEncryptionSchema(string $dbName): void
    {
        $alterStatements = [
            "ALTER TABLE `$dbName`.`customers` MODIFY `name` TEXT NOT NULL",
            "ALTER TABLE `$dbName`.`customers` MODIFY `email` TEXT NULL",
            "ALTER TABLE `$dbName`.`customers` MODIFY `phone` TEXT NULL",
            "ALTER TABLE `$dbName`.`customers` MODIFY `address` TEXT NULL",
            "ALTER TABLE `$dbName`.`users` MODIFY `name` TEXT NOT NULL",
            "ALTER TABLE `$dbName`.`users` MODIFY `phone` TEXT NULL",
            "ALTER TABLE `$dbName`.`users` MODIFY `address` TEXT NULL",
            "ALTER TABLE `$dbName`.`users` MODIFY `driver_license_front_path` TEXT NULL",
            "ALTER TABLE `$dbName`.`users` MODIFY `driver_license_back_path` TEXT NULL",
        ];

        foreach ($alterStatements as $sql) {
            try {
                DB::statement($sql);
            } catch (\Throwable $e) {
                // Best effort only.
            }
        }
    }

    protected function syncTenantDomainRouting(Tenant $tenant, ?string $previousDomain = null): void
    {
        $tenancyTenant = TenancyTenant::firstOrCreate(
            ['id' => (string) $tenant->id],
            ['data' => ['company_name' => $tenant->company_name]]
        );

        $existingDb = TenantDatabaseName::fromTenancyData($tenancyTenant);
        TenantDatabaseName::setOnTenancyTenant(
            $tenancyTenant,
            $existingDb ?? TenantDatabaseName::generate((int) $tenant->id)
        );
        $tenancyTenant->save();

        if (! $tenant->domain) {
            TenancyDomain::where('tenant_id', $tenancyTenant->id)->delete();
            return;
        }

        if ($previousDomain) {
            TenancyDomain::where('tenant_id', $tenancyTenant->id)
                ->where('domain', $previousDomain)
                ->delete();
        }

        TenancyDomain::where('tenant_id', $tenancyTenant->id)
            ->where('domain', '!=', $tenant->domain)
            ->delete();

        if ($tenant->domain) {
            TenancyDomain::firstOrCreate([
                'tenant_id' => $tenancyTenant->id,
                'domain' => $tenant->domain,
            ]);
        }
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

    protected function defaultTenantDomain(Tenant $tenant): string
    {
        $baseDomain = env('TENANT_BASE_DOMAIN', 'localhost');
        $baseDomain = ltrim(strtolower(trim((string) $baseDomain)), '.');

        $slug = Str::slug((string) $tenant->company_name);
        if ($slug === '') {
            $slug = 'tenant' . $tenant->id;
        }

        $candidate = $slug . '.' . $baseDomain;

        $collision = Tenant::where('domain', $candidate)
            ->where('id', '!=', $tenant->id)
            ->exists();

        if ($collision) {
            $candidate = $slug . '-' . $tenant->id . '.' . $baseDomain;
        }

        return $candidate;
    }

    protected function normalizeDomainInput(?string $domain): ?string
    {
        $domain = trim((string) $domain);
        if ($domain === '') {
            return null;
        }

        $candidate = preg_match('#^https?://#i', $domain) ? $domain : 'http://' . $domain;
        $host = parse_url($candidate, PHP_URL_HOST);

        if (is_string($host) && $host !== '') {
            return strtolower($host);
        }

        return strtolower(trim(strtok($domain, '/')));
    }

    protected function uniqueTenantSlug(string $companyName, ?int $ignoreTenantId = null): string
    {
        $base = Str::slug($companyName);
        if ($base === '') {
            $base = 'tenant';
        }

        $slug = $base;
        $i = 2;
        while (
            Tenant::where('slug', $slug)
                ->when($ignoreTenantId !== null, fn ($q) => $q->where('id', '!=', $ignoreTenantId))
                ->exists()
        ) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }
}

