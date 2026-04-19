<?php

namespace App\Support;

use App\Models\Booking;
use Carbon\Carbon;

class BookingDateHelper
{
    private const CONFIRMED_STATUSES = ['dp_paid', 'in_progress', 'completed'];

    public static function availabilityMeta(string $date, ?int $ignoreBookingId = null): array
    {
        $date = Carbon::parse($date)->toDateString();

        if (self::hasConfirmedBooking($date, $ignoreBookingId)) {
            return [
                'status' => 'full',
                'label' => 'Tanggal Penuh',
                'message' => 'Tanggal ini sudah ada event terkonfirmasi. Silakan pilih tanggal lain atau hubungi admin.',
            ];
        }

        if (self::hasPendingBooking($date, $ignoreBookingId)) {
            return [
                'status' => 'tentative',
                'label' => 'Tentative',
                'message' => 'Sudah ada calon klien lain, namun tanggal masih bisa Anda klaim. Segera selesaikan booking.',
            ];
        }

        return [
            'status' => 'available',
            'label' => 'Tersedia',
            'message' => 'Tanggal ini masih kosong. Anda bisa melanjutkan booking.',
        ];
    }

    public static function hasConfirmedBooking(string $date, ?int $ignoreBookingId = null): bool
    {
        $date = Carbon::parse($date)->toDateString();

        return Booking::whereDate('event_date', $date)
            ->whereIn('status', self::CONFIRMED_STATUSES)
            ->when($ignoreBookingId, fn ($q) => $q->where('id', '!=', $ignoreBookingId))
            ->exists();
    }

    public static function hasPendingBooking(string $date, ?int $ignoreBookingId = null): bool
    {
        $date = Carbon::parse($date)->toDateString();

        return Booking::whereDate('event_date', $date)
            ->where('status', 'pending')
            ->when($ignoreBookingId, fn ($q) => $q->where('id', '!=', $ignoreBookingId))
            ->exists();
    }
}
