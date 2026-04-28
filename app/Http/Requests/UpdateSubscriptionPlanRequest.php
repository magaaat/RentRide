<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubscriptionPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'tier' => ['required', 'string', 'max:64'],
            'feature_tier' => ['required', 'in:basic,standard,premium'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'discount_type' => ['required', 'in:none,percent,fixed'],
            'discount_value' => ['nullable', 'numeric', 'min:0'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'enable_plan' => ['nullable', 'boolean'],
            'show_on_landing' => ['nullable', 'boolean'],
            'features_text' => ['nullable', 'string', 'max:10000'],
        ];
    }
}
