@php
    $layout = Auth::check() && !Auth::user()->isAdmin() ? 'layouts.app' : 'layouts.guest';
    $isApp = $layout === 'layouts.app';
    $isInvitationOnly = $isInvitationOnly ?? false;
@endphp

@extends($layout)

@section('title', $paymentTitle ?? 'Pembayaran')

@section('content')
<div class="{{ $isApp ? 'py-8' : 'min-h-screen pt-28 pb-16' }} dark:bg-[#0A0A0A]">
    <div class="max-w-xl mx-auto px-4">
        {{-- Header Section --}}
        <div class="text-center mb-10">
            <div class="flex items-center justify-center mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-2xl bg-green-500 text-white flex items-center justify-center shadow-lg font-bold">
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="w-12 h-px bg-green-500"></div>
                    <div class="w-10 h-10 rounded-2xl bg-green-500 text-white flex items-center justify-center shadow-lg font-bold">
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="w-12 h-px bg-green-500"></div>
                    <div class="w-10 h-10 rounded-2xl gold-gradient text-white flex items-center justify-center shadow-lg font-bold">3</div>
                </div>
            </div>
            
            <a href="javascript:history.back()" class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-gray-100 dark:bg-white/5 text-gray-500 dark:text-gray-400 text-[10px] font-bold uppercase tracking-widest hover:text-gray-900 dark:hover:text-white transition-all mb-4">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            
            <h1 class="font-playfair text-4xl font-bold text-gray-900 dark:text-white">{{ $paymentTitle ?? 'Pembayaran' }}</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2 text-sm">
                {{ $isInvitationOnly ? 'Selesaikan pembayaran undangan digital Anda.' : 'Satu langkah terakhir untuk mengamankan tanggal spesial Anda.' }}
            </p>
        </div>

        {{-- Booking Summary Card (Premium Style) --}}
        <div class="bg-white dark:bg-white/5 rounded-[40px] border border-gray-100 dark:border-white/10 shadow-2xl overflow-hidden mb-8 relative">
            {{-- Decorative Accent --}}
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-yellow-400 via-amber-500 to-yellow-600"></div>
            
            <div class="p-8 pb-4">
                <div class="flex flex-col items-center text-center mb-8">
                    <span class="text-[10px] font-black uppercase tracking-[0.3em] text-yellow-600 dark:text-yellow-500 mb-2">Booking Code</span>
                    <div class="px-6 py-3 rounded-2xl bg-gray-50 dark:bg-black/20 border border-gray-100 dark:border-white/5 shadow-inner">
                        <span class="font-playfair text-2xl font-bold text-gray-900 dark:text-white tracking-widest">{{ $booking->booking_code }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-8 mb-8">
                    <div class="space-y-1">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Pesan Atas Nama</p>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $booking->groom_name }} & {{ $booking->bride_name }}</p>
                    </div>
                    <div class="space-y-1 text-right">
                        @if(!$isInvitationOnly)
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Tanggal Acara</p>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $booking->event_date->isoFormat('D MMMM Y') }}</p>
                        @else
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Jenis Order</p>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">Undangan Digital</p>
                        @endif
                    </div>
                </div>

                <div class="space-y-4 border-t border-gray-100 dark:border-white/5 pt-6">
                    @if(!$isInvitationOnly)
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Paket Layanan</span>
                        <span class="font-bold text-gray-900 dark:text-white">{{ $booking->package->name }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Venue</span>
                        <span class="font-bold text-gray-900 dark:text-white">{{ $booking->venue }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Total Harga Paket</span>
                        <span class="font-bold text-gray-900 dark:text-white">Rp {{ number_format($booking->package_price, 0, ',', '.') }}</span>
                    </div>
                    @else
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Template</span>
                        <span class="font-bold text-gray-900 dark:text-white">{{ optional($booking->invitation->template ?? null)->name ?? 'Premium Template' }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Total Harga</span>
                        <span class="font-bold text-gray-900 dark:text-white">Rp {{ number_format($booking->package_price, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    
                    <div class="p-5 rounded-3xl bg-yellow-50 dark:bg-yellow-900/10 border border-yellow-100 dark:border-yellow-800/30 mt-6">
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold text-yellow-700 dark:text-yellow-500 uppercase tracking-widest">
                                {{ $isInvitationOnly ? 'Total Bayar' : 'DP yang Dibayar (30%)' }}
                            </span>
                            <span class="text-xl font-black text-yellow-800 dark:text-yellow-400">
                                Rp {{ number_format($payAmount ?? $booking->dp_amount, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Payment Methods --}}
            <div class="p-8 pt-4">
                <div class="bg-gray-50 dark:bg-black/10 rounded-[32px] p-6 border border-gray-100 dark:border-white/5">
                    <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-widest mb-4 flex items-center gap-2">
                        <i class="fas fa-credit-card text-yellow-600"></i> Pilih Pembayaran
                    </h4>
                    
                    <div class="grid grid-cols-3 gap-3 mb-6">
                        <div class="flex flex-col items-center gap-2 p-3 rounded-2xl bg-white dark:bg-white/5 border border-gray-100 dark:border-white/10">
                            <i class="fas fa-university text-blue-500"></i>
                            <span class="text-[9px] font-bold uppercase tracking-widest text-gray-400">Transfer</span>
                        </div>
                        <div class="flex flex-col items-center gap-2 p-3 rounded-2xl bg-white dark:bg-white/5 border border-gray-100 dark:border-white/10">
                            <i class="fas fa-wallet text-green-500"></i>
                            <span class="text-[9px] font-bold uppercase tracking-widest text-gray-400">E-Wallet</span>
                        </div>
                        <div class="flex flex-col items-center gap-2 p-3 rounded-2xl bg-white dark:bg-white/5 border border-gray-100 dark:border-white/10">
                            <i class="fas fa-bolt text-purple-500"></i>
                            <span class="text-[9px] font-bold uppercase tracking-widest text-gray-400">QRIS</span>
                        </div>
                    </div>

                    <button id="pay-button" onclick="payNow()"
                            class="w-full gold-gradient text-white font-bold py-5 rounded-2xl text-sm hover:shadow-2xl hover:scale-[1.02] active:scale-[0.98] transition-all duration-300 flex items-center justify-center gap-3">
                        <i class="fas fa-lock"></i>
                        <span>{{ $isInvitationOnly ? 'Bayar Undangan Sekarang' : 'Bayar DP Sekarang' }}</span>
                    </button>
                    
                    <p class="text-[10px] text-gray-400 dark:text-gray-500 text-center mt-4">
                        <i class="fas fa-lock mr-1"></i> Pembayaran Aman via Midtrans
                    </p>
                </div>
            </div>
        </div>

        {{-- Manual Upload (Optional) --}}
        <div x-data="{ open: false }" class="bg-white dark:bg-white/5 rounded-[32px] border border-gray-100 dark:border-white/10 p-6 shadow-xl">
            <button @click="open = !open" class="w-full flex items-center justify-between text-left">
                <div>
                    <h4 class="text-sm font-bold text-gray-900 dark:text-white">Metode Manual?</h4>
                    <p class="text-[10px] text-gray-500 dark:text-gray-400">Upload bukti transfer secara manual jika Snap gagal.</p>
                </div>
                <i class="fas fa-chevron-down text-gray-400 transition-transform duration-300" :class="open ? 'rotate-180' : ''"></i>
            </button>
            
            <div x-show="open" x-collapse x-cloak class="mt-6 pt-6 border-t border-gray-100 dark:border-white/5">
                <form action="{{ route('payment.manual', $booking->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest ml-1">Unggah Bukti Transfer</label>
                        <input type="file" name="proof_attachment" required
                               class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-bold file:uppercase file:bg-yellow-50 file:text-yellow-700 hover:file:bg-yellow-100 dark:file:bg-white/5 dark:file:text-yellow-500 transition-all" />
                    </div>
                    <button type="submit" class="w-full py-4 rounded-2xl border-2 border-yellow-400 dark:border-yellow-500 text-yellow-600 dark:text-yellow-500 font-bold text-sm hover:bg-yellow-400 hover:text-white dark:hover:bg-yellow-500 dark:hover:text-gray-900 transition-all">
                        Upload Bukti Manual
                    </button>
                </form>
            </div>
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
    const originalContent = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...';

    try {
        const res = await fetch('{{ route("payment.process", $booking->id) }}', {
            method: 'POST',
            headers: { 
                'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });
        const data = await res.json();
        
        if (data.snap_token) {
            window.snap.pay(data.snap_token, {
                onSuccess: (result) => { window.location.href = '{{ route("payment.success", $booking->id) }}?order_id=' + result.order_id; },
                onPending: (result) => { window.location.href = '{{ route("payment.success", $booking->id) }}?order_id=' + result.order_id; },
                onError: () => {
                    alert('Pembayaran gagal. Silakan coba lagi.');
                    btn.disabled = false;
                    btn.innerHTML = originalContent;
                },
                onClose: () => { 
                    btn.disabled = false; 
                    btn.innerHTML = originalContent; 
                }
            });
        } else {
            alert(data.error || 'Gagal memproses pembayaran. Token tidak valid.');
            btn.disabled = false;
            btn.innerHTML = originalContent;
        }
    } catch(e) {
        console.error(e);
        alert('Terjadi kesalahan teknis. Silakan coba lagi.');
        btn.disabled = false;
        btn.innerHTML = originalContent;
    }
}
</script>
@endpush
@endsection
