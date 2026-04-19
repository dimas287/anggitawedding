@extends('layouts.guest')
@section('title', $paymentTitle ?? 'Pembayaran')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-[#0A0A0A] pt-24 pb-16">
    <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8">
            <h1 class="font-playfair text-3xl font-bold text-gray-800 dark:text-white">{{ $paymentTitle ?? 'Pembayaran' }}</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">
                {{ ($isInvitationOnly ?? false) ? 'Selesaikan pembayaran undangan digital agar Anda bisa publish undangan.' : 'Amankan tanggal pernikahan Anda dengan membayar DP' }}
            </p>
        </div>

        <div class="bg-white dark:bg-[#111111] rounded-2xl shadow-sm overflow-hidden mb-6 border border-transparent dark:border-white/10">
            <div class="gold-gradient p-5 text-white dark:!text-gray-900">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-yellow-100 text-xs font-medium uppercase tracking-wider">Booking Code</p>
                        <p class="font-bold text-xl">{{ $booking->booking_code }}</p>
                    </div>
                    <div class="text-right">
                        @if(!($isInvitationOnly ?? false))
                        <p class="text-yellow-100 text-xs">Tanggal Acara</p>
                        <p class="font-semibold">{{ $booking->event_date->isoFormat('D MMMM Y') }}</p>
                        @else
                        <p class="text-yellow-100 text-xs">Jenis Order</p>
                        <p class="font-semibold">Undangan Digital</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="p-6 space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Pengantin</span><span class="font-medium text-gray-900 dark:text-white">{{ $booking->groom_name }} & {{ $booking->bride_name }}</span></div>
                @if(!($isInvitationOnly ?? false))
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Paket</span><span class="font-medium text-gray-900 dark:text-white">{{ $booking->package->name }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Venue</span><span class="font-medium text-gray-900 dark:text-white">{{ $booking->venue }}</span></div>
                @else
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Template</span><span class="font-medium text-gray-900 dark:text-white">{{ optional($booking->invitation->template ?? null)->name ?? 'Belum dipilih' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Kuota Tamu</span><span class="font-medium text-gray-900 dark:text-white">Tidak terbatas</span></div>
                @endif
                <hr class="dark:border-white/10">
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">{{ ($isInvitationOnly ?? false) ? 'Harga Undangan' : 'Harga Paket' }}</span><span class="text-gray-900 dark:text-white">Rp {{ number_format($booking->package_price, 0, ',', '.') }}</span></div>
                <div class="flex justify-between font-bold text-lg text-yellow-600 dark:text-yellow-500">
                    <span>{{ ($isInvitationOnly ?? false) ? 'Total Pembayaran' : 'DP yang Dibayar (30%)' }}</span>
                    <span>Rp {{ number_format($payAmount ?? $booking->dp_amount, 0, ',', '.') }}</span>
                </div>
                @if(!($isInvitationOnly ?? false))
                    <div class="flex justify-between text-gray-400 dark:text-gray-500 text-xs"><span>Sisa Pelunasan</span><span>Rp {{ number_format($booking->package_price - $booking->dp_amount, 0, ',', '.') }}</span></div>
                @endif
            </div>
        </div>

        <div class="bg-white dark:bg-[#111111] rounded-2xl shadow-sm p-6 mb-6 border border-transparent dark:border-white/10">
            <h3 class="font-semibold text-gray-800 dark:text-white mb-4">Metode Pembayaran</h3>

            <div class="grid grid-cols-3 gap-2 text-xs text-center text-gray-500 dark:text-gray-400 mb-4">
                <div class="border dark:border-white/10 rounded-xl p-2 bg-gray-50/50 dark:bg-white/5"><i class="fas fa-university block text-blue-500 mb-1"></i>Transfer Bank</div>
                <div class="border dark:border-white/10 rounded-xl p-2 bg-gray-50/50 dark:bg-white/5"><i class="fas fa-wallet block text-green-500 mb-1"></i>E-Wallet</div>
                <div class="border dark:border-white/10 rounded-xl p-2 bg-gray-50/50 dark:bg-white/5"><i class="fas fa-credit-card block text-purple-500 mb-1"></i>Kartu Kredit</div>
            </div>
            <p class="text-xs text-gray-400 dark:text-gray-500 text-center mb-4">Didukung oleh Midtrans – Platform pembayaran terpercaya Indonesia</p>

            <button id="pay-button" onclick="payNow()"
                    class="w-full gold-gradient text-white dark:!text-gray-900 font-bold py-4 rounded-xl text-sm hover:shadow-lg transition-all flex items-center justify-center gap-2">
                <i class="fas fa-lock"></i>
                {{ ($isInvitationOnly ?? false) ? 'Bayar Undangan Sekarang' : 'Bayar DP Sekarang' }}
                – Rp {{ number_format($payAmount ?? $booking->dp_amount, 0, ',', '.') }}
            </button>

            <div class="mt-6 border-t dark:border-white/10 pt-5">
                <h4 class="font-semibold text-gray-800 dark:text-white text-sm mb-2">Atau upload bukti transfer (manual)</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Unggah bukti pembayaran, lalu tunggu admin memverifikasi. Setelah terverifikasi, Anda bisa publish undangan.</p>
                <form action="{{ route('payment.manual', $booking->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <div>
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-400">Metode</label>
                        <select name="method" class="mt-1 w-full border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 rounded-xl px-3 py-2 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-yellow-400 transition-all">
                            <option value="transfer" class="dark:bg-[#111111]">Transfer</option>
                            <option value="cash" class="dark:bg-[#111111]">Cash</option>
                            <option value="other" class="dark:bg-[#111111]">Lainnya</option>
                        </select>
                        @error('method')
                            <div class="text-xs text-red-600 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-400">Nominal</label>
                        <input type="number" min="1000" name="amount" value="{{ old('amount', $payAmount ?? $booking->dp_amount) }}" class="mt-1 w-full border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 rounded-xl px-3 py-2 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-yellow-400 transition-all" {{ ($isInvitationOnly ?? false) ? 'readonly' : '' }} />
                        @error('amount')
                            <div class="text-xs text-red-600 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-400">Catatan (opsional)</label>
                        <input type="text" name="notes" value="{{ old('notes') }}" class="mt-1 w-full border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 rounded-xl px-3 py-2 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-yellow-400 transition-all" placeholder="Contoh: Transfer BCA a.n. ..." />
                        @error('notes')
                            <div class="text-xs text-red-600 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-400">Bukti Transfer</label>
                        <input type="file" name="proof_attachment" accept="image/*,application/pdf" class="mt-1 w-full text-xs text-gray-600 dark:text-gray-400" required />
                        <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1">Format JPG/PNG/WEBP/PDF • maks 5MB</p>
                        @error('proof_attachment')
                            <div class="text-xs text-red-600 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="w-full border-2 border-yellow-400 dark:border-yellow-500 text-yellow-700 dark:text-yellow-400 font-semibold py-3 rounded-xl text-sm hover:bg-yellow-50 dark:hover:bg-yellow-900/10 transition-all">
                        <i class="fas fa-upload mr-2"></i> Upload Bukti Pembayaran
                    </button>
                </form>
            </div>
        </div>

        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800/40 rounded-xl p-4 text-sm text-green-700 dark:text-green-400 text-center">
            <i class="fas fa-shield-alt mr-1"></i> Pembayaran 100% aman dan terenkripsi melalui Midtrans
        </div>
    </div>
</div>

@push('head')
<script src="{{ config('services.midtrans.snap_url') }}" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
@endpush

@push('scripts')
<script>
async function payNow() {
    const btn = document.getElementById('pay-button');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...';

    try {
        const res = await fetch('{{ route("payment.process", $booking->id) }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
        });
        const data = await res.json();
        if (data.snap_token) {
            window.snap.pay(data.snap_token, {
                onSuccess: () => { window.location.href = '{{ route("payment.success", $booking->id) }}'; },
                onPending: () => { window.location.href = '{{ route("payment.success", $booking->id) }}'; },
                onError: () => {
                    window.AnggitaStatusModal?.show({ type: 'error', message: 'Pembayaran gagal. Silakan coba lagi.' });
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-lock mr-2"></i> {{ ($isInvitationOnly ?? false) ? 'Bayar Undangan Sekarang' : 'Bayar DP Sekarang' }}';
                },
                onClose: () => { btn.disabled = false; btn.innerHTML = '<i class="fas fa-lock mr-2"></i> {{ ($isInvitationOnly ?? false) ? 'Bayar Undangan Sekarang' : 'Bayar DP Sekarang' }}'; }
            });
        } else {
            window.AnggitaStatusModal?.show({ type: 'error', message: data.error || 'Gagal memproses pembayaran' });
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-lock mr-2"></i> {{ ($isInvitationOnly ?? false) ? 'Bayar Undangan Sekarang' : 'Bayar DP Sekarang' }}';
        }
    } catch(e) {
        window.AnggitaStatusModal?.show({ type: 'error', message: 'Terjadi kesalahan. Silakan coba lagi.' });
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-lock mr-2"></i> {{ ($isInvitationOnly ?? false) ? 'Bayar Undangan Sekarang' : 'Bayar DP Sekarang' }}';
    }
}
</script>
@endpush
@endsection
