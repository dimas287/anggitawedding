@extends('layouts.admin')
@section('title', 'Profil Klien')
@section('page-title', 'Profil Klien')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3 text-sm text-gray-500">
            <a href="{{ route('admin.clients.index') }}" class="flex items-center gap-2 text-gray-500 hover:text-gray-800">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar Klien
            </a>
            <span class="hidden sm:block">/</span>
            <span class="font-semibold text-gray-800">{{ $user->name }}</span>
        </div>
        <span class="inline-flex items-center gap-2 text-xs px-3 py-1.5 rounded-full {{ $user->address && $user->phone ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600' }}">
            <i class="fas {{ $user->address && $user->phone ? 'fa-check-circle' : 'fa-exclamation-triangle' }}"></i>
            {{ $user->address && $user->phone ? 'Profil Lengkap' : 'Data Kontak Belum Lengkap' }}
        </span>
    </div>

    <div class="grid lg:grid-cols-3 gap-5">
        <div class="lg:col-span-2 space-y-5">
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <div class="flex flex-col md:flex-row gap-5 md:items-center">
                    <div class="w-20 h-20 rounded-full gold-gradient flex items-center justify-center text-white text-3xl font-bold">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.4em] text-gray-400">Klien</p>
                        <h1 class="text-2xl font-semibold text-gray-900">{{ $user->name }}</h1>
                        <p class="text-sm text-gray-500">Bergabung {{ $user->created_at->translatedFormat('d F Y') }}</p>
                    </div>
                </div>
                <div class="mt-6 grid sm:grid-cols-2 gap-4 text-sm text-gray-600">
                    <div>
                        <p class="text-xs uppercase tracking-[0.3em] text-gray-400 mb-1">Email</p>
                        <p class="font-medium">{{ $user->email }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.3em] text-gray-400 mb-1">No. WhatsApp</p>
                        <p class="font-medium">{{ $user->phone ?? '-' }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-xs uppercase tracking-[0.3em] text-gray-400 mb-1">Alamat Lengkap</p>
                        <p class="font-medium">{{ $user->address ?? 'Belum diisi' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Riwayat Booking</h2>
                        <p class="text-sm text-gray-500">Urut paling terbaru</p>
                    </div>
                    <span class="text-xs px-3 py-1.5 rounded-full bg-gray-100 text-gray-600">{{ $stats['total_bookings'] }} Booking</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500 uppercase text-xs tracking-widest">
                                <th class="pb-3">Kode</th>
                                <th class="pb-3">Paket</th>
                                <th class="pb-3">Tanggal Event</th>
                                <th class="pb-3">Status</th>
                                <th class="pb-3">Dibuat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($bookings as $booking)
                                <tr class="align-top">
                                    <td class="py-3 font-semibold text-gray-800">#{{ $booking->code ?? $booking->id }}</td>
                                    <td class="py-3">
                                        <p class="text-gray-900 font-medium">{{ $booking->package->name ?? '-' }}</p>
                                        <p class="text-xs text-gray-400">{{ number_format($booking->package_price ?? 0, 0, ',', '.') }}</p>
                                    </td>
                                    <td class="py-3 text-gray-600">{{ optional($booking->event_date)->format('d M Y') ?? '-' }}</td>
                                    <td class="py-3">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold capitalize
                                            {{ $booking->status === 'completed' ? 'bg-emerald-50 text-emerald-600' : ($booking->status === 'pending' ? 'bg-yellow-50 text-yellow-700' : 'bg-blue-50 text-blue-600') }}">
                                            {{ str_replace('_', ' ', $booking->status) }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-gray-500">{{ $booking->created_at->format('d M Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-gray-400">Belum ada booking dari klien ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $bookings->links() }}
                </div>
            </div>
        </div>

        <div class="space-y-5">
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-[0.3em] mb-4">Ringkasan</h3>
                <div class="space-y-4 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Total Booking</span>
                        <span class="font-semibold text-gray-900">{{ $stats['total_bookings'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Sedang Berjalan</span>
                        <span class="font-semibold text-amber-600">{{ $stats['active'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Selesai</span>
                        <span class="font-semibold text-emerald-600">{{ $stats['completed'] }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-[0.3em] mb-4">Catatan Cepat</h3>
                <p class="text-xs text-gray-500 mb-3">Gunakan catatan admin di detail booking untuk menyimpan preferensi khusus klien ini.</p>
                <a href="{{ route('admin.bookings.index') }}?user={{ $user->id }}" class="text-sm text-yellow-600 font-semibold">Lihat booking terkait →</a>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-[0.3em]">Konsultasi Terakhir</h3>
                    <span class="text-xs text-gray-400">{{ $consultations->count() }} data</span>
                </div>
                <div class="space-y-4">
                    @forelse($consultations as $consultation)
                        <div class="border border-gray-100 rounded-xl p-4 text-sm">
                            <div class="flex items-center justify-between mb-2">
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $consultation->preferred_date?->format('d M Y') ?? 'Tanggal TBD' }}</p>
                                    <p class="text-xs text-gray-400">{{ $consultation->preferred_time ?? 'Waktu fleksibel' }}</p>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-semibold capitalize
                                    {{ $consultation->status === 'completed' ? 'bg-emerald-50 text-emerald-600' : ($consultation->status === 'pending' ? 'bg-yellow-50 text-yellow-700' : 'bg-blue-50 text-blue-600') }}">
                                    {{ $consultation->status }}
                                </span>
                            </div>
                            @if($consultation->notes)
                                <p class="text-gray-500 text-xs">{{ Str::limit($consultation->notes, 120) }}</p>
                            @else
                                <p class="text-gray-400 text-xs">Belum ada catatan.</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-xs text-gray-400">Belum ada konsultasi yang tercatat.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
