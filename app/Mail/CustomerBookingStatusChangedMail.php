<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomerBookingStatusChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Booking $booking,
        public string $previousStatus
    ) {
    }

    public function build(): self
    {
        $this->booking->loadMissing(['vehicle', 'tenant', 'customer']);

        return $this->subject('RentRide — booking status: '.ucfirst($this->booking->status))
            ->view('emails.customer-booking-status');
    }
}
