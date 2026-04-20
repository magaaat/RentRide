<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\User;
use App\Models\PlanExtensionRequest;
use App\Models\PasswordResetCode;
use App\Mail\PasswordResetCodeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function showForgotPassword(Request $request)
    {
        if ($request->filled('from') || $request->filled('tenant')) {
            $prev = session('password_reset_return', []);
            session([
                'password_reset_return' => [
                    'from' => (string) $request->query('from', $prev['from'] ?? 'home'),
                    'tenant' => $request->query('tenant', $prev['tenant'] ?? null),
                ],
            ]);
        }

        return view('auth.forgot-password', [
            'returnUrl' => $this->passwordResetReturnUrl(),
        ]);
    }

    public function sendResetCode(Request $request)
    {
        $this->syncPasswordResetReturnFromRequest($request);

        $data = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'from' => ['nullable', 'string', 'max:32'],
            'tenant' => ['nullable'],
        ]);

        $user = User::where('email', $data['email'])->firstOrFail();

        // Always derive “back to login” from the account (fixes tenant admins when query/session was missing).
        if ($user->isSuperAdmin()) {
            $request->session()->put('password_reset_return', ['from' => 'superadmin', 'tenant' => null]);
        } elseif ($user->isCustomer()) {
            $request->session()->put('password_reset_return', ['from' => 'customer', 'tenant' => null]);
        } elseif ($user->tenant_id) {
            $tenant = Tenant::find($user->tenant_id);
            // Any rental-company user tied to a tenant (admin / future roles)
            $request->session()->put('password_reset_return', [
                'from' => 'tenant',
                'tenant' => $tenant?->slug ?: (string) $user->tenant_id,
            ]);
        }

        $code = (string) random_int(100000, 999999);

        PasswordResetCode::where('email', $data['email'])->delete();
        PasswordResetCode::create([
            'email' => $data['email'],
            'code' => Hash::make($code),
            'expires_at' => now()->addMinutes(15),
            'created_at' => now(),
        ]);

        Mail::to($data['email'])->send(new PasswordResetCodeMail($code));

        $request->session()->put([
            'password_reset_email' => $data['email'],
            'password_reset_verified' => false,
        ]);

        return redirect()
            ->to('/reset-password/verify')
            ->with('status', 'Check your email for the code.');
    }

    protected function syncPasswordResetReturnFromRequest(Request $request): void
    {
        if (! $request->filled('from') && ! $request->filled('tenant')) {
            return;
        }

        $prev = session('password_reset_return', []);
        $request->session()->put('password_reset_return', [
            'from' => $request->input('from', $prev['from'] ?? 'home'),
            'tenant' => $request->input('tenant', $prev['tenant'] ?? null),
        ]);
    }

    public function showVerifyResetCode()
    {
        if (! session('password_reset_email')) {
            return redirect()
                ->to('/forgot-password')
                ->withErrors(['email' => 'Start by entering your email on the forgot password page.']);
        }

        return view('auth.verify-reset-code', [
            'email' => session('password_reset_email'),
            'returnUrl' => $this->passwordResetReturnUrl(),
        ]);
    }

    public function verifyResetCode(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $email = session('password_reset_email');
        if (! $email) {
            return redirect()
                ->to('/forgot-password')
                ->withErrors(['email' => 'Your session expired. Please request a new code.']);
        }

        $reset = PasswordResetCode::where('email', $email)
            ->where('expires_at', '>', now())
            ->orderByDesc('created_at')
            ->first();

        if (! $reset || ! Hash::check($data['code'], $reset->code)) {
            return back()->withErrors(['code' => 'Invalid or expired reset code.']);
        }

        $request->session()->put('password_reset_verified', true);

        return redirect()
            ->to('/reset-password')
            ->with('status', 'Enter your new password.');
    }

    public function showResetPassword(Request $request)
    {
        if (! session('password_reset_verified') || ! session('password_reset_email')) {
            if (session('password_reset_email')) {
                return redirect()->to('/reset-password/verify');
            }

            return redirect()
                ->to('/forgot-password')
                ->withErrors(['email' => 'Please complete the email and code steps first.']);
        }

        return view('auth.reset-password', [
            'email' => session('password_reset_email'),
            'returnUrl' => $this->passwordResetReturnUrl(),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $data = $request->validate([
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $email = session('password_reset_email');
        if (! $email || ! session('password_reset_verified')) {
            return redirect()
                ->to('/forgot-password')
                ->withErrors(['email' => 'Your session expired. Please start the reset process again.']);
        }

        $user = User::where('email', $email)->firstOrFail();

        // User model casts password as "hashed" — assign plain text (do not Hash::make here).
        $user->password = $data['password'];
        $user->save();

        PasswordResetCode::where('email', $email)->delete();

        $request->session()->forget([
            'password_reset_email',
            'password_reset_verified',
            'password_reset_return',
        ]);

        if ($user->isCustomer()) {
            return redirect()->route('customer.login')->with('success', 'Password reset successful. You can now sign in.');
        }

        if ($user->isSuperAdmin()) {
            return redirect()->route('superadmin.login')->with('success', 'Password reset successful. You can now sign in.');
        }

        $tenant = $user->tenant_id ? Tenant::find($user->tenant_id) : null;
        return redirect()->route('login', ['tenant' => $tenant?->slug ?: $user->tenant_id])->with('success', 'Password reset successful. You can now sign in.');
    }

    /**
     * Where "Back to login" should go for the password-reset wizard.
     * Always recomputed from session + current host (no cached URL — avoids stale “/”).
     */
    protected function passwordResetReturnUrl(): string
    {
        return $this->computePasswordResetLoginUrl(request(), session('password_reset_return', []));
    }

    /**
     * Tenant subdomain / custom domain: same-host /login.
     * Central app: Super Admin, Customer, or /login?tenant=… from session/query.
     */
    protected function computePasswordResetLoginUrl(Request $request, array $return = []): string
    {
        if (function_exists('tenant') && tenant()) {
            return url('/login');
        }

        $host = $request->getHost();
        $centralDomains = config('tenancy.central_domains', []);
        if ($centralDomains !== [] && ! in_array($host, $centralDomains, true)) {
            return url('/login');
        }

        $from = $return['from'] ?? 'home';
        $tenantKey = isset($return['tenant']) && $return['tenant'] !== null && $return['tenant'] !== ''
            ? (string) $return['tenant']
            : null;

        return match ($from) {
            'customer' => route('customer.login'),
            'superadmin' => route('superadmin.login'),
            'tenant' => $tenantKey ? $this->centralTenantLoginUrl($tenantKey) : url('/'),
            default => url('/'),
        };
    }

    /** Central app: tenant-branded login uses query ?tenant= */
    protected function centralTenantLoginUrl(string $tenantKey): string
    {
        return url('/login?'.http_build_query(['tenant' => $tenantKey]));
    }

    protected function resolveTenantFromQuery(?string $tenantParam): ?Tenant
    {
        $tenantParam = $tenantParam !== null ? trim($tenantParam) : null;
        if ($tenantParam === null || $tenantParam === '') {
            return null;
        }

        // Backward compatible: tenant=4
        if (ctype_digit($tenantParam)) {
            return Tenant::find((int) $tenantParam);
        }

        // New: tenant=acme-rentals
        $slug = Str::slug($tenantParam);
        if ($slug === '') {
            return null;
        }

        return Tenant::where('slug', $slug)->first();
    }

    public function showLogin(Request $request)
    {
        // If a tenant domain is opened directly (e.g. company.localhost:8000/login),
        // resolve tenant by host and show tenant login instead of central superadmin login.
        $host = $request->getHost();
        $centralDomains = config('tenancy.central_domains', []);
        if (! in_array($host, $centralDomains, true)) {
            $tenantByHost = Tenant::where('domain', $host)->first();
            if ($tenantByHost) {
                return $this->renderTenantLoginPage($tenantByHost);
            }
        }

        // If this request includes a tenant query (from the email link),
        // show the tenant-specific login page.
        if ($request->query('tenant')) {
            $tenant = $this->resolveTenantFromQuery((string) $request->query('tenant'));

            if (! $tenant) {
                return view('auth.tenant-login')->withErrors([
                    'email' => 'Tenant not found.',
                ]);
            }

            return $this->renderTenantLoginPage($tenant);
        }

        // Central /login is reserved for tenant-link login. Super admin login lives on its own page.
        return redirect()->route('superadmin.login');
    }

    public function showTenantLogin()
    {
        // Per-domain tenant app: resolve central Tenant from current tenancy context
        $tenant = null;
        if (function_exists('tenant') && tenant()) {
            $tid = tenant('id');
            $tenant = Tenant::find($tid);
        }

        return view('auth.tenant-login', compact('tenant'));
    }

    public function showSuperAdminLogin()
    {
        return view('auth.superadmin-login');
    }

    public function showCustomerLogin()
    {
        return view('auth.customer-login');
    }

    public function customerLogin(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $credentials = [
            'email' => $validated['email'],
            'password' => $validated['password'],
        ];

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Invalid credentials.',
            ]);
        }

        $request->session()->regenerate();

        $user = Auth::user();

        if (! $user->isCustomer()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'This page is for customers only. Rental companies use their company login link; platform staff use Super Admin login.',
            ]);
        }

        return redirect()->intended(route('customer.dashboard'));
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'login_tenant_id' => ['nullable', 'integer', 'exists:tenants,id'],
        ]);

        $credentials = [
            'email' => $validated['email'],
            'password' => $validated['password'],
        ];

        $expectedTenantId = isset($validated['login_tenant_id']) && $validated['login_tenant_id'] !== null
            ? (int) $validated['login_tenant_id']
            : null;

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Super Admin must use /superadmin/login (no tenant binding on the form)
            if ($user->isSuperAdmin()) {
                if ($expectedTenantId !== null) {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return back()->withErrors([
                        'email' => 'Please use the Super Admin login page to sign in.',
                    ]);
                }
            }

            // Tenant users (admin + staff) must sign in from their company login URL.
            if ($user->isTenantUser()) {
                if ($expectedTenantId === null) {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return back()->withErrors([
                        'email' => 'Open your company login link (from your approval email) and sign in from that page only.',
                    ]);
                }

                if ((int) $user->tenant_id !== $expectedTenantId) {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return back()->withErrors([
                        'email' => 'This account belongs to a different company. Use the correct login link for your business.',
                    ]);
                }
            }

            // Customers on a tenant-branded login: if tenant is specified, scope to that tenant when applicable
            if ($user->isCustomer() && $expectedTenantId !== null && $user->tenant_id !== null) {
                if ((int) $user->tenant_id !== $expectedTenantId) {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return back()->withErrors([
                        'email' => 'This customer account is not registered for this company.',
                    ]);
                }
            }

            if ($user->isTenantUser() && $user->tenant) {
                // Prevent tenant admins from logging in until approved
                if ($user->tenant->status !== 'approved') {
                    Auth::logout();
                    return back()->withErrors([
                        'email' => 'Your account is pending approval by the Super Admin.',
                    ]);
                }

                // If subscription expired, disable domain and block access
                if ($user->tenant->subscription_expiry && now()->greaterThan($user->tenant->subscription_expiry)) {
                    if ($user->tenant->is_domain_active) {
                        $user->tenant->update(['is_domain_active' => false]);
                    }
                    Auth::logout();
                    return back()->withErrors([
                        'email' => 'Your subscription has expired. Please renew your plan to restore access.',
                    ]);
                }

                // Prevent login if tenant domain is disabled
                if (! $user->tenant->is_domain_active) {
                    Auth::logout();
                    return back()->withErrors([
                        'email' => 'Your company domain is currently disabled. Please renew your subscription or contact the Super Admin for assistance.',
                    ]);
                }

                if ($user->isStaff() && ! $user->is_active) {
                    Auth::logout();
                    return back()->withErrors([
                        'email' => 'Your staff account is disabled. Please contact your tenant admin.',
                    ]);
                }
            }

            if ($user->isSuperAdmin()) {
                return redirect()->route('superadmin.dashboard');
            }

            if ($user->isTenantUser()) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('customer.dashboard');
        }

        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ]);
    }

    public function showRegisterTenant()
    {
        return view('auth.tenant-register');
    }

    public function registerTenant(Request $request)
    {
        $data = $request->validate([
            'company_name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenants,email',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'password' => 'required|confirmed|min:8',
            'plan' => ['required', Rule::exists('subscription_plans', 'key')->where('is_active', true)],
        ]);

        $tenant = Tenant::create([
            'company_name' => $data['company_name'],
            'slug' => $this->uniqueTenantSlug($data['company_name']),
            'domain' => $this->defaultTenantDomainFromCompany($data['company_name']),
            'owner_name' => $data['owner_name'],
            'status' => 'pending',
            'email' => $data['email'],
            'phone' => $data['phone'],
            'address' => $data['address'] ?? null,
            'subscription_plan' => $data['plan'],
            // Expiry starts when the tenant is approved (set by Super Admin).
            'subscription_expiry' => null,
        ]);

        $user = User::create([
            'name' => $data['owner_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'admin',
            'tenant_id' => $tenant->id,
        ]);

        // Do not auto-login; Super Admin must approve first. Tenant will receive login details by email when approved.
        return redirect('/')
            ->with(
                'success',
                'Registration successful. Your application is pending approval. You will receive an email with your login link once the Super Admin approves your company.'
            );
    }

    public function requestPlanExtension(Request $request)
    {
        $data = $request->validate([
            'tenant_id' => ['required', 'integer', 'exists:tenants,id'],
            'requested_plan' => [
                'required',
                Rule::exists('subscription_plans', 'key')
                    ->where('show_on_landing', true)
                    ->where('is_active', true),
            ],
        ]);

        $tenant = Tenant::findOrFail($data['tenant_id']);
        $tenantKey = $tenant->slug ?: (string) $tenant->id;

        // Only allow requests if the tenant is approved but currently disabled/expired.
        $expired = $tenant->subscription_expiry && now()->greaterThan($tenant->subscription_expiry);
        if ($tenant->status !== 'approved' || ($tenant->is_domain_active && ! $expired)) {
            return redirect()->route('login', ['tenant' => $tenantKey])
                ->withErrors(['email' => 'This tenant does not require a plan extension at the moment.']);
        }

        $alreadyPending = PlanExtensionRequest::where('tenant_id', $tenant->id)
            ->where('status', 'pending')
            ->exists();

        if ($alreadyPending) {
            return redirect()->route('login', ['tenant' => $tenantKey])
                ->with('success', 'Your extension request is already pending approval.');
        }

        PlanExtensionRequest::create([
            'tenant_id' => $tenant->id,
            'requested_plan' => $data['requested_plan'],
            'status' => 'pending',
        ]);

        return redirect()->route('login', ['tenant' => $tenantKey])
            ->with('success', 'Your extension request has been sent to the Super Admin for review.');
    }

    public function showRegisterCustomer()
    {
        return view('auth.customer-register');
    }

    public function registerCustomer(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:500',
            'password' => 'required|confirmed|min:8',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'password' => Hash::make($data['password']),
            'role' => 'customer',
            'tenant_id' => null,
        ]);

        Auth::login($user);

        return redirect()->route('customer.dashboard')->with('success', 'Welcome! Your customer account is ready — browse rental companies and book a vehicle.');
    }

    public function logout(Request $request)
    {
        $user = auth()->user();
        $tenantId = $user?->tenant_id;
        $isCustomer = $user?->isCustomer();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($isCustomer) {
            return redirect()->route('customer.login');
        }

        if ($tenantId) {
            $tenant = Tenant::find($tenantId);
            return redirect()->route('login', ['tenant' => $tenant?->slug ?: $tenantId]);
        }

        return redirect()->route('login');
    }

    public function showCustomerProfile()
    {
        $user = Auth::user();

        abort_unless($user?->isCustomer(), 403);

        return view('customer.profile', compact('user'));
    }

    public function updateCustomerProfile(Request $request)
    {
        $user = Auth::user();

        abort_unless($user?->isCustomer(), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
            'password' => ['nullable', 'confirmed', 'min:8'],
            'license_front' => ['nullable', 'image', 'max:5120'],
            'license_back' => ['nullable', 'image', 'max:5120'],
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->phone = $data['phone'] ?? null;
        $user->address = $data['address'] ?? null;

        if ($request->hasFile('license_front')) {
            if ($user->driver_license_front_path) {
                Storage::disk('public')->delete($user->driver_license_front_path);
            }
            $user->driver_license_front_path = $request->file('license_front')->store('licenses', 'public');
        }

        if ($request->hasFile('license_back')) {
            if ($user->driver_license_back_path) {
                Storage::disk('public')->delete($user->driver_license_back_path);
            }
            $user->driver_license_back_path = $request->file('license_back')->store('licenses', 'public');
        }

        if (! empty($data['password'])) {
            $user->password = $data['password'];
        }

        $user->save();

        return redirect()
            ->route('customer.profile')
            ->with('success', 'Profile updated successfully.');
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

    protected function defaultTenantDomainFromCompany(string $companyName): string
    {
        $baseDomain = env('TENANT_BASE_DOMAIN', 'localhost');
        $baseDomain = ltrim(strtolower(trim((string) $baseDomain)), '.');

        $slug = $this->uniqueTenantSlug($companyName);
        $candidate = $slug . '.' . $baseDomain;
        $i = 2;

        while (Tenant::where('domain', $candidate)->exists()) {
            $candidate = $slug . '-' . $i . '.' . $baseDomain;
            $i++;
        }

        return $candidate;
    }

    protected function renderTenantLoginPage(Tenant $tenant)
    {
        // If domain is disabled (or expired), show the domain disabled page (pre-login)
        $expired = $tenant->subscription_expiry && now()->greaterThan($tenant->subscription_expiry);
        if (! $tenant->is_domain_active || $expired) {
            $plans = SubscriptionPlan::query()
                ->where('show_on_landing', true)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();

            $hasPending = PlanExtensionRequest::where('tenant_id', $tenant->id)
                ->where('status', 'pending')
                ->exists();

            return view('auth.tenant-domain-disabled', compact('tenant', 'plans', 'hasPending'));
        }

        return view('auth.tenant-login', compact('tenant'));
    }
}

