<?php

namespace App\Mail;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomerWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Tenant $tenant,
        public User $customerUser,
        public string $plainPassword,
    ) {}

    public function build(): self
    {
        return $this->subject('Your RentRide customer account – '.$this->tenant->company_name)
            ->view('emails.customer-welcome')
            ->with([
                'tenant' => $this->tenant,
                'customerUser' => $this->customerUser,
                'plainPassword' => $this->plainPassword,
                'loginUrl' => route('customer.login'),
            ]);
    }
}
