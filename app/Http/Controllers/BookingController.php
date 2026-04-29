<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Booking;
use App\Models\Package;
use App\Models\Invitation;
use App\Models\FinancialTransaction;
use App\Mail\BookingConfirmationMail;
use App\Support\BookingDateHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function checkDate(Request $request)
    {
        $request->validate(['date' => 'required|date|after:today']);
        $ignoreBookingId = $request->input('booking_id');
        $meta = BookingDateHelper::availabilityMeta($request->date, $ignoreBookingId ? (int) $ignoreBookingId : null);

        return response()->json($meta);
    }

    public function selectPackage(Request $request)
    {
        $request->validate(['date' => 'nullable|date|after:today']);

        $packages = Package::where('is_active', true)
            ->with('mediaItems')
            ->withCount(['bookings as popular_score' => function ($query) {
                $query->whereIn('status', ['pending', 'dp_paid', 'in_progress', 'completed']);
            }])
            ->orderBy('sort_order')
            ->get();

        $popularPackageId = $packages->sortByDesc('popular_score')->first()?->id;
        $packagesByCategory = $packages->groupBy('category');
        $popularPackageIdsByCategory = $packagesByCategory->map(function ($group) {
            return $group->sortByDesc('popular_score')->first()?->id;
        });
        $categoryLabels = Package::CATEGORY_LABELS;
        $date = $request->date ?? now()->addDay()->toDateString();
        $dateAvailability = BookingDateHelper::availabilityMeta($date);

        return view('booking.select-package', compact(
            'packages',
            'packagesByCategory',
            'categoryLabels',
            'date',
            'popularPackageId',
            'popularPackageIdsByCategory',
            'dateAvailability'
        ));
    }

    public function form(Request $request)
    {
        $request->validate([
            'date' => 'required|date|after:today',
            'package_id' => 'required|exists:packages,id',
        ]);

        if (!Auth::check()) {
            session(['booking_intent' => $request->all()]);
            return redirect()->route('login')->with('info', 'Silakan login untuk melanjutkan booking.');
        }

        $package = Package::findOrFail($request->package_id);
        $date = $request->date;
        $dateAvailability = BookingDateHelper::availabilityMeta($date);

        if ($dateAvailability['status'] === 'full') {
            return redirect()->route('booking.select-package', ['date' => $date])
                ->with('error', 'Tanggal yang dipilih sudah penuh. Silakan pilih tanggal lain.');
        }

        $isIndividual = $package->isIndividualService();

        return view('booking.form', compact('package', 'date', 'isIndividual', 'dateAvailability'));
    }

    public function store(Request $request)
    {
        $rules = [
            'package_id' => 'required|exists:packages,id',
            'event_date' => 'required|date|after:today',
            'venue' => 'required|string|max:255',
            'venue_address' => 'nullable|string',
            'phone' => 'required|string|max:20',
            'estimated_guests' => 'nullable|integer|min:10',
            'notes' => 'nullable|string',
            'consultation_preference' => 'nullable|string',
        ];

        $package = $request->filled('package_id') ? Package::find($request->package_id) : null;
        $isIndividual = $package?->isIndividualService() ?? false;

        if ($isIndividual) {
            $rules['client_name'] = 'required|string|max:100';
            $rules['client_label'] = 'nullable|string|max:100';
        } else {
            $rules['groom_name'] = 'required|string|max:100';
            $rules['bride_name'] = 'required|string|max:100';
            $rules['groom_short_name'] = 'required|string|max:60';
            $rules['bride_short_name'] = 'required|string|max:60';
        }

        $validated = $request->validate($rules);

        $eventDate = $validated['event_date'];
        $package = Package::findOrFail($validated['package_id']);
        $isIndividual = $package->isIndividualService();
        
        // Use effective_price to account for any active promos/discounts
        $packagePrice = $package->effective_price;
        $dpAmount = $package->dp_amount;
        
        $groomName = $isIndividual ? $validated['client_name'] : $validated['groom_name'];
        $brideName = $isIndividual ? ($validated['client_label'] ?? 'Personal Service') : $validated['bride_name'];

        $booking = DB::transaction(function () use ($validated, $eventDate, $request, $package, $isIndividual, $groomName, $brideName, $packagePrice, $dpAmount) {
            // CRITICAL: Lock records for this date to prevent Race Conditions (Atomic Booking)
            Booking::whereDate('event_date', $eventDate)->lockForUpdate()->get();

            $availability = BookingDateHelper::availabilityMeta($eventDate);

            if ($availability['status'] === 'full') {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'event_date' => 'Maaf, tanggal tersebut baru saja dipesan oleh orang lain. Silakan pilih tanggal lain.'
                ]);
            }

            $existingBooking = Booking::where('user_id', Auth::id())
                ->where('package_id', $package->id)
                ->whereDate('event_date', $eventDate)
                ->where('payment_status', 'unpaid')
                ->whereIn('status', ['pending'])
                ->latest('id')
                ->first();

            if ($existingBooking) {
                return $existingBooking;
            }

            $newBooking = new Booking([
                'booking_code' => Booking::generateCode(),
                'user_id' => Auth::id(),
                'package_id' => $package->id,
                'groom_name' => strip_tags($groomName),
                'groom_short_name' => strip_tags($isIndividual ? $validated['client_name'] : ($validated['groom_short_name'] ?? null)),
                'bride_name' => strip_tags($brideName),
                'bride_short_name' => strip_tags($isIndividual
                    ? ($validated['client_label'] ?? $validated['client_name'])
                    : ($validated['bride_short_name'] ?? null)),
                'event_date' => $eventDate,
                'venue' => strip_tags($validated['venue']),
                'venue_address' => strip_tags($validated['venue_address'] ?? null),
                'phone' => strip_tags($validated['phone']),
                'email' => Auth::user()->email,
                'estimated_guests' => $validated['estimated_guests'] ?? null,
                'notes' => strip_tags($validated['notes'] ?? null),
                'consultation_preference' => strip_tags($validated['consultation_preference'] ?? null),
                'package_price' => $packagePrice,
                'dp_amount' => $dpAmount,
            ]);

            $newBooking->total_paid = 0;
            $newBooking->status = 'pending';
            $newBooking->payment_status = 'unpaid';
            $newBooking->save();

            try {
                \Illuminate\Support\Facades\Mail::to(Auth::user()->email)->send(new \App\Mail\BookingConfirmationMail($newBooking));
            } catch (\Exception $e) {
                // Silent fail for mail
            }

            return $newBooking;
        });

        if ($booking->wasRecentlyCreated) {
            return redirect()->route('payment.checkout', $booking->id)
                ->with('success', 'Booking berhasil dibuat! Silakan pilih metode pembayaran.');
        }

        return redirect()->route('payment.checkout', $booking->id)
            ->with('info', 'Booking sebelumnya masih menunggu pembayaran. Silakan lanjutkan pembayaran yang sudah ada.');
    }
}
