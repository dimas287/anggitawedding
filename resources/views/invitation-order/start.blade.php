@extends('layouts.guest')
@section('title', 'Checkout Undangan Digital')

@section('content')
<div class="pt-28 bg-[radial-gradient(circle_at_top,_rgba(251,191,36,0.18),_transparent_55%),linear-gradient(135deg,_#16001f_0%,_#3a0f55_40%,_#6a1b8c_75%,_#f5f1ff_100%)] text-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <div class="flex flex-col lg:flex-row gap-12 items-start">
            <div class="flex-1">
                <span class="text-yellow-200 text-[11px] font-semibold uppercase tracking-[0.6em]">Checkout</span>
                <h1 class="font-playfair text-4xl md:text-6xl font-semibold mt-4 leading-tight">Undangan Digital Saja</h1>
                <p class="text-white/80 mt-4 text-base md:text-lg leading-relaxed max-w-xl">Pilih template, buat undangan <span class="font-semibold text-white">draft</span> di dashboard, lalu lengkapi data & upload file. Anda baru bisa <span class="font-semibold text-white">Publish</span> setelah pembayaran selesai.</p>

                <div class="mt-8 bg-white/10 border border-white/15 rounded-3xl p-6 backdrop-blur">
                    <h3 class="font-semibold text-white text-sm tracking-wide uppercase">Alur singkat</h3>
                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm text-white/85">
                        <div class="bg-gradient-to-br from-white/15 to-white/5 rounded-2xl p-4 border border-white/10">
                            <div class="text-yellow-200 text-[10px] font-semibold tracking-[0.3em] uppercase">Step 1</div>
                            <div class="font-semibold mt-2">Checkout</div>
                            <div class="text-white/70 text-xs mt-1">Buat undangan draft</div>
                        </div>
                        <div class="bg-gradient-to-br from-white/15 to-white/5 rounded-2xl p-4 border border-white/10">
                            <div class="text-yellow-200 text-[10px] font-semibold tracking-[0.3em] uppercase">Step 2</div>
                            <div class="font-semibold mt-2">Isi Data</div>
                            <div class="text-white/70 text-xs mt-1">Teks, foto, video, musik</div>
                        </div>
                        <div class="bg-gradient-to-br from-white/15 to-white/5 rounded-2xl p-4 border border-white/10">
                            <div class="text-yellow-200 text-[10px] font-semibold tracking-[0.3em] uppercase">Step 3</div>
                            <div class="font-semibold mt-2">Bayar & Publish</div>
                            <div class="text-white/70 text-xs mt-1">Midtrans / manual verifikasi</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-full lg:w-[420px]">
                <div class="bg-white rounded-[28px] border border-white/40 shadow-[0_30px_80px_rgba(16,0,24,0.35)] overflow-hidden text-gray-900">
                    <div class="p-6 border-b border-gray-100">
                        <h2 class="font-semibold text-lg">Ringkasan Checkout</h2>
                        <p class="text-gray-500 text-sm mt-1">Pilih template, lalu klik Checkout.</p>
                    </div>

                    <form method="POST" action="{{ route('invitation-order.checkout') }}" class="p-6 space-y-4">
                        @csrf

                        <div>
                            <label class="text-xs font-semibold text-gray-700 uppercase tracking-[0.2em]">Template Undangan</label>
                            <select name="template_id" class="mt-2 w-full rounded-2xl border-gray-200 focus:border-yellow-400 focus:ring-yellow-400 bg-gray-50/60">
                                <option value="">-- Pilih template --</option>
                                @foreach($templates as $template)
                                    <option value="{{ $template->id }}" @selected((string)old('template_id', $selectedTemplateId) === (string)$template->id)>
                                        {{ $template->name }}
                                        @if($template->effective_price)
                                            - Rp {{ number_format($template->effective_price, 0, ',', '.') }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('template_id')
                                <div class="text-xs text-red-600 mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="text-xs font-semibold text-gray-700 uppercase tracking-[0.2em]">Nama Pria</label>
                                <input type="text" name="groom_name" value="{{ old('groom_name') }}" class="mt-2 w-full rounded-2xl border-gray-200 focus:border-yellow-400 focus:ring-yellow-400 bg-gray-50/60" placeholder="Contoh: Andi" />
                                @error('groom_name')
                                    <div class="text-xs text-red-600 mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-700 uppercase tracking-[0.2em]">Nama Wanita</label>
                                <input type="text" name="bride_name" value="{{ old('bride_name') }}" class="mt-2 w-full rounded-2xl border-gray-200 focus:border-yellow-400 focus:ring-yellow-400 bg-gray-50/60" placeholder="Contoh: Sari" />
                                @error('bride_name')
                                    <div class="text-xs text-red-600 mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="w-full bg-gray-900 text-white font-semibold py-4 rounded-2xl text-sm hover:shadow-lg transition-all flex items-center justify-center gap-2">
                                <i class="fas fa-bag-shopping"></i> Checkout & Buat Draft
                            </button>
                        </div>

                        <p class="text-[11px] text-gray-400 leading-relaxed">
                            Dengan melanjutkan, Anda akan diarahkan ke dashboard untuk melengkapi undangan. Status publish akan terkunci sampai pembayaran selesai.
                        </p>
                    </form>
                </div>

                <div class="mt-4 bg-white/10 border border-white/20 rounded-3xl p-5 text-white/85 text-xs">
                    <div class="font-semibold text-white">Sudah punya akun?</div>
                    <div class="mt-1">Jika belum login, Anda akan diminta login terlebih dulu.</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-white py-16">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <h3 class="font-playfair text-2xl md:text-3xl font-bold text-gray-900">Lihat Semua Template</h3>
            <a href="{{ route('digital-invitations') }}" class="text-sm font-semibold text-yellow-700 hover:text-yellow-800">Kembali ke katalog</a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-8">
            @foreach($templates as $template)
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="h-40 bg-gray-100">
                    @if($template->image)
                        <img src="{{ asset('storage/'.$template->image) }}" alt="{{ $template->name }}" class="w-full h-full object-cover" />
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                            <i class="fas fa-image"></i>
                        </div>
                    @endif
                </div>
                <div class="p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h4 class="font-semibold text-gray-900">{{ $template->name }}</h4>
                            @if($template->promo_label && $template->has_active_promo)
                                <div class="text-xs text-purple-700 font-semibold mt-1">{{ $template->promo_label }}</div>
                            @endif
                        </div>
                        <div class="text-right">
                            @if($template->effective_price)
                                <div class="font-bold text-yellow-700">Rp {{ number_format($template->effective_price, 0, ',', '.') }}</div>
                                @if($template->has_active_promo)
                                    <div class="text-[11px] text-gray-400 line-through">Rp {{ number_format($template->price, 0, ',', '.') }}</div>
                                @endif
                            @else
                                <div class="text-[11px] text-gray-400">Harga belum diatur</div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-2 gap-3">
                        @php $demoUrl = $template->demo_url; @endphp
                        <a href="{{ $demoUrl ?? '#' }}" target="{{ $demoUrl ? '_blank' : '_self' }}"
                           class="text-sm font-semibold px-4 py-2 rounded-full border {{ $demoUrl ? 'border-gray-200 text-gray-700 hover:border-yellow-400 hover:text-yellow-600' : 'border-dashed border-gray-300 text-gray-400 cursor-not-allowed' }} transition-colors flex items-center justify-center gap-2"
                           {{ $demoUrl ? '' : 'aria-disabled=true' }}>
                            <i class="fas fa-eye"></i> Preview
                        </a>
                        <a href="{{ route('invitation-order.start', ['template' => $template->id]) }}" class="text-sm font-semibold px-4 py-2 rounded-full gold-gradient text-white shadow hover:shadow-lg transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-check"></i> Pilih
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
