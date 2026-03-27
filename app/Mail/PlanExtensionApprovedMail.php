<?php

namespace App\Mail;

use App\Models\PlanExtensionRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PlanExtensionApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public PlanExtensionRequest $request)
    {
    }

    public function build(): self
    {
        $tenant = $this->request->tenant;
        $loginUrl = config('app.url') . '/login?tenant=' . $tenant->id;

        return $this->subject('Your RentRide plan extension was approved')
            ->view('emails.plan-extension-approved')
            ->with([
                'tenant' => $tenant,
                'request' => $this->request,
                'loginUrl' => $loginUrl,
            ]);
    }
}

