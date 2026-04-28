<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTenantProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->user()?->id;

        return [
            'company_name' => ['required', 'string', 'max:255'],
            'owner_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'theme' => ['required', 'in:slate,indigo,emerald,fuchsia'],
            'logo' => ['nullable', 'image', 'max:5120'],
            'public_tagline' => ['nullable', 'string', 'max:255'],
            'website_url' => ['nullable', 'url', 'max:512'],
            'public_booking_notes' => ['nullable', 'string', 'max:5000'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $userId],
            'new_password' => ['nullable', 'confirmed', 'min:8'],
        ];
    }
}
