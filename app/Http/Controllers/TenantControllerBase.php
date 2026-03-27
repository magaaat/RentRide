<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

abstract class TenantControllerBase extends Controller
{
    protected function tenantId(): ?int
    {
        return Auth::user()?->tenant_id;
    }

    protected function authorizeTenantAccess($model): void
    {
        if ($model->tenant_id !== $this->tenantId()) {
            abort(403);
        }
    }
}

