@extends('layouts.admin')
@section('title', 'Pengaturan Akun')
@section('page-title', 'Pengaturan Akun')
@section('breadcrumb', 'Dashboard / Pengaturan Akun')

@section('content')
<div class="max-w-3xl space-y-6">
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800">Profil Admin</h3>
        <p class="text-sm text-gray-500 mt-1">Gunakan email ini untuk menerima link verifikasi perubahan password.</p>
        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div class="bg-gray-50 rounded-xl p-4">
                <div class="text-xs uppercase tracking-widest text-gray-400">Nama</div>
                <div class="text-gray-800 font-semibold mt-1">{{ $user->name }}</div>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
                <div class="text-xs uppercase tracking-widest text-gray-400">Email</div>
                <div class="text-gray-800 font-semibold mt-1">{{ $user->email }}</div>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.account.password.request') }}" method="POST" class="bg-white rounded-2xl shadow-sm p-6 space-y-4">
        @csrf
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Ubah Password Admin</h3>
                <p class="text-sm text-gray-500">Kami akan mengirim link verifikasi ke email admin untuk konfirmasi.</p>
            </div>
            <button type="submit" class="px-5 py-2.5 rounded-xl gold-gradient text-white text-sm font-semibold">Kirim Link Verifikasi</button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="text-xs font-semibold text-gray-600">Password Saat Ini</label>
                <input type="password" name="current_password" required
                       class="mt-1 w-full border rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-yellow-400">
                @error('current_password')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600">Password Baru</label>
                <input type="password" name="password" minlength="8" required
                       class="mt-1 w-full border rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-yellow-400">
                @error('password')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" minlength="8" required
                       class="mt-1 w-full border rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-yellow-400">
            </div>
        </div>

        <div class="rounded-xl border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm text-yellow-900">
            Link verifikasi berlaku 30 menit. Setelah dikonfirmasi, Anda akan diminta login ulang.
        </div>
    </form>
</div>
@endsection
