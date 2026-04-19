@component('mail::message')
# Bukti Pembayaran Baru

Ada bukti pembayaran manual baru dari klien.

**Booking**: {{ $booking->booking_code }}  
**Nama Klien**: {{ $booking->groom_name }} & {{ $booking->bride_name }}  
**Tanggal Acara**: {{ $booking->event_date->format('d M Y') }}  
**Nominal**: Rp {{ number_format($payment->amount, 0, ',', '.') }}  
**Metode**: {{ ucfirst($payment->method) }}  
**Status Saat Ini**: {{ ucfirst($payment->status) }}

@if($payment->notes)
**Catatan**: {{ $payment->notes }}
@endif

@if($payment->proof_url)
[Klik untuk melihat bukti pembayaran]({{ $payment->proof_url }})
@endif

Silakan masuk ke dashboard admin untuk memverifikasi pembayaran ini.

Terima kasih,
{{ config('app.name') }}
@endcomponent
