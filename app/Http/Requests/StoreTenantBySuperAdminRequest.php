<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTenantBySuperAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:255'],
            'owner_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:tenants,email', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'subscription_plan' => ['required', Rule::exists('subscription_plans', 'key')],
            'domain' => ['nullable', 'string', 'max:255', 'unique:tenants,domain'],
            'payment_method' => ['required', Rule::in(['gcash', 'maya', 'bank_transfer', 'cash'])],
            'payment_reference' => ['required', 'string', 'max:120'],
            'payment_notes' => ['nullable', 'string', 'max:1000'],
            'payment_proof' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }
}
