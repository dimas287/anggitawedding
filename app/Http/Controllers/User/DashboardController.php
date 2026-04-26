<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Consultation;
use App\Models\Package;
use App\Models\Payment;
use App\Models\BookingFitting;
use App\Support\BookingDateHelper;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $bookings = Booking::where('user_id', $user->id)
            ->with(['package', 'invitation', 'extraCharges'])
            ->latest()
            ->get();
        $consultations = Consultation::where('user_id', $user->id)->latest()->take(5)->get();
        $latestBooking = $bookings->first();
        return view('user.dashboard', compact('user', 'bookings', 'consultations', 'latestBooking'));
    }

    public function bookingShow(Booking $booking)
    {
        if (Auth::user()->isAdmin() === false && $booking->user_id != Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke booking ini.');
        }
        $booking->load('package', 'payments', 'extraCharges', 'invitation', 'consultations', 'vendors', 'documents', 'review', 'fittings', 'wardrobeItems');
        $unreadChats = \App\Models\Chat::where('booking_id', $booking->id)
            ->where('receiver_id', Auth::id())->where('is_read', false)->count();
        $availablePackages = Package::where('is_active', true)->orderBy('sort_order')->get();
        return view('user.booking-detail', compact('booking', 'unreadChats', 'availablePackages'));
    }

    public function downloadInvoice(Booking $booking)
    {
        if (Auth::user()->isAdmin() === false && $booking->user_id != Auth::id()) {
            abort(403);
        }

        $booking->load('user', 'package', 'payments', 'extraCharges', 'invitation.template');
        $extraTotal = $booking->active_extra_charges_total;
        $grandTotal = $booking->package_price + $extraTotal;

        $pdf = Pdf::loadView('pdf.invoice', [
            'booking' => $booking,
            'extraTotal' => $extraTotal,
            'grandTotal' => $grandTotal,
        ])->setPaper('a4');

        return $pdf->download('invoice-' . $booking->booking_code . '.pdf');
    }

    public function storeFitting(Request $request, Booking $booking)
    {
        if (Auth::user()->isAdmin() === false && $booking->user_id != Auth::id()) {
            abort(403);
        }

        $data = $request->validate([
            'scheduled_at' => 'required|date|after:now',
            'location' => 'nullable|string|max:160',
            'focus' => 'nullable|string|max:120',
            'notes' => 'nullable|string',
        ]);

        $booking->fittings()->create([
            'scheduled_at' => $data['scheduled_at'],
            'location' => $data['location'] ?? null,
            'focus' => $data['focus'] ?? null,
            'notes' => $data['notes'] ?? null,
            'created_by' => Auth::id(),
        ]);

        return back()->with('success', 'Jadwal fitting berhasil ditambahkan.');
    }

    public function deleteFitting(Booking $booking, BookingFitting $fitting)
    {
        if (Auth::user()->isAdmin() === false && $booking->user_id != Auth::id()) {
            abort(403);
        }
        if ($fitting->booking_id !== $booking->id || ($fitting->created_by && $fitting->created_by != Auth::id() && !Auth::user()->isAdmin())) {
            abort(403);
        }

        $fitting->delete();

        return back()->with('success', 'Jadwal fitting berhasil dihapus.');
    }

    public function profile()
    {
        $user = Auth::user();
        return view('user.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'address' => 'nullable|string|max:255',
        ]);

        $data = $request->only('name', 'phone', 'email', 'address');

        if ($request->hasFile('avatar')) {
            $request->validate(['avatar' => 'image|max:2048']);
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $path;
        }

        $user->update($data);
        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak sesuai.']);
        }

        $user->update(['password' => Hash::make($request->password)]);
        return back()->with('success', 'Password berhasil diubah.');
    }

    public function review(Request $request, Booking $booking)
    {
        if (Auth::user()->isAdmin() === false && $booking->user_id != Auth::id()) {
            abort(403);
        }
        if ($booking->status !== 'completed') {
            return back()->with('error', 'Ulasan hanya bisa diberikan setelah event selesai.');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:100',
            'review' => 'required|string|min:20',
        ]);

        $reviewData = [
            'booking_id' => $booking->id,
            'user_id' => Auth::id(),
            'rating' => $request->rating,
            'title' => $request->title,
            'review' => $request->review,
            'is_published' => false,
        ];

        if ($request->hasFile('photo')) {
            $request->validate(['photo' => 'image|max:3072']);
            $reviewData['photo'] = $request->file('photo')->store('reviews', 'public');
        }

        $booking->review()->updateOrCreate(['booking_id' => $booking->id], $reviewData);
        return back()->with('success', 'Ulasan berhasil dikirim. Terima kasih!');
    }

    public function changePackage(Request $request, Booking $booking)
    {
        if (Auth::user()->isAdmin() === false && $booking->user_id != Auth::id()) {
            abort(403);
        }
        if ($booking->status !== 'pending') {
            return back()->with('error', 'Paket hanya bisa diganti ketika status booking masih pending.');
        }

        $data = $request->validate([
            'package_id' => 'required|exists:packages,id',
        ]);

        $package = Package::where('is_active', true)->find($data['package_id']);
        if (!$package) {
            return back()->with('error', 'Paket tidak valid atau sudah nonaktif.');
        }

        $booking->update([
            'package_id' => $package->id,
            'package_price' => $package->price,
            'dp_amount' => $package->price * 0.30,
        ]);

        return back()->with('success', 'Paket berhasil diperbarui.');
    }

    public function rescheduleBooking(Request $request, Booking $booking)
    {
        if (Auth::user()->isAdmin() === false && $booking->user_id != Auth::id()) {
            abort(403);
        }
        if (!in_array($booking->status, ['pending', 'dp_paid'], true)) {
            return back()->with('error', 'Perubahan tanggal hanya tersedia untuk booking pending atau yang sudah bayar DP.');
        }

        $data = $request->validate([
            'event_date' => 'required|date|after:today',
        ]);

        $availability = BookingDateHelper::availabilityMeta($data['event_date'], $booking->id);
        if ($availability['status'] === 'full') {
            return back()->withErrors(['event_date' => 'Tanggal baru tidak tersedia. Silakan pilih tanggal lain.']);
        }

        $booking->update(['event_date' => $data['event_date']]);
        if ($booking->invitation) {
            $booking->invitation->update(['reception_datetime' => $data['event_date']]);
        }

        return back()->with('success', 'Tanggal acara berhasil diperbarui.');
    }

    public function cancelBooking(Request $request, Booking $booking)
    {
        if (Auth::user()->isAdmin() === false && $booking->user_id != Auth::id()) {
            abort(403);
        }
        if ($booking->status !== 'pending') {
            return back()->with('error', 'Booking hanya bisa dibatalkan sebelum pembayaran DP.');
        }
        if ($booking->total_paid > 0) {
            return back()->with('error', 'Booking ini sudah memiliki pembayaran sehingga tidak bisa dibatalkan mandiri.');
        }

        $booking->update([
            'status' => 'cancelled',
            'payment_status' => 'unpaid',
        ]);

        Payment::where('booking_id', $booking->id)->where('status', 'pending')->delete();

        return redirect()->route('user.dashboard')->with('success', 'Booking berhasil dibatalkan.');
    }
}
