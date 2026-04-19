@extends('layouts.app')
@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="bg-white rounded-2xl shadow-sm p-6">
        <div class="flex items-center gap-5 mb-6 pb-6 border-b">
            <div class="relative">
                @if($user->avatar)
                    <img src="{{ Storage::url($user->avatar) }}" class="w-20 h-20 rounded-full object-cover border-4 border-yellow-200">
                @else
                    <div class="w-20 h-20 rounded-full gold-gradient flex items-center justify-center text-white text-3xl font-bold border-4 border-yellow-200">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
            </div>
            <div>
                <h2 class="font-playfair text-2xl font-bold text-gray-800">{{ $user->name }}</h2>
                <p class="text-gray-500 text-sm">{{ $user->email }}</p>
                <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium">
                    <i class="fas fa-user"></i> Klien
                </span>
            </div>
        </div>

        <h3 class="font-semibold text-gray-800 mb-4">Edit Profil</h3>
        <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf @method('PUT')
            @if($errors->any())
            <div class="p-3 bg-red-50 border border-red-200 rounded-xl text-red-600 text-sm">
                @foreach($errors->all() as $e)<p>• {{ $e }}</p>@endforeach
            </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">No. WhatsApp</label>
                <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}"
                       class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400"
                       placeholder="08xxxxxxxxxx">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat Lengkap</label>
                <textarea name="address" rows="3"
                          class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400"
                          placeholder="Jalan, Kelurahan, Kota, Provinsi">{{ old('address', $user->address) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Foto Profil</label>
                <input type="file" name="avatar" accept="image/*"
                       class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-yellow-50 file:text-yellow-700 hover:file:bg-yellow-100">
            </div>
            <button type="submit" class="gold-gradient text-white font-semibold px-6 py-3 rounded-xl text-sm hover:shadow-lg transition-all">
                <i class="fas fa-save mr-2"></i> Simpan Perubahan
            </button>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Ubah Password</h3>
        <form action="{{ route('user.profile.password') }}" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Password Saat Ini</label>
                <input type="password" name="current_password" required
                       class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Password Baru</label>
                <input type="password" name="password" required minlength="8"
                       class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" required
                       class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
            </div>
            <button type="submit" class="border-2 border-gray-300 text-gray-700 font-semibold px-6 py-3 rounded-xl text-sm hover:border-yellow-400 hover:text-yellow-600 transition-all">
                <i class="fas fa-lock mr-2"></i> Ubah Password
            </button>
        </form>
    </div>
</div>
@endsection
