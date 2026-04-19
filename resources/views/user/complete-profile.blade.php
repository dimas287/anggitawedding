@extends('layouts.app')
@section('title', 'Lengkapi Profil')
@section('page-title', 'Lengkapi Profil Anda')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <div class="text-center mb-6">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center text-2xl">
                <i class="fas fa-user-check"></i>
            </div>
            <h2 class="font-semibold text-gray-900 text-xl mt-4">Lengkapi Informasi Kontak</h2>
            <p class="text-gray-500 text-sm">Kami butuh nomor WhatsApp dan alamat Anda agar tim bisa menghubungi dan menyiapkan dokumen booking.</p>
            @if(session('info'))
                <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-xl text-blue-700 text-sm">
                    {{ session('info') }}
                </div>
            @endif
        </div>

        <form action="{{ route('user.profile.complete.store') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">No. WhatsApp</label>
                <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" required
                       class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400"
                       placeholder="0812xxxxxxxx">
                @error('phone')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat Lengkap</label>
                <textarea name="address" rows="3" required
                          class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400"
                          placeholder="Jalan, Kelurahan, Kota, Provinsi">{{ old('address', $user->address) }}</textarea>
                @error('address')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="w-full py-3.5 rounded-2xl bg-gradient-to-r from-[#F7C977] via-[#F39BC0] to-[#8F82FF] text-white font-semibold text-sm tracking-wide shadow-lg shadow-[#f9c16c]/40">
                Simpan & Lanjutkan
            </button>
        </form>
    </div>
</div>
@endsection
