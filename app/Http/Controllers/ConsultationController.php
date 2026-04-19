<?php

namespace App\Http\Controllers;

use App\Mail\ConsultationAdminNotificationMail;
use App\Mail\ConsultationPendingMail;
use App\Models\Consultation;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ConsultationController extends Controller
{
    public function form(Request $request)
    {
        $date = $request->date;
        return view('booking.consultation', compact('date'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'preferred_date' => 'required|date|after_or_equal:today',
            'preferred_time' => 'required',
            'event_date' => 'nullable|date|after:today',
            'consultation_type' => 'required|in:online,offline',
            'message' => 'nullable|string',
        ]);

        $userId = Auth::id();
        if (!$userId) {
            session(['consultation_intent' => $request->all()]);
            return redirect()->route('login')->with('info', 'Silakan login untuk melanjutkan booking konsultasi.');
        }

        $existingConsultation = Consultation::where('user_id', $userId)
            ->whereDate('preferred_date', $request->preferred_date)
            ->where('preferred_time', $request->preferred_time)
            ->where('consultation_type', $request->consultation_type)
            ->where('status', 'pending')
            ->latest('id')
            ->first();

        if ($existingConsultation) {
            return redirect()->route('user.dashboard')
                ->with('info', 'Permintaan konsultasi sebelumnya masih menunggu konfirmasi.');
        }

        $consultation = DB::transaction(function () use ($request, $userId) {
            return Consultation::create([
                'consultation_code' => Consultation::generateCode(),
                'user_id' => $userId,
                'name' => strip_tags($request->name),
                'email' => strip_tags($request->email),
                'phone' => strip_tags($request->phone),
                'preferred_date' => $request->preferred_date,
                'preferred_time' => $request->preferred_time,
                'event_date' => $request->event_date,
                'consultation_type' => $request->consultation_type,
                'message' => strip_tags($request->message),
                'status' => 'pending',
            ]);
        });

        try {
            Mail::to($consultation->email)->send(new ConsultationPendingMail($consultation));
        } catch (\Exception $e) {
            // Silent fail
        }

        $settings = SiteSetting::getJson('consultation_settings', []);
        $rawEmails = $settings['admin_email'] ?? config('mail.from.address');
        $adminRecipients = collect(explode(',', (string) $rawEmails))
            ->map(fn ($email) => trim($email))
            ->filter(fn ($email) => !empty($email));

        if ($adminRecipients->isEmpty() && config('mail.from.address')) {
            $adminRecipients->push(config('mail.from.address'));
        }

        if ($adminRecipients->isNotEmpty()) {
            try {
                Mail::to($adminRecipients->all())->send(new ConsultationAdminNotificationMail($consultation));
            } catch (\Exception $e) {
                // Silent fail
            }
        }

        return redirect()->route('user.dashboard')
            ->with('success', 'Terima kasih! Permintaan konsultasi Anda sudah kami terima, mohon tunggu konfirmasi admin.');
    }
}
