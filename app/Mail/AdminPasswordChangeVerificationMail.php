<?php

namespace App\Mail;

use App\Models\PasswordChangeRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class AdminPasswordChangeVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $verificationUrl;

    public function __construct(public User $user, public PasswordChangeRequest $request)
    {
        $this->verificationUrl = URL::temporarySignedRoute(
            'admin.password.confirm',
            $request->expires_at,
            ['token' => $request->token, 'email' => $user->email]
        );
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Verifikasi Perubahan Password Admin');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.admin-password-change-verification');
    }
}
