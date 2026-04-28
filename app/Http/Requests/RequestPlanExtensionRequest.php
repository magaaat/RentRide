<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RequestPlanExtensionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tenant_id' => ['required', 'integer', 'exists:tenants,id'],
            'requested_plan' => [
                'required',
                Rule::exists('subscription_plans', 'key')
                    ->where('show_on_landing', true)
                    ->where('is_active', true),
            ],
            'payment_method' => ['required', Rule::in(['gcash', 'maya', 'bank_transfer', 'cash'])],
            'payment_reference' => ['required', 'string', 'max:120'],
            'payment_notes' => ['nullable', 'string', 'max:1000'],
            'payment_proof' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }
}
