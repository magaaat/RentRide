<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomerBookingPlacedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking)
    {
    }

    public function build(): self
    {
        $this->booking->loadMissing(['vehicle', 'tenant', 'customer']);

        return $this->subject('RentRide — booking request received')
            ->view('emails.customer-booking-placed');
    }
}
