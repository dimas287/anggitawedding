@extends('layouts.guest')
@section('title', 'Reset Password – Anggita WO')

@section('content')
<div class="min-h-screen hero-gradient flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
        <div class="bg-white dark:bg-[#111111] rounded-3xl shadow-2xl overflow-hidden border border-transparent dark:border-white/5">
            <div class="gold-gradient p-8 text-center">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-lock text-white text-2xl"></i>
                </div>
                <h1 class="font-playfair text-2xl font-bold text-white">Reset Password</h1>
                <p class="text-white/80 text-sm mt-1">Buat password baru untuk akun Anda</p>
            </div>
            <div class="p-8">
                @if(session('status'))
                    <div class="mb-4 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl text-green-700 dark:text-green-400 text-sm">
                        <i class="fas fa-check-circle mr-2"></i>{{ session('status') }}
                    </div>
                @endif
                @if($errors->any())
                    <div class="mb-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-red-600 dark:text-red-400 text-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('password.update') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $email }}">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email</label>
                        <input type="email" value="{{ $email }}" disabled
                               class="w-full border border-gray-200 dark:border-white/10 rounded-xl px-4 py-3 text-sm bg-gray-50 dark:bg-white/5 text-gray-500 dark:text-gray-400">
                    </div>

                    <div x-data="{ show: false }">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Password Baru</label>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'" name="password" required minlength="8"
                                   class="w-full border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 rounded-xl px-4 py-3 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent transition-all pr-10"
                                   placeholder="Minimal 8 karakter">
                            <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i :class="show ? 'fa-eye-slash' : 'fa-eye'" class="fas text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <div x-data="{ show: false }">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Konfirmasi Password</label>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'" name="password_confirmation" required
                                   class="w-full border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 rounded-xl px-4 py-3 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent transition-all pr-10"
                                   placeholder="Ulangi password">
                            <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i :class="show ? 'fa-eye-slash' : 'fa-eye'" class="fas text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="w-full gold-gradient text-white font-semibold py-3.5 rounded-xl hover:shadow-lg transition-all text-sm">
                        <i class="fas fa-save mr-2"></i> Simpan Password Baru
                    </button>
                </form>

                <p class="text-center text-sm text-gray-500 dark:text-gray-400 mt-6">
                    <a href="{{ route('login') }}" class="text-yellow-600 font-semibold hover:underline">← Kembali ke Login</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
