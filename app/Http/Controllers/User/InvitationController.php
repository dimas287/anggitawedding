<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\InvitationTemplate;
use App\Models\Booking;
use App\Models\Rsvp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class InvitationController extends Controller
{
    private function isInvitationOnlyOrder(Booking $booking): bool
    {
        $hasInvitation = (bool) $booking->invitation;
        $packageHasInvitation = (bool) (optional($booking->package)->has_digital_invitation ?? false);

        return $hasInvitation && !$packageHasInvitation;
    }

    public function index(Booking $booking)
    {
        if (Auth::user()->isAdmin() === false && $booking->user_id != Auth::id()) {
            abort(403);
        }
        $booking->loadMissing('package', 'invitation.template');

        $isInvitationOnly = $this->isInvitationOnlyOrder($booking);
        if (!$isInvitationOnly && !in_array($booking->status, ['dp_paid', 'in_progress', 'completed'])) {
            return redirect()->route('user.booking.show', $booking->id)
                ->with('error', 'Fitur undangan aktif setelah DP terbayar.');
        }

        $invitation = $booking->invitation;
        $templates = InvitationTemplate::where('is_active', true)->orderBy('sort_order')->get();
        $includesDigitalInvitation = optional($booking->package)->has_digital_invitation ?? false;
        return view('user.invitation.index', compact('booking', 'invitation', 'templates', 'includesDigitalInvitation'));
    }

    public function edit(Booking $booking)
    {
        if (Auth::user()->isAdmin() === false && $booking->user_id != Auth::id()) {
            abort(403);
        }
        $booking->loadMissing('package', 'invitation');

        $isInvitationOnly = $this->isInvitationOnlyOrder($booking);
        if (!$isInvitationOnly && !in_array($booking->status, ['dp_paid', 'in_progress', 'completed'])) abort(403);

        $invitation = $booking->invitation;
        $templates = InvitationTemplate::where('is_active', true)->orderBy('sort_order')->get();
        $includesDigitalInvitation = optional($booking->package)->has_digital_invitation ?? false;
        return view('user.invitation.edit', compact('booking', 'invitation', 'templates', 'includesDigitalInvitation'));
    }

    public function update(Request $request, Booking $booking)
    {
        if (Auth::user()->isAdmin() === false && $booking->user_id != Auth::id()) {
            abort(403);
        }

        $request->validate([
            'template_id' => 'nullable|exists:invitation_templates,id',
            'groom_name' => 'required|string|max:100',
            'groom_short_name' => 'nullable|string|max:50',
            'bride_name' => 'required|string|max:100',
            'bride_short_name' => 'nullable|string|max:50',
            'groom_father' => 'nullable|string|max:100',
            'groom_mother' => 'nullable|string|max:100',
            'bride_father' => 'nullable|string|max:100',
            'bride_mother' => 'nullable|string|max:100',
            'akad_datetime' => 'nullable|date',
            'akad_venue' => 'nullable|string|max:200',
            'akad_address' => 'nullable|string',
            'reception_datetime' => 'nullable|date',
            'reception_venue' => 'nullable|string|max:200',
            'reception_address' => 'nullable|string',
            'maps_link' => 'nullable|url',
            'love_story' => 'nullable|string',
            'opening_quote' => 'nullable|string',
            'closing_message' => 'nullable|string',
            'hashtag' => 'nullable|string|max:50',
            'qris_image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:3072',
            'photo_prewedding' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:5120',
            'music_file' => 'nullable|file|mimes:mp3,ogg,wav|max:10240',
            'gallery_photos' => 'nullable|array|max:20',
            'gallery_photos.*' => 'file|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $invitation = $booking->invitation;
        $data = $request->only([
            'template_id', 'groom_name', 'groom_short_name', 'bride_name', 'bride_short_name', 'groom_father', 'groom_mother',
            'bride_father', 'bride_mother', 'akad_datetime', 'akad_venue', 'akad_address',
            'reception_datetime', 'reception_venue', 'reception_address', 'maps_link',
            'love_story', 'opening_quote', 'closing_message', 'hashtag'
        ]);
        
        // Bank Accounts
        $bankName = $request->input('bank_name');
        $bankAccount = $request->input('bank_account');
        $bankOwner = $request->input('bank_owner');
        if ($bankName || $bankAccount || $bankOwner) {
            $data['bank_accounts'] = [
                [
                    'bank_name' => $bankName,
                    'account_number' => $bankAccount,
                    'account_name' => $bankOwner,
                ]
            ];
        }

        // QRIS Image
        if ($request->hasFile('qris_image')) {
            if ($invitation->qris_image) {
                \Illuminate\Support\Facades\Storage::disk('local')->delete($invitation->qris_image);
            }
            $data['qris_image'] = $request->file('qris_image')->store('invitations/qris', 'local');
        } elseif ($request->boolean('clear_qris')) {
            if ($invitation->qris_image) {
                \Illuminate\Support\Facades\Storage::disk('local')->delete($invitation->qris_image);
            }
            $data['qris_image'] = null;
        }

        if ($request->hasFile('photo_prewedding')) {
            $data['photo_prewedding'] = $request->file('photo_prewedding')->store('invitations/prewedding', 'local');
        }

        if ($request->hasFile('music_file')) {
            $data['music_file'] = $request->file('music_file')->store('invitations/music', 'local');
        }

        if ($request->hasFile('gallery_photos')) {
            $gallery = [];
            foreach ($request->file('gallery_photos') as $photo) {
                $gallery[] = $photo->store('invitations/gallery', 'local');
            }
            $data['gallery_photos'] = array_merge($invitation->gallery_photos ?? [], $gallery);
        }

        // Handle dynamic media slots
        if ($request->has('media_slots')) {
            $slotsData = $invitation->media_files ?? [];
            foreach ($request->file('media_slots', []) as $key => $fileOrFiles) {
                if (is_array($fileOrFiles)) {
                    $urls = [];
                    foreach ($fileOrFiles as $f) {
                        $urls[] = $f->store('invitations/dynamic', 'local');
                    }
                    $slotsData[$key] = $urls;
                } else {
                    $slotsData[$key] = $fileOrFiles->store('invitations/dynamic', 'local');
                }
            }
            $data['media_files'] = $slotsData;
        }

        $invitation->update($data);
        return back()->with('success', 'Undangan berhasil diperbarui.');
    }

    public function publish(Booking $booking)
    {
        if (Auth::user()->isAdmin() === false && $booking->user_id != Auth::id()) {
            abort(403);
        }

        $booking->loadMissing('package', 'invitation');
        $isInvitationOnly = $this->isInvitationOnlyOrder($booking);
        if ($isInvitationOnly && $booking->payment_status !== 'paid_full') {
            return back()->with('error', 'Undangan belum bisa dipublish. Silakan selesaikan pembayaran terlebih dulu.');
        }

        $invitation = $booking->invitation;
        $invitation->update(['is_published' => !$invitation->is_published]);
        $msg = $invitation->is_published ? 'Undangan berhasil dipublikasikan!' : 'Undangan disembunyikan.';
        return back()->with('success', $msg);
    }

    public function show(string $slug)
    {
        $invitation = Invitation::where('slug', $slug)->where('is_published', true)->firstOrFail();
        $invitation->increment('view_count');
        return view('invitation.show', compact('invitation'));
    }

    public function rsvp(Request $request, string $slug)
    {
        $invitation = Invitation::where('slug', $slug)->where('is_published', true)->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'guests_count' => 'required|integer|min:1|max:10',
            'attendance' => 'required|in:hadir,tidak_hadir,mungkin',
            'message' => 'nullable|string|max:500',
        ]);

        Rsvp::create([
            'invitation_id' => $invitation->id,
            'name' => strip_tags($request->name),
            'phone' => strip_tags($request->phone),
            'guests_count' => $request->guests_count,
            'attendance' => $request->attendance,
            'message' => strip_tags($request->message),
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'RSVP berhasil dikirim. Terima kasih!');
    }

    public function rsvpStats(Booking $booking)
    {
        if (Auth::user()->isAdmin() === false && $booking->user_id != Auth::id()) {
            abort(403);
        }
        $invitation = $booking->invitation;
        if (!$invitation) abort(404);
        $invitation->loadCount('views');
        $stats = $invitation->rsvp_stats;
        $stats['views_total'] = (int) ($invitation->views_count ?? 0);
        $stats['view_count'] = (int) ($invitation->view_count ?? 0);
        $rsvps = $invitation->rsvps()->latest()->get();
        $guestbooks = $invitation->guestbook()->latest()->get();
        $visitors = $invitation->views()->latest()->take(50)->get();
        return view('user.invitation.rsvp', compact('booking', 'invitation', 'stats', 'rsvps', 'guestbooks', 'visitors'));
    }

    public function qrisImage(string $slug)
    {
        $invitation = \App\Models\Invitation::where('slug', $slug)->where('is_published', true)->firstOrFail();
        if (!$invitation->qris_image) {
            abort(404);
        }
        return \Illuminate\Support\Facades\Storage::disk('local')->download($invitation->qris_image);
    }
}
