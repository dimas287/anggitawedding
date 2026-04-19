@extends('layouts.guest')
@section('title', 'Pembayaran Berhasil')

@section('content')
@php
    $isInvitationOnly = (bool) $booking->invitation && !(optional($booking->package)->has_digital_invitation ?? false);
@endphp
@php
    $statusIcon = [
        'success' => ['bg' => 'bg-green-100 dark:bg-green-900/20', 'text' => 'text-green-500 dark:text-green-400', 'icon' => 'fa-check-circle', 'title' => 'Pembayaran Berhasil!'],
        'pending' => ['bg' => 'bg-yellow-100 dark:bg-yellow-900/20', 'text' => 'text-yellow-500 dark:text-yellow-400', 'icon' => 'fa-hourglass-half', 'title' => 'Menunggu Pembayaran'],
        'failed' => ['bg' => 'bg-red-100 dark:bg-red-900/20', 'text' => 'text-red-500 dark:text-red-400', 'icon' => 'fa-times-circle', 'title' => 'Pembayaran Gagal'],
    ][$paymentStatus] ?? ['bg' => 'bg-yellow-100 dark:bg-yellow-900/20', 'text' => 'text-yellow-500 dark:text-yellow-400', 'icon' => 'fa-hourglass-half', 'title' => 'Status Pembayaran'];

    $payMethodName = '';
    $payCode = '';
    $payCompanyCode = '';
    $isVA = false;
    
    if(!empty($paymentDetails)) {
        $ptype = $paymentDetails['payment_type'] ?? '';
        if ($ptype === 'bank_transfer') {
            $isVA = true;
            $bankInfo = $paymentDetails['va_numbers'][0] ?? null;
            if ($bankInfo) {
                $payMethodName = strtoupper($bankInfo['bank']) . ' Virtual Account';
                $payCode = $bankInfo['va_number'];
            } elseif (isset($paymentDetails['permata_va_number'])) {
                $payMethodName = 'Permata Virtual Account';
                $payCode = $paymentDetails['permata_va_number'];
            } elseif (isset($paymentDetails['bca_va_number'])) {
                $payMethodName = 'BCA Virtual Account';
                $payCode = $paymentDetails['bca_va_number'];
            }
        } elseif ($ptype === 'echannel') {
            $isVA = true;
            $payMethodName = 'Mandiri Bill Payment';
            $payCompanyCode = $paymentDetails['biller_code'] ?? '';
            $payCode = $paymentDetails['bill_key'] ?? '';
        } elseif ($ptype === 'cstore') {
            $isVA = true;
            $store = ucfirst($paymentDetails['store'] ?? 'Alfamart / Indomaret');
            $payMethodName = $store;
            $payCode = $paymentDetails['payment_code'] ?? '';
        }
    }
