<?php

namespace App\Http\Requests;

use App\Models\Tenant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSuperAdminTenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->isSuperAdmin();
    }

    public function rules(): array
    {
        /** @var Tenant|null $tenant */
        $tenant = $this->route('tenant');
        $tenantId = $tenant?->id;

        return [
            'company_name' => ['required', 'string', 'max:255'],
            'subscription_plan' => ['required', Rule::exists('subscription_plans', 'key')],
            'subscription_expiry' => ['nullable', 'date'],
            'domain' => ['nullable', 'string', 'max:255', Rule::unique('tenants', 'domain')->ignore($tenantId)],
            'manual_domain_disabled' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'status' => ['required', Rule::in(['pending', 'approved'])],
        ];
    }
}
