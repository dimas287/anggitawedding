<?php

namespace App\Mail;

use App\Models\Booking;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentSuccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking, public Payment $payment) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Pembayaran Berhasil - ' . $this->booking->booking_code);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.payment-success');
    }

    public function attachments(): array
    {
        $booking = $this->booking->loadMissing(
            'user',
            'package',
            'payments',
            'extraCharges',
            'financialTransactions',
            'invitation.template'
        );

        $pdf = Pdf::loadView('pdf.invoice', ['booking' => $booking])->setPaper('a4');

        return [
            Attachment::fromData(fn () => $pdf->output(), 'invoice-' . $booking->booking_code . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
