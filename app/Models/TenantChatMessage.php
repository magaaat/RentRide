<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantChatMessage extends Model
{
    public const SENDER_TENANT = 'tenant';
    public const SENDER_SUPER_ADMIN = 'super_admin';

    protected $fillable = [
        'tenant_id',
        'user_id',
        'sender_role',
        'message',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
