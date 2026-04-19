<?php

namespace App\Mail;

use App\Models\Consultation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConsultationAdminNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Consultation $consultation) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Konsultasi Baru Menunggu Konfirmasi - ' . $this->consultation->consultation_code);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.consultation-admin-notification');
    }
}
