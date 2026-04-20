<?php

namespace App\Mail;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StaffWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Tenant $tenant,
        public User $staffUser,
        public string $plainPassword,
    ) {}

    public function build(): self
    {
        $loginUrl = $this->tenant->tenantLoginUrl() ?? url('/login');

        return $this->subject('Your RentRide staff account – '.$this->tenant->company_name)
            ->view('emails.staff-welcome')
            ->with([
                'tenant' => $this->tenant,
                'staffUser' => $this->staffUser,
                'plainPassword' => $this->plainPassword,
                'loginUrl' => $loginUrl,
                'loginDomain' => $this->tenant->domain,
            ]);
    }
}
