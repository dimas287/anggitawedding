<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking, protected string $pdfContent)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invoice Booking ' . $this->booking->booking_code
        );
    }

    public function content(): Content
    {
        $extraTotal = $this->booking->active_extra_charges_total;
        return new Content(
            view: 'emails.booking-invoice',
            with: [
                'extraTotal' => $extraTotal,
                'grandTotal' => $this->booking->package_price + $extraTotal,
            ]
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->pdfContent, 'invoice-' . $this->booking->booking_code . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
