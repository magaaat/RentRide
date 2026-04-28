<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTenantProfileRequest extends FormRequest
{
    private const NAV_SEQUENCE_KEYS = [
        'dashboard',
        'vehicles',
        'maintenance',
        'customers',
        'reports',
        'analytics',
        'bookings',
        'calendar',
        'payments',
        'staff',
        'updated_module',
        'about',
    ];

    public function authorize(): bool
    {
        return (bool) $this->user()?->isTenantUser();
    }

    public function rules(): array
    {
        $userId = (int) $this->user()?->id;

        return [
            'company_name' => ['required', 'string', 'max:255'],
            'owner_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'theme' => ['required', Rule::in(['slate', 'indigo', 'emerald', 'fuchsia'])],
            'logo' => ['nullable', 'image', 'max:5120'],
            'public_tagline' => ['nullable', 'string', 'max:255'],
            'website_url' => ['nullable', 'url', 'max:512'],
            'public_booking_notes' => ['nullable', 'string', 'max:5000'],
            'navbar_sequence' => ['nullable', 'array'],
            'navbar_sequence.*' => ['nullable', 'string', Rule::in(self::NAV_SEQUENCE_KEYS)],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'new_password' => ['nullable', 'confirmed', 'min:8'],
        ];
    }
}
