<?php

namespace App\Http\Requests;

use App\Models\Tenant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTenantBySuperAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tenant = $this->route('tenant');
        $tenantId = $tenant instanceof Tenant ? $tenant->id : $tenant;

        return [
            'company_name' => ['required', 'string', 'max:255'],
            'subscription_plan' => ['required', Rule::exists('subscription_plans', 'key')],
            'subscription_expiry' => ['nullable', 'date'],
            'domain' => ['nullable', 'string', 'max:255', 'unique:tenants,domain,' . $tenantId],
            'is_domain_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'status' => ['required', 'in:pending,approved'],
        ];
    }
}
