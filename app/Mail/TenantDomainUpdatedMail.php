<?php

namespace App\Mail;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TenantDomainUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Tenant $tenant,
        public ?string $oldDomain = null
    ) {
    }

    public function build(): self
    {
        $loginUrl = $this->tenant->tenantLoginUrl()
            ?? route('login', ['tenant' => $this->tenant->slug ?: $this->tenant->id], absolute: true);

        return $this->subject('Your RentRide login domain was updated')
            ->view('emails.tenant-domain-updated')
            ->with([
                'tenant' => $this->tenant,
                'oldDomain' => $this->oldDomain,
                'newDomain' => $this->tenant->domain,
                'loginUrl' => $loginUrl,
            ]);
    }
}

