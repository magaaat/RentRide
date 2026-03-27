<?php

namespace App\Mail;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TenantApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Tenant $tenant,
        public string $loginDomain,
        public ?string $temporaryPassword = null
    ) {
    }

    public function build(): self
    {
        $loginUrl = config('app.url') . '/login?tenant=' . $this->tenant->id;

        return $this->subject('Your RentRide tenant has been approved')
            ->view('emails.tenant-approved')
            ->with([
                'tenant' => $this->tenant,
                'loginDomain' => $this->loginDomain,
                'loginUrl' => $loginUrl,
                'temporaryPassword' => $this->temporaryPassword,
            ]);
    }
}

