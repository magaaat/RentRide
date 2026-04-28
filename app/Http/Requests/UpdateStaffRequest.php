<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $staff = $this->route('staff');
        $staffId = $staff instanceof User ? $staff->id : $staff;
        $availablePermissionKeys = implode(',', array_keys(User::permissionLabelsForTenant($this->user()?->tenant)));

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $staffId],
            'role' => ['required', 'string', 'max:64'],
            'custom_role_name' => ['nullable', 'string', 'max:64', 'regex:/^[a-zA-Z0-9 _-]+$/'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'in:' . $availablePermissionKeys],
            'is_active' => ['nullable', 'boolean'],
            'password' => ['nullable', 'confirmed', 'min:8'],
        ];
    }
}
