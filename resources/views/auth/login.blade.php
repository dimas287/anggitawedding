@extends('layouts.guest')
@section('title', 'Masuk – Anggita WO')

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

<div class="relative bg-[#07041a] px-4 pt-28 pb-20 overflow-hidden">
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute -top-24 -left-24 w-80 h-80 bg-[#f8b86c]/30 blur-[160px]"></div>
        <div class="absolute bottom-0 right-0 w-[28rem] h-[28rem] bg-[#7c5bff]/25 blur-[200px]"></div>
    </div>
    <div class="w-full max-w-xl mx-auto relative z-10">
        <div class="bg-white/95 dark:bg-black/60 backdrop-blur-2xl border border-white/60 dark:border-white/10 rounded-[32px] shadow-[0_20px_70px_rgba(9,4,26,0.35)] p-1">
            <div class="rounded-[30px] bg-white dark:bg-[#111111] p-8">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-white/5 flex items-center justify-center overflow-hidden">
                        <img src="{{ $brandLogoSrc }}" alt="{{ $brandName }}" class="w-12 h-12 object-contain">
                    </div>
                    <div>
                        <p class="font-playfair text-xl font-semibold text-gray-900 dark:text-white">{{ $brandName }}</p>
                        <p class="tracking-[0.35em] text-[10px] uppercase text-amber-500">{{ $brandTagline }}</p>
                    </div>
                </div>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-6 text-cente  r sm:text-left">
                        <div>
                            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">Selamat Datang</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Masuk ke akun Anggita WO Anda</p>
                        </div>
                        <div class="px-4 py-2 rounded-2xl bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-400 text-xs font-semibold w-fit mx-auto sm:mx-0">Secure Access</div>
                </div>

                    @if(session('info'))
                        <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-xl text-blue-700 text-sm flex items-center gap-2">
                            <i class="fas fa-info-circle"></i> {{ session('info') }}
                        </div>
                    @endif
                    @if($errors->any())
                        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-red-600 text-sm">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
                        @csrf
                        <div>
                            <label class="block text-xs uppercase tracking-[0.3em] text-gray-500 mb-2">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                   class="w-full border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 rounded-2xl px-4 py-3 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent transition-all"
                                   placeholder="email@contoh.com">
                        </div>
                        <div x-data="{ show: false }">
                            <label class="block text-xs uppercase tracking-[0.3em] text-gray-500 mb-2">Password</label>
                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" name="password" required
                                       class="w-full border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 rounded-2xl px-4 py-3 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent transition-all pr-12"
                                       placeholder="••••••••">
                                <button type="button" @click="show = !show" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <i :class="show ? 'fa-eye-slash' : 'fa-eye'" class="fas text-sm"></i>
                                </button>
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="remember" class="rounded border-gray-300 text-amber-500 focus:ring-amber-400">
                                Ingat saya
                            </label>
                            <a href="{{ route('password.request') }}" class="text-amber-600 font-semibold hover:underline">Lupa password?</a>
                        </div>
                        <button type="submit" class="w-full py-3.5 rounded-2xl bg-gradient-to-r from-[#F7C977] via-[#F39BC0] to-[#8F82FF] text-white font-semibold text-sm tracking-wide shadow-lg shadow-[#f9c16c]/40 hover:translate-y-0.5 transition-transform">
                            <i class="fas fa-arrow-right-to-bracket mr-2"></i> Masuk ke Dashboard
                        </button>
                    </form>

                    <div class="my-6 flex items-center gap-4">
                        <hr class="flex-1 border-gray-200 dark:border-white/10">
                        <span class="text-xs text-gray-400 tracking-[0.3em] uppercase">atau</span>
                        <hr class="flex-1 border-gray-200 dark:border-white/10">
                    </div>

                    <a href="{{ route('auth.google') }}"
                       class="w-full flex items-center justify-center gap-3 border border-gray-200 dark:border-white/10 rounded-2xl py-3 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5 transition-all">
                        <svg class="w-5 h-5" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                        Masuk dengan Google
                    </a>

                    <p class="text-center text-sm text-gray-500 dark:text-gray-400 mt-6">
                        Belum punya akun?
                        <a href="{{ route('register') }}" class="text-amber-600 font-semibold hover:underline">Daftar sekarang</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="h-16 bg-[#07041a]"></div>
@endsection
