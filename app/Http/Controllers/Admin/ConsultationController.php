<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendConsultationReminderJob;
use App\Mail\ConsultationCancelledMail;
use App\Mail\ConsultationPendingMail;
use App\Mail\ConsultationReminderMail;
use App\Mail\ConsultationRescheduledMail;
use App\Models\Consultation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ConsultationController extends Controller
{
    public function index(Request $request)
    {
        $query = Consultation::with('user', 'booking')->latest();
        if ($request->status) $query->where('status', $request->status);
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('consultation_code', 'like', '%' . $request->search . '%')
                  ->orWhere('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        $consultations = $query->paginate(15);
        $clients = User::where('role', 'client')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'phone']);

        return view('admin.consultations.index', compact('consultations', 'clients'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'name' => 'nullable|string|max:150',
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:50',
            'preferred_date' => 'required|date|after_or_equal:today',
            'preferred_time' => 'required|date_format:H:i',
            'consultation_type' => 'required|in:online,offline',
            'event_date' => 'nullable|date|after_or_equal:today',
            'status' => 'required|in:pending,confirmed',
            'message' => 'nullable|string',
        ]);

        $selectedUser = !empty($data['user_id']) ? User::find($data['user_id']) : null;

        $name = $data['name'] ?: optional($selectedUser)->name;
        $email = $data['email'] ?: optional($selectedUser)->email;
        $phone = $data['phone'] ?: optional($selectedUser)->phone;

        if (!$name || !$email || !$phone) {
            return back()->withErrors([
                'name' => 'Nama, email, dan nomor WhatsApp wajib diisi atau pilih klien yang sudah memiliki data lengkap.',
            ])->withInput();
        }

        $consultation = DB::transaction(function () use ($data, $name, $email, $phone) {
            return Consultation::create([
                'consultation_code' => Consultation::generateCode(),
                'user_id' => $data['user_id'] ?? null,
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'preferred_date' => $data['preferred_date'],
                'preferred_time' => $data['preferred_time'],
                'event_date' => $data['event_date'] ?? null,
                'consultation_type' => $data['consultation_type'],
                'message' => $data['message'] ?? null,
                'status' => $data['status'],
                'reminder_sent' => false,
            ]);
        });

        try {
            if ($consultation->status === 'pending') {
                Mail::to($consultation->email)->send(new ConsultationPendingMail($consultation));
            } else {
                Mail::to($consultation->email)->send(new ConsultationRescheduledMail($consultation));
            }
        } catch (\Exception $e) {
            // ignore mail failures for manual creation
        }

        if ($consultation->status === 'confirmed') {
            $this->scheduleReminder($consultation);
        }

        return redirect()
            ->route('admin.consultations.show', $consultation->id)
            ->with('success', 'Konsultasi manual berhasil dibuat. Email notifikasi telah dikirim ke klien.');
    }

    public function show(Consultation $consultation)
    {
        $consultation->load('user', 'booking');
        return view('admin.consultations.show', compact('consultation'));
    }

    public function updateStatus(Request $request, Consultation $consultation)
    {
        $request->validate(['status' => 'required|in:pending,confirmed,done,cancelled,converted']);

        $consultation->update([
            'status' => $request->status,
            'reminder_sent' => $request->status === 'confirmed' ? false : $consultation->reminder_sent,
        ]);

        if ($request->status === 'cancelled') {
            $this->notifyCancellation($consultation);
        }

        if ($request->status === 'confirmed') {
            $this->scheduleReminder($consultation);
        }

        return back()->with('success', 'Status konsultasi diperbarui.');
    }

    public function reschedule(Request $request, Consultation $consultation)
    {
        $data = $request->validate([
            'preferred_date' => 'required|date|after_or_equal:today',
            'preferred_time' => 'required|date_format:H:i',
            'consultation_type' => 'required|in:online,offline',
        ]);

        $consultation->update([
            'preferred_date' => $data['preferred_date'],
            'preferred_time' => $data['preferred_time'],
            'consultation_type' => $data['consultation_type'],
            'status' => 'confirmed',
            'reminder_sent' => false,
        ]);

        try {
            Mail::to($consultation->email)->send(new ConsultationRescheduledMail($consultation));
        } catch (\Exception $e) {
            // silent fail, admin will see flash success regardless
        }

        $this->scheduleReminder($consultation);

        return back()->with('success', 'Jadwal baru tersimpan dan email konfirmasi dikirim ke klien.');
    }

    public function saveMeetingNotes(Request $request, Consultation $consultation)
    {
        $request->validate([
            'meeting_notes' => 'nullable|string',
            'followup_notes' => 'nullable|string',
        ]);
        $consultation->update([
            'meeting_notes' => $request->meeting_notes,
            'followup_notes' => $request->followup_notes,
            'status' => 'done',
        ]);
        return back()->with('success', 'Catatan meeting tersimpan.');
    }

    public function sendReminder(Consultation $consultation)
    {
        try {
            Mail::to($consultation->email)->send(new ConsultationReminderMail($consultation));
            $consultation->update(['reminder_sent' => true]);
            return back()->with('success', 'Reminder berhasil dikirim ke ' . $consultation->email);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengirim reminder: ' . $e->getMessage());
        }
    }

    protected function scheduleReminder(Consultation $consultation): void
    {
        if (!$consultation->preferred_date || !$consultation->preferred_time) {
            return;
        }

        $preferredDateTime = $consultation->preferred_date->copy()->setTimeFromTimeString($consultation->preferred_time);
        $runAt = $preferredDateTime->copy()->subHours(3);

        if ($preferredDateTime->isPast()) {
            return;
        }

        if ($runAt->isPast()) {
            SendConsultationReminderJob::dispatch($consultation->id);
        } else {
            SendConsultationReminderJob::dispatch($consultation->id)->delay($runAt);
        }
    }

    protected function notifyCancellation(Consultation $consultation): void
    {
        try {
            Mail::to($consultation->email)->send(new ConsultationCancelledMail($consultation));
        } catch (\Exception $e) {
            // ignore
        }
    }
}
