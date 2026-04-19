<?php

namespace App\Jobs;

use App\Mail\ConsultationReminderMail;
use App\Models\Consultation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendConsultationReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $consultationId)
    {
    }

    public function handle(): void
    {
        $consultation = Consultation::find($this->consultationId);
        if (!$consultation || $consultation->reminder_sent || $consultation->status !== 'confirmed') {
            return;
        }

        try {
            Mail::to($consultation->email)->send(new ConsultationReminderMail($consultation));
            $consultation->forceFill(['reminder_sent' => true])->save();
        } catch (\Exception $e) {
            // allow retry by not marking reminder as sent
        }
    }
}
