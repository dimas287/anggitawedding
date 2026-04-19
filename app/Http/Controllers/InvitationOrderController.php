<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Invitation;
use App\Models\InvitationTemplate;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class InvitationOrderController extends Controller
{
    public function start(Request $request)
    {
        $templates = InvitationTemplate::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $selectedTemplateId = null;

        if ($request->filled('template')) {
            $selectedTemplateId = (int) $request->input('template');
        } elseif ($request->filled('template_slug')) {
            $selectedTemplateId = InvitationTemplate::where('slug', $request->input('template_slug'))
                ->value('id');
        }

        return view('invitation-order.start', compact('templates', 'selectedTemplateId'));
    }

    public function checkout(Request $request)
    {
        if (!Auth::check()) {
            session(['invitation_order_intent' => $request->all()]);
            return redirect()->route('login')->with('info', 'Silakan login untuk melanjutkan checkout undangan.');
        }

        $validated = $request->validate([
            'template_id' => 'required|exists:invitation_templates,id',
            'groom_name' => 'required|string|max:100',
            'groom_short_name' => 'nullable|string|max:60',
            'bride_name' => 'required|string|max:100',
            'bride_short_name' => 'nullable|string|max:60',
        ]);

        $template = InvitationTemplate::findOrFail($validated['template_id']);

        $package = Package::where('slug', 'undangan-digital')->first();

        if (!$package) {
            $package = Package::where('is_active', true)->orderBy('sort_order')->first();
        }

        if (!$package) {
            abort(500, 'Package untuk checkout undangan digital belum tersedia.');
        }

        $templatePrice = (float) ($template->effective_price ?? 0);
        $dpAmount = 0;

        $existingBooking = Booking::where('user_id', Auth::id())
            ->where('venue', 'Undangan Digital')
            ->where('payment_status', 'unpaid')
            ->where('status', 'pending')
            ->whereHas('invitation', function ($query) use ($validated) {
                $query->where('template_id', $validated['template_id']);
            })
            ->latest('id')
            ->first();

        if ($existingBooking) {
            return redirect()->route('user.booking.show', $existingBooking->id)
                ->with('info', 'Booking undangan sebelumnya masih menunggu pembayaran. Silakan lanjutkan pembayaran yang sudah ada.');
        }

        $booking = Booking::create([
            'booking_code' => Booking::generateCode(),
            'user_id' => Auth::id(),
            'package_id' => $package->id,
            'groom_name' => $validated['groom_name'],
            'groom_short_name' => ($validated['groom_short_name'] ?? null) ?: $validated['groom_name'],
            'bride_name' => $validated['bride_name'],
            'bride_short_name' => ($validated['bride_short_name'] ?? null) ?: $validated['bride_name'],
            'event_date' => now()->addYear()->toDateString(),
            'venue' => 'Undangan Digital',
            'venue_address' => null,
            'phone' => Auth::user()->phone ?? '-',
            'email' => Auth::user()->email,
            'estimated_guests' => null,
            'notes' => 'Order undangan digital (tanpa paket wedding) — draft dibuat otomatis.',
            'consultation_preference' => null,
            'package_price' => $templatePrice,
            'dp_amount' => $dpAmount,
            'total_paid' => 0,
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);

        Invitation::create([
            'booking_id' => $booking->id,
            'user_id' => $booking->user_id,
            'template_id' => $validated['template_id'],
            'slug' => Str::slug($validated['groom_name'] . '-' . $validated['bride_name'] . '-' . $booking->id),
            'groom_name' => $validated['groom_name'],
            'bride_name' => $validated['bride_name'],
            'is_published' => false,
            'rsvp_enabled' => true,
        ]);

        return redirect()->route('user.booking.show', $booking->id)
            ->with('success', 'Undangan draft berhasil dibuat. Silakan lengkapi data undangan Anda.');
    }
}
