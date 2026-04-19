@extends('layouts.guest')
@section('title', 'Verifikasi Email – Anggita WO')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-[#0a0a0a] flex items-center justify-center px-4 py-16">
    <div class="w-full max-w-lg">
        <div class="bg-white dark:bg-[#111111] rounded-3xl shadow-xl overflow-hidden border border-transparent dark:border-white/5">
            <div class="gold-gradient p-8 text-center">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-envelope-open-text text-white text-2xl"></i>
                </div>
                <h1 class="font-playfair text-3xl text-white font-bold">Verifikasi Email Anda</h1>
                <p class="text-white/80 mt-2 text-sm">Kami telah mengirim tautan verifikasi ke alamat email Anda.</p>
            </div>
            <div class="p-8 space-y-6">
                @if(session('success'))
                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-xl px-4 py-3 flex items-center gap-2">
                        <i class="fas fa-check-circle"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('info'))
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 text-blue-700 dark:text-blue-400 text-sm rounded-xl px-4 py-3 flex items-center gap-2">
                        <i class="fas fa-info-circle"></i>
                        <span>{{ session('info') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 text-sm rounded-xl px-4 py-3 flex items-center gap-2">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                    Silakan buka email <span class="font-semibold">{{ auth()->user()->email }}</span> dan klik tautan verifikasi
                    untuk mengaktifkan akun Anda. Belum menerima email? Anda dapat meminta ulang tautan di bawah ini.
                </p>

                <form method="POST" action="{{ route('verification.send') }}" class="space-y-4">
                    @csrf
                    <button type="submit" class="w-full gold-gradient text-white font-semibold py-3.5 rounded-xl hover:shadow-lg transition-all text-sm">
                        <i class="fas fa-paper-plane mr-2"></i> Kirim Ulang Email Verifikasi
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}" class="text-center">
                    @csrf
                    <button type="submit" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white underline">
                        Keluar & ganti akun
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
