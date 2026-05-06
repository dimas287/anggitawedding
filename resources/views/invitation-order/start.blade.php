@php
    $layout = Auth::check() && !Auth::user()->isAdmin() ? 'layouts.app' : 'layouts.guest';
    $isApp = $layout === 'layouts.app';
@endphp

@extends($layout)

@section('title', 'Checkout Undangan Digital')

@section('content')
<div class="{{ $isApp ? 'py-8' : 'min-h-screen pt-28 pb-16' }} dark:bg-[#0A0A0A]">
    <div class="max-w-6xl mx-auto px-4">
        {{-- Header Section --}}
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-3xl bg-yellow-50 dark:bg-yellow-900/20 text-yellow-600 dark:text-yellow-500 mb-6 shadow-xl shadow-yellow-400/10">
                <i class="fas fa-envelope-open-text text-2xl"></i>
            </div>
            
            <a href="{{ route('booking.start') }}" class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-gray-100 dark:bg-white/5 text-gray-500 dark:text-gray-400 text-[10px] font-bold uppercase tracking-widest hover:text-gray-900 dark:hover:text-white transition-all mb-4">
                <i class="fas fa-arrow-left"></i> Kembali Pilih Layanan
            </a>
            
            <h1 class="font-playfair text-4xl md:text-5xl font-bold text-gray-900 dark:text-white">Undangan Digital Saja</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-4 text-sm md:text-base leading-relaxed max-w-2xl mx-auto">
                Pilih template favorit Anda, buat draft, dan lengkapi data undangan. Publish undangan Anda setelah pembayaran dikonfirmasi.
            </p>
        </div>

        <div class="flex flex-col lg:flex-row gap-10 items-start">
            {{-- Left Side: Info --}}
            <div class="flex-1 space-y-8 order-2 lg:order-1">
                <div class="bg-white dark:bg-white/5 rounded-[40px] border border-gray-100 dark:border-white/10 shadow-2xl p-8">
                    <h3 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-widest mb-6 flex items-center gap-2">
                        <span class="w-1.5 h-4 bg-yellow-400 rounded-full"></span> Alur Pemesanan
                    </h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <div class="space-y-3">
                            <div class="w-10 h-10 rounded-2xl bg-yellow-50 dark:bg-yellow-900/20 text-yellow-600 dark:text-yellow-500 flex items-center justify-center font-bold text-sm shadow-sm">1</div>
                            <div class="font-bold text-sm text-gray-900 dark:text-white">Pilih & Checkout</div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">Pilih template dan buat draft undangan digital Anda.</p>
                        </div>
                        <div class="space-y-3">
                            <div class="w-10 h-10 rounded-2xl bg-yellow-50 dark:bg-yellow-900/20 text-yellow-600 dark:text-yellow-500 flex items-center justify-center font-bold text-sm shadow-sm">2</div>
                            <div class="font-bold text-sm text-gray-900 dark:text-white">Lengkapi Data</div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">Isi nama, tanggal, lokasi, hingga upload foto & video.</p>
                        </div>
                        <div class="space-y-3">
                            <div class="w-10 h-10 rounded-2xl bg-yellow-50 dark:bg-yellow-900/20 text-yellow-600 dark:text-yellow-500 flex items-center justify-center font-bold text-sm shadow-sm">3</div>
                            <div class="font-bold text-sm text-gray-900 dark:text-white">Bayar & Publish</div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">Selesaikan pembayaran untuk mempublikasikan undangan.</p>
                        </div>
                    </div>
                </div>

                {{-- Template Catalog Preview --}}
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <h3 class="font-playfair text-2xl font-bold text-gray-900 dark:text-white">Pilih Template</h3>
                        <a href="{{ route('digital-invitations') }}" class="text-xs font-bold text-yellow-600 dark:text-yellow-500 uppercase tracking-widest hover:underline">Semua Katalog <i class="fas fa-arrow-right ml-1"></i></a>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        @foreach($templates->take(4) as $template)
                        <div class="bg-white dark:bg-white/5 rounded-[32px] border border-gray-100 dark:border-white/10 overflow-hidden shadow-xl group">
                            <div class="aspect-[4/3] bg-gray-100 dark:bg-black/20 relative overflow-hidden">
                                @if($template->image)
                                    <img src="{{ asset('storage/'.$template->image) }}" alt="{{ $template->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-300">
                                        <i class="fas fa-image text-4xl"></i>
                                    </div>
                                @endif
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-6">
                                    <a href="{{ $template->demo_url ?? '#' }}" target="_blank" class="w-full py-2.5 rounded-xl bg-white text-gray-900 font-bold text-xs text-center shadow-lg">Preview Template</a>
                                </div>
                            </div>
                            <div class="p-6">
                                <div class="flex justify-between items-start gap-2 mb-4">
                                    <h4 class="font-bold text-gray-900 dark:text-white">{{ $template->name }}</h4>
                                    <span class="text-sm font-black text-yellow-600 dark:text-yellow-500">Rp {{ number_format($template->effective_price, 0, ',', '.') }}</span>
                                </div>
                                <a href="{{ route('invitation-order.start', ['template' => $template->id]) }}" 
                                   class="block w-full py-3 rounded-2xl {{ (string)$selectedTemplateId === (string)$template->id ? 'gold-gradient text-white shadow-xl shadow-yellow-500/20' : 'bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-white/10' }} text-center text-xs font-bold transition-all">
                                    {{ (string)$selectedTemplateId === (string)$template->id ? 'Terpilih' : 'Pilih Template Ini' }}
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Right Side: Form --}}
            <div class="w-full lg:w-[400px] order-1 lg:order-2 sticky top-24">
                <div class="bg-white dark:bg-white/5 rounded-[40px] border border-gray-100 dark:border-white/10 shadow-2xl overflow-hidden relative">
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-yellow-400 via-amber-500 to-yellow-600"></div>
                    
                    <div class="p-8 border-b border-gray-50 dark:border-white/5 text-center">
                        <div class="flex items-center justify-center gap-4 mb-6">
                            <div class="w-10 h-10 rounded-2xl gold-gradient text-white flex items-center justify-center shadow-lg font-bold">1</div>
                            <div class="w-12 h-px bg-gray-200 dark:bg-white/10"></div>
                            <div class="w-10 h-10 rounded-2xl border border-gray-100 dark:border-white/10 bg-gray-50 dark:bg-white/5 text-gray-400 flex items-center justify-center font-bold">2</div>
                        </div>
                        <h2 class="font-playfair text-2xl font-bold text-gray-900 dark:text-white">Ringkasan Checkout</h2>
                        <p class="text-gray-500 dark:text-gray-400 text-sm mt-2">Lengkapi data awal untuk draft undangan.</p>
                    </div>

                    <form method="POST" action="{{ route('invitation-order.checkout') }}" class="p-8 space-y-6">
                        @csrf
                        
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest ml-1">Template Undangan <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <i class="fas fa-palette absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                                <select name="template_id" required
                                        class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-2xl pl-12 pr-5 py-4 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-yellow-400/20 transition-all appearance-none">
                                    <option value="" class="dark:bg-[#151515]">-- Pilih Template --</option>
                                    @foreach($templates as $template)
                                        <option value="{{ $template->id }}" class="dark:bg-[#151515]" @selected((string)old('template_id', $selectedTemplateId) === (string)$template->id)>
                                            {{ $template->name }} (Rp {{ number_format($template->effective_price, 0, ',', '.') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('template_id')<p class="text-[10px] text-red-500 mt-1 ml-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest ml-1">Nama Pria <span class="text-red-500">*</span></label>
                            <input type="text" name="groom_name" value="{{ old('groom_name') }}" required
                                   class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-2xl px-5 py-4 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-yellow-400/20 transition-all"
                                   placeholder="Contoh: Dimas">
                            @error('groom_name')<p class="text-[10px] text-red-500 mt-1 ml-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest ml-1">Nama Wanita <span class="text-red-500">*</span></label>
                            <input type="text" name="bride_name" value="{{ old('bride_name') }}" required
                                   class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-2xl px-5 py-4 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-yellow-400/20 transition-all"
                                   placeholder="Contoh: Anggita">
                            @error('bride_name')<p class="text-[10px] text-red-500 mt-1 ml-1">{{ $message }}</p>@enderror
                        </div>

                        <button type="submit" class="w-full gold-gradient text-white font-bold py-5 rounded-2xl text-sm hover:shadow-2xl hover:scale-[1.02] active:scale-[0.98] transition-all duration-300 shadow-lg shadow-yellow-500/20">
                            Checkout & Buat Draft <i class="fas fa-arrow-right ml-2"></i>
                        </button>

                        @guest
                        <div class="bg-blue-50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-800/30 rounded-2xl p-4 text-center">
                            <p class="text-[10px] text-blue-700 dark:text-blue-400 leading-relaxed">
                                <i class="fas fa-info-circle mr-1"></i> Anda akan diminta login/register untuk menyimpan draft ke dashboard.
                            </p>
                        </div>
                        @else
                        <div class="bg-green-50 dark:bg-green-900/10 border border-green-100 dark:border-green-800/30 rounded-2xl p-4 text-center">
                            <p class="text-[10px] text-green-700 dark:text-green-400 leading-relaxed">
                                <i class="fas fa-check-circle mr-1"></i> Draft akan disimpan di dashboard Anda.
                            </p>
                        </div>
                        @endguest
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@endsection
