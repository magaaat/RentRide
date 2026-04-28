<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $recaptchaRules = $this->shouldValidateRecaptcha()
            ? ['required', 'string']
            : ['nullable', 'string'];

        return [
            'email' => ['required', 'email'],
            'password' => ['required'],
            'g-recaptcha-response' => $recaptchaRules,
        ];
    }

    protected function shouldValidateRecaptcha(): bool
    {
        return trim((string) config('services.recaptcha.site_key', '')) !== ''
            && trim((string) config('services.recaptcha.secret_key', '')) !== '';
    }
}