@endphp
<div class="min-h-screen bg-gray-50 dark:bg-[#0A0A0A] pt-24 pb-16 flex items-center justify-center px-4">
    <div class="max-w-lg w-full text-center">
        <div class="bg-white dark:bg-[#111111] rounded-3xl shadow-xl p-10 border border-transparent dark:border-white/10">
            <div class="w-20 h-20 {{ $statusIcon['bg'] }} rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas {{ $statusIcon['icon'] }} {{ $statusIcon['text'] }} text-4xl"></i>
            </div>
            <h1 class="font-playfair text-3xl font-bold text-gray-800 dark:text-white mb-2">{{ $statusIcon['title'] }}</h1>
            <p class="text-gray-500 dark:text-gray-400 mb-6">
                @switch($paymentStatus)
                    @case('success')
                        {{ $isInvitationOnly ? 'Pembayaran undangan berhasil. Anda sekarang bisa publish undangan.' : 'Selamat! Anda kini resmi menjadi klien Anggita Wedding Organizer 🎉' }}
                        @break
                    @case('pending')
                        @if($isVA && $payCode)
                            Selesaikan pembayaran Anda sebelum batas waktu berakhir.
                        @else
                            Pembayaran Anda masih dalam proses (atau menunggu verifikasi). Jika Anda menutup pop-up Midtrans sebelum selesai, silakan coba lagi atau tunggu notifikasi.
                        @endif
                        @break
                    @case('failed')
                        Pembayaran gagal atau dibatalkan. Silakan coba lagi atau gunakan metode lain.
                        @break
                    @default
                        Status pembayaran belum ditentukan. Silakan cek riwayat transaksi Anda.
                @endswitch
            </p>

            @if($paymentStatus === 'pending' && $isVA && $payCode)
                <div class="bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-2xl p-5 mb-4 text-left relative overflow-hidden shadow-inner">
                    <div class="absolute -top-10 -right-10 w-32 h-32 bg-yellow-400 rounded-full opacity-10"></div>
                    <div class="relative z-10">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wider font-semibold">Metode Pembayaran</p>
                        <p class="font-bold text-gray-800 dark:text-white text-lg flex items-center gap-2">
                            <i class="fas fa-university text-blue-600 dark:text-blue-400"></i> {{ $payMethodName }}
                        </p>
                        
                        <div class="mt-4 border-t border-b border-gray-200 dark:border-white/10 py-4 mb-4">
                            @if($payCompanyCode)
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase">Company Code</p>
                                <div class="flex justify-between items-center mb-4">
                                    <p class="font-mono font-bold text-2xl text-gray-800 dark:text-white tracking-wider">{{ $payCompanyCode }}</p>
                                    <button type="button" onclick="copyText('{{ $payCompanyCode }}', this)" class="text-yellow-600 dark:text-yellow-500 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 px-3 py-1 rounded-lg text-xs font-semibold border border-yellow-200 dark:border-yellow-700/40 transition-all focus:outline-none"><i class="far fa-copy mr-1"></i> Salin</button>
                                </div>
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase">Kode Pembayaran</p>
                            @else
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase">{{ str_contains(strtolower($payMethodName), 'virtual account') ? 'Nomor Virtual Account' : 'Kode Pembayaran' }}</p>
                            @endif

                            <div class="flex justify-between items-center">
                                <p class="font-mono font-bold text-2xl text-gray-800 dark:text-white tracking-wider">{{ $payCode }}</p>
                                <button type="button" onclick="copyText('{{ $payCode }}', this)" class="text-yellow-600 dark:text-yellow-500 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 px-3 py-1 rounded-lg text-xs font-semibold border border-yellow-200 dark:border-yellow-700/40 transition-all focus:outline-none"><i class="far fa-copy mr-1"></i> Salin</button>
                            </div>
                        </div>

                        <div class="flex justify-between items-center bg-white dark:bg-[#1a1a1a] p-3 rounded-xl border border-gray-200 dark:border-white/10 shadow-sm">
                            <div>
                                <p class="text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500 mb-0.5 tracking-wider">Total Tagihan</p>
                                <p class="font-bold text-yellow-700 dark:text-yellow-500 text-lg">Rp {{ number_format($paymentDetails['gross_amount'] ?? $payment->amount, 0, ',', '.') }}</p>
                            </div>
                            @php $cleanAmount = rtrim(rtrim(number_format($paymentDetails['gross_amount'] ?? $payment->amount, 2, '.', ''), '0'), '.'); @endphp
                            <button type="button" onclick="copyText('{{ $cleanAmount }}', this)" class="text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white text-xs flex items-center px-2 py-1 bg-gray-50 dark:bg-white/5 rounded border dark:border-white/10 focus:outline-none"><i class="far fa-copy mr-1"></i> Salin Nominal</button>
                        </div>

                        @if(isset($paymentDetails['pdf_url']))
                            <a href="{{ $paymentDetails['pdf_url'] }}" target="_blank" class="mt-4 block text-center text-sm font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 hover:underline">
                                Lihat Cara Pembayaran Secara Lengkap <i class="fas fa-external-link-alt ml-1 text-xs"></i>
                            </a>
                        @endif
                    </div>
                </div>

                @if(isset($paymentDetails['expiry_time']))
                    <div class="bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border border-red-100 dark:border-red-800/40 rounded-xl p-3 text-sm font-medium mb-6 flex items-center justify-center gap-2" id="expiry-timer" data-expiry="{{ $paymentDetails['expiry_time'] }}">
                        <i class="far fa-clock animate-pulse"></i> <span>Sisa waktu: </span><span id="timer-text" class="font-bold tracking-wider font-mono">Menghitung...</span>
                    </div>
                @endif
            @endif

            <div class="bg-yellow-50 dark:bg-yellow-900/15 border border-yellow-200 dark:border-yellow-700/40 rounded-2xl p-5 mb-6 text-left space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Booking Code</span><span class="font-bold text-yellow-700 dark:text-yellow-500">{{ $booking->booking_code }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Pengantin</span><span class="font-medium text-gray-800 dark:text-white">{{ $booking->groom_name }} & {{ $booking->bride_name }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Tanggal Acara</span><span class="font-medium text-gray-800 dark:text-white">{{ $booking->event_date->isoFormat('D MMMM Y') }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Paket</span><span class="font-medium text-gray-800 dark:text-white">{{ $booking->package->name }}</span></div>
                @if($payment)
                    <div class="flex justify-between text-green-700 dark:text-green-400 font-semibold">
                        <span>{{ $isInvitationOnly ? 'Terbayar' : 'DP Terbayar' }}</span>
                        <span>Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                    </div>
                @endif
            </div>

            <div class="bg-blue-50 dark:bg-blue-900/15 rounded-xl p-4 text-sm text-blue-700 dark:text-blue-300 mb-6 border border-transparent dark:border-blue-800/40">
                <i class="fas fa-envelope mr-1"></i> {{ $isInvitationOnly ? 'Detail pembayaran telah dicatat. Jika Anda memilih transfer manual, tunggu admin memverifikasi.' : 'Detail booking & jadwal konsultasi telah dikirim ke email Anda.' }}
            </div>

            <div class="space-y-3">
                @if(in_array($paymentStatus, ['pending', 'failed']))
                <a href="{{ route('payment.checkout', $booking->id) }}?reset=1"
                   class="block w-full gold-gradient text-white font-bold py-3.5 rounded-xl text-sm hover:shadow-lg transition-all">
                    <i class="fas fa-sync-alt mr-2"></i> Coba Lagi / Ganti Metode
                </a>
                <a href="{{ route('user.booking.show', $booking->id) }}"
                   class="block w-full border border-gray-200 dark:border-white/10 text-gray-500 dark:text-gray-400 font-semibold py-3 rounded-xl text-sm hover:bg-gray-50 dark:hover:bg-white/5 transition-all">
                    Kembali ke Dashboard
                </a>
                @else
                <a href="{{ route('user.booking.show', $booking->id) }}"
                   class="block w-full gold-gradient text-white font-bold py-3.5 rounded-xl text-sm hover:shadow-lg transition-all">
                    <i class="fas fa-home mr-2"></i> Masuk ke Dashboard Klien
                </a>
                @endif

                @if($booking->invitation && $paymentStatus === 'success')
                <a href="{{ route('user.invitation.index', $booking->id) }}"
                   class="block w-full border-2 border-yellow-400 dark:border-yellow-500 text-yellow-600 dark:text-yellow-500 font-semibold py-3.5 rounded-xl text-sm hover:bg-yellow-50 dark:hover:bg-yellow-900/10 transition-all">
                    <i class="fas fa-envelope-open-text mr-2"></i> Buat Undangan Digital
                </a>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function copyText(text, btnElement) {
        navigator.clipboard.writeText(text).then(() => {
            const originalHTML = btnElement.innerHTML;
            btnElement.innerHTML = '<i class="fas fa-check mr-1 text-green-600"></i> Disalin!';
            btnElement.classList.add('bg-green-50', 'dark:bg-green-900/30');
            setTimeout(() => {
                btnElement.innerHTML = originalHTML;
                btnElement.classList.remove('bg-green-50', 'dark:bg-green-900/30');
            }, 2000);
        });
    }

    const timerElement = document.getElementById('expiry-timer');
    if (timerElement) {
        const expiryTime = new Date(timerElement.dataset.expiry.replace(' ', 'T')).getTime();
        const textElement = document.getElementById('timer-text');

        const updateTimer = setInterval(() => {
            const now = new Date().getTime();
            const distance = expiryTime - now;

            if (distance < 0) {
                clearInterval(updateTimer);
                textElement.innerHTML = "Waktu Habis";
                timerElement.classList.replace('text-red-600', 'text-gray-500');
                timerElement.classList.replace('dark:text-red-400', 'dark:text-gray-500');
                timerElement.classList.replace('bg-red-50', 'bg-gray-100');
                timerElement.classList.replace('dark:bg-red-900/20', 'dark:bg-white/5');
                timerElement.classList.replace('border-red-100', 'border-gray-200');
                timerElement.classList.replace('dark:border-red-800/40', 'dark:border-white/10');
            } else {
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                textElement.innerHTML = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            }
        }, 1000);
    }
</script>
@endpush
@endsection
