@extends('layouts.guest')
@section('title', 'Lupa Password – Anggita WO')

@section('content')
<div class="min-h-screen hero-gradient flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
        <div class="bg-white dark:bg-[#111111] rounded-3xl shadow-2xl overflow-hidden border border-transparent dark:border-white/5">
            <div class="gold-gradient p-8 text-center">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-key text-white text-2xl"></i>
                </div>
                <h1 class="font-playfair text-2xl font-bold text-white">Lupa Password?</h1>
                <p class="text-white/80 text-sm mt-1">Masukkan email untuk reset password</p>
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

                <form action="{{ route('password.email') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                               class="w-full border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 rounded-xl px-4 py-3 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent transition-all"
                               placeholder="email@contoh.com">
                    </div>
                    <button type="submit" class="w-full gold-gradient text-white font-semibold py-3.5 rounded-xl hover:shadow-lg transition-all text-sm">
                        <i class="fas fa-paper-plane mr-2"></i> Kirim Link Reset
                    </button>
                </form>

                <p class="text-center text-sm text-gray-500 dark:text-gray-400 mt-6">
                    Ingat password?
                    <a href="{{ route('login') }}" class="text-yellow-600 font-semibold hover:underline">Masuk</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
