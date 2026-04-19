<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Consultation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CalendarController extends Controller
{
    public function index()
    {
        $upcomingBookings = Booking::with('package')
            ->whereNotNull('event_date')
            ->whereBetween('event_date', [now()->startOfDay(), now()->addDays(30)->endOfDay()])
            ->orderBy('event_date')
            ->get();

        $upcomingConsultations = Consultation::with('user')
            ->whereNotNull('preferred_date')
            ->whereBetween('preferred_date', [now()->startOfDay(), now()->addDays(30)->endOfDay()])
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('preferred_date')
            ->orderBy('preferred_time')
            ->get();

        return view('admin.calendar', compact('upcomingBookings', 'upcomingConsultations'));
    }

    public function events(Request $request)
    {
        $start = Carbon::parse($request->start)->startOfDay();
        $end = Carbon::parse($request->end)->endOfDay();

        $bookings = Booking::whereBetween('event_date', [$start, $end])
            ->with('user', 'package')
            ->get()
            ->map(function ($booking) {
                $colors = [
                    'pending' => '#EAB308',
                    'dp_paid' => '#3B82F6',
                    'in_progress' => '#6366F1',
                    'completed' => '#22C55E',
                    'cancelled' => '#EF4444',
                ];
                return [
                    'id' => 'booking-' . $booking->id,
                    'title' => '💍 ' . $booking->couple_short_display,
                    'start' => $booking->event_date->format('Y-m-d'),
                    'color' => $colors[$booking->status] ?? '#6B7280',
                    'url' => route('admin.bookings.show', $booking->id),
                    'extendedProps' => [
                        'type' => 'wedding',
                        'status' => $booking->status_label,
                        'package' => $booking->package->name,
                        'venue' => $booking->venue,
                    ],
                ];
            });

        $consultations = Consultation::whereBetween('preferred_date', [$start, $end])
            ->whereIn('status', ['pending', 'confirmed'])
            ->with('user')
            ->get()
            ->map(function ($c) {
                return [
                    'id' => 'consultation-' . $c->id,
                    'title' => '📋 Konsultasi: ' . $c->name,
                    'start' => $c->preferred_date->format('Y-m-d') . 'T' . $c->preferred_time,
                    'color' => '#8B5CF6',
                    'url' => route('admin.consultations.show', $c->id),
                    'extendedProps' => [
                        'type' => 'consultation',
                        'status' => $c->status_label,
                    ],
                ];
            });

        return response()->json([...$bookings, ...$consultations]);
    }
}
