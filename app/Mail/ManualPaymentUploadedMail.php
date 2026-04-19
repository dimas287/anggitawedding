<?php

namespace App\Mail;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ManualPaymentUploadedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Booking $booking;
    public Payment $payment;

    public function __construct(Booking $booking, Payment $payment)
    {
        $this->booking = $booking;
        $this->payment = $payment;
    }

    public function build()
    {
        return $this->subject('[Manual Payment] ' . $this->booking->booking_code)
            ->view('emails.manual-payment-uploaded');
    }
}
