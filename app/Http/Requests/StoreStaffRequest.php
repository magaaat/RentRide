<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreStaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $availablePermissionKeys = implode(',', array_keys(User::permissionLabelsForTenant($this->user()?->tenant)));

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'string', 'max:64'],
            'custom_role_name' => ['nullable', 'string', 'max:64', 'regex:/^[a-zA-Z0-9 _-]+$/'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'in:' . $availablePermissionKeys],
            'is_active' => ['nullable', 'boolean'],
            'generate_password' => ['nullable', 'in:0,1'],
            'password' => ['required_unless:generate_password,1', 'nullable', 'string', 'min:8', 'confirmed'],
        ];
    }
}
