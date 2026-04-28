<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubscriptionPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'key' => ['required', 'string', 'max:64', 'regex:/^[a-z0-9_\-]+$/', 'unique:subscription_plans,key'],
            'name' => ['required', 'string', 'max:255'],
            'tier' => ['required', 'string', 'max:64'],
            'feature_tier' => ['required', 'in:basic,standard,premium'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'billing_period' => ['required', 'string', 'max:32'],
            'currency' => ['required', 'string', 'size:3'],
            'discount_type' => ['required', 'in:none,percent,fixed'],
            'discount_value' => ['nullable', 'numeric', 'min:0'],
            'features_text' => ['nullable', 'string', 'max:10000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'enable_plan' => ['nullable', 'boolean'],
            'show_on_landing' => ['nullable', 'boolean'],
        ];
    }
}
