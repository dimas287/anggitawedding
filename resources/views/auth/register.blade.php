@extends('layouts.guest')
@section('title', 'Daftar – Anggita WO')

@section('content')
@php
    $defaultBrand = [
        'brand_name' => 'Anggita Wedding Organizer',
        'tagline' => 'Make Up & Wedding Service',
        'logo_main' => null,
    ];
    $brand = \App\Models\SiteSetting::getJson('brand_assets', $defaultBrand);
    $brandName = $brand['brand_name'];
    $brandTagline = $brand['tagline'];
    $brandLogoPath = $brand['logo_main'] ?? null;
    $brandLogoSrc = $brandLogoPath ? \Illuminate\Support\Facades\Storage::url($brandLogoPath) : asset('images/brand/anggita-logo-main.svg');
@endphp

<div class="relative bg-[#07041a] px-4 pt-24 pb-10 md:pb-16 overflow-hidden">
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute -top-24 -left-24 w-80 h-80 bg-[#f8b86c]/30 blur-[160px]"></div>
        <div class="absolute bottom-0 right-0 w-[28rem] h-[28rem] bg-[#7c5bff]/25 blur-[200px]"></div>
    </div>
    <div class="w-full max-w-xl mx-auto relative z-10">
        <div class="bg-white/95 dark:bg-black/60 backdrop-blur-2xl border border-white/60 dark:border-white/10 rounded-[32px] shadow-[0_20px_70px_rgba(9,4,26,0.35)] p-1">
            <div class="rounded-[30px] bg-white dark:bg-[#111111] p-8 pb-14">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-white/5 flex items-center justify-center overflow-hidden">
                        <img src="{{ $brandLogoSrc }}" alt="{{ $brandName }}" class="w-12 h-12 object-contain">
                    </div>
                    <div>
                        <p class="font-playfair text-xl font-semibold text-gray-900 dark:text-white">{{ $brandName }}</p>
                        <p class="tracking-[0.35em] text-[10px] uppercase text-amber-500">{{ $brandTagline }}</p>
                    </div>
                </div>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-6 text-center sm:text-left">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Buat Akun Baru</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Mulai perjalanan pernikahan impian Anda</p>
                    </div>
                    <div class="px-4 py-2 rounded-2xl bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-400 text-xs font-semibold w-fit mx-auto sm:mx-0">Secure Access</div>
                </div>
            <div class="space-y-0">
                @if($errors->any())
                    <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-red-600 text-sm">
                        <ul class="space-y-1">@foreach($errors->all() as $err)<li>• {{ $err }}</li>@endforeach</ul>
                    </div>
                @endif

                <form action="{{ route('register.post') }}" method="POST" class="space-y-4">
                    @csrf
                    {{-- Honeypot Anti-Spam --}}
                    <input type="text" name="hp_field" style="display:none !important" tabindex="-1" autocomplete="off">
                    <div>
                        <label class="block text-xs uppercase tracking-[0.3em] text-gray-500 mb-2">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 transition-all"
                               placeholder="Nama lengkap Anda">
                    </div>
                    <div>
                        <label class="block text-xs uppercase tracking-[0.3em] text-gray-500 mb-2">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 transition-all"
                               placeholder="email@contoh.com">
                    </div>
                    <div>
                        <label class="block text-xs uppercase tracking-[0.3em] text-gray-500 mb-2">No. WhatsApp</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}"
                               class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 transition-all"
                               placeholder="0812xxxxxxxx">
                    </div>
                    <div>
                        <label class="block text-xs uppercase tracking-[0.3em] text-gray-500 mb-2">Alamat Lengkap</label>
                        <textarea name="address" rows="3" required
                                  class="w-full border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 rounded-2xl px-4 py-3 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-400 transition-all"
                                  placeholder="Jalan, Kelurahan, Kota, Provinsi">{{ old('address') }}</textarea>
                    </div>
                    <div x-data="{
                            show: false,
                            value: '',
                            get score() {
                                let s = 0;
                                if (this.value.length >= 8) s++;
                                if (/[A-Z]/.test(this.value)) s++;
                                if (/[0-9]/.test(this.value)) s++;
                                if (/[^A-Za-z0-9]/.test(this.value)) s++;
                                return s;
                            },
                            get label() {
                                return ['Sangat Lemah','Lemah','Sedang','Kuat','Sangat Kuat'][this.score] ?? 'Sangat Lemah';
                            }
                        }">
                        <label class="block text-xs uppercase tracking-[0.3em] text-gray-500 mb-2">Password</label>
                        <div class="relative">
                            <input x-model="value" :type="show ? 'text' : 'password'" name="password" required minlength="8"
                                   class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 transition-all pr-12"
                                   placeholder="Minimal 8 karakter">
                            <button type="button" @click="show = !show" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i :class="show ? 'fa-eye-slash' : 'fa-eye'" class="fas text-sm"></i>
                            </button>
                        </div>
                        <div class="mt-3">
                            <div class="flex gap-1">
                                <template x-for="bar in 4" :key="bar">
                                    <div class="h-1 flex-1 rounded-full"
                                         :class="score >= bar ? ['bg-red-400','bg-orange-400','bg-lime-400','bg-emerald-500'][bar-1] : 'bg-gray-200'"></div>
                                </template>
                            </div>
                            <div class="flex items-center justify-between text-[11px] text-gray-500 mt-2">
                                <span :class="score >= 3 ? 'text-emerald-600 font-semibold' : 'text-gray-500'">Kekuatan: <span x-text="label"></span></span>
                                <span class="tracking-[0.2em] uppercase text-gray-400">Min. 8 char</span>
                            </div>
                            <ul class="mt-2 space-y-1 text-xs text-gray-500">
                                <li class="flex items-center gap-2" :class="value.length >= 8 ? 'text-emerald-600' : ''">
                                    <i class="fas" :class="value.length >= 8 ? 'fa-check-circle text-emerald-500' : 'fa-circle text-gray-300'"></i>
                                    Minimal 8 karakter
                                </li>
                                <li class="flex items-center gap-2" :class="/[A-Z]/.test(value) ? 'text-emerald-600' : ''">
                                    <i class="fas" :class="/[A-Z]/.test(value) ? 'fa-check-circle text-emerald-500' : 'fa-circle text-gray-300'"></i>
                                    Ada huruf kapital
                                </li>
                                <li class="flex items-center gap-2" :class="/[0-9]/.test(value) ? 'text-emerald-600' : ''">
                                    <i class="fas" :class="/[0-9]/.test(value) ? 'fa-check-circle text-emerald-500' : 'fa-circle text-gray-300'"></i>
                                    Ada angka
                                </li>
                                <li class="flex items-center gap-2" :class="/[^A-Za-z0-9]/.test(value) ? 'text-emerald-600' : ''">
                                    <i class="fas" :class="/[^A-Za-z0-9]/.test(value) ? 'fa-check-circle text-emerald-500' : 'fa-circle text-gray-300'"></i>
                                    Ada simbol (@,#,&, dll)
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs uppercase tracking-[0.3em] text-gray-500 mb-2">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" required
                               class="w-full border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 rounded-2xl px-4 py-3 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-400 transition-all"
                               placeholder="Ulangi password">
                    </div>
                    <button type="submit" class="w-full py-3.5 rounded-2xl bg-gradient-to-r from-[#F7C977] via-[#F39BC0] to-[#8F82FF] text-white font-semibold text-sm tracking-wide shadow-lg shadow-[#f9c16c]/40 hover:translate-y-0.5 transition-transform mb-6">
                        <i class="fas fa-user-plus mr-2"></i> Buat Akun
                    </button>
                </form>

                <div class="flex flex-col items-stretch gap-5 mt-8">
                    <div class="flex items-center gap-3 pt-1">
                        <hr class="flex-1 border-gray-200">
                        <span class="text-xs text-gray-400 tracking-[0.2em]">atau daftar dengan</span>
                        <hr class="flex-1 border-gray-200">
                    </div>

                    <a href="{{ route('auth.google') }}"
                       class="w-full flex items-center justify-center gap-3 border border-gray-200 dark:border-white/10 rounded-xl py-3 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5 transition-all">
                        <svg class="w-5 h-5" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                        Daftar dengan Google
                    </a>

                    <p class="text-center text-sm text-gray-500 dark:text-gray-400">
                        Sudah punya akun? <a href="{{ route('login') }}" class="text-yellow-600 font-semibold hover:underline">Masuk di sini</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
