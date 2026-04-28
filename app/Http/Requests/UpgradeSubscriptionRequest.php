<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpgradeSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'plan_key' => [
                'required',
                'string',
                Rule::exists('subscription_plans', 'key')->where('is_active', 1),
            ],
        ];
    }
}
