<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Invitation;
use App\Models\InvitationTemplate;
use App\Models\Package;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvitationBookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'invitation.template', 'package'])
            ->whereHas('invitation')
            ->where(function ($q) {
                $q->where('venue', 'Undangan Digital')
                  ->orWhereDoesntHave('package', function ($inner) {
                      $inner->where('includes_digital_invitation', true);
                  });
            })
            ->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($sub) use ($search) {
                $sub->where('booking_code', 'like', "%{$search}%")
                    ->orWhere('groom_name', 'like', "%{$search}%")
                    ->orWhere('bride_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('event_date', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('event_date', '<=', $request->input('date_to'));
        }

        $bookings = $query->paginate(15);
        $clients = User::where('role', 'client')->orderBy('name')->get(['id', 'name', 'email']);
        $templates = InvitationTemplate::where('is_active', true)->orderBy('name')->get();

        return view('admin.bookings.invitation-index', compact('bookings', 'clients', 'templates'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'template_id' => 'required|exists:invitation_templates,id',
            'groom_name' => 'required|string|max:120',
            'groom_short_name' => 'required|string|max:60',
            'bride_name' => 'required|string|max:120',
            'bride_short_name' => 'required|string|max:60',
            'event_date' => 'required|date',
            'price' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $template = InvitationTemplate::findOrFail($data['template_id']);
        $package = Package::where('slug', 'undangan-digital')->first();
        if (!$package) {
            $package = Package::orderBy('sort_order')->first();
        }

        if (!$package) {
            abort(500, 'Paket digital belum disiapkan. Mohon buat paket "Undangan Digital" terlebih dahulu.');
        }

        $user = User::findOrFail($data['user_id']);

        $booking = DB::transaction(function () use ($data, $package, $template, $user) {
            $booking = Booking::create([
                'booking_code' => Booking::generateCode(),
                'user_id' => $user->id,
                'package_id' => $package->id,
                'groom_name' => $data['groom_name'],
                'groom_short_name' => $data['groom_short_name'],
                'bride_name' => $data['bride_name'],
                'bride_short_name' => $data['bride_short_name'],
                'event_date' => $data['event_date'],
                'venue' => 'Undangan Digital',
                'phone' => $user->phone,
                'email' => $user->email,
                'notes' => $data['notes'] ?? 'Order undangan digital dibuat oleh admin.',
                'package_price' => $data['price'],
                'dp_amount' => 0,
                'total_paid' => 0,
                'status' => 'pending',
                'payment_status' => 'unpaid',
            ]);

            Invitation::create([
                'booking_id' => $booking->id,
                'user_id' => $booking->user_id,
                'template_id' => $template->id,
                'slug' => Str::slug($booking->groom_name . '-' . $booking->bride_name . '-' . $booking->id . '-' . Str::random(3)),
                'groom_name' => $booking->groom_name,
                'bride_name' => $booking->bride_name,
                'is_published' => false,
                'rsvp_enabled' => true,
            ]);

            return $booking;
        });

        return redirect()->route('admin.bookings.show', $booking->id)
            ->with('success', 'Booking undangan digital berhasil dibuat.');
    }
}
