<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStaffRolePermissionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $permissionKeys = implode(',', array_keys(User::permissionLabelsForTenant($this->user()?->tenant)));

        return [
            'role' => ['required', 'string', 'max:64'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'in:' . $permissionKeys],
        ];
    }
}
