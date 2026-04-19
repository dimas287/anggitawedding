@extends('layouts.admin')
@section('title', 'Manajemen Konsultasi')
@section('page-title', 'Manajemen Konsultasi')

@section('content')
<div class="space-y-5" x-data="{ showManualModal: false }">
    {{-- Manual Creation Trigger --}}
    <div class="bg-white rounded-xl shadow-sm p-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Tambah Konsultasi Manual</h3>
            <p class="text-sm text-gray-500">Gunakan saat klien sudah deal via WhatsApp atau booking paket tanpa isi form.</p>
        </div>
        <button type="button" @click="showManualModal = true"
            class="inline-flex items-center gap-2 gold-gradient text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow hover:shadow-lg">
            <i class="fas fa-plus"></i> Input Konsultasi Manual
        </button>
    </div>

    {{-- Manual Modal --}}
    <div x-show="showManualModal" x-cloak class="fixed inset-0 z-[120] flex items-start md:items-center justify-center p-4 md:p-8" @keydown.window.escape="showManualModal = false">
        <div class="absolute inset-0 bg-black/50" @click="showManualModal = false"></div>
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-5xl max-h-[90vh] overflow-y-auto mx-4" @click.outside="showManualModal = false">
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <div>
                    <p class="text-xs uppercase tracking-wide text-gray-400">Form Manual</p>
                    <h3 class="text-lg font-semibold text-gray-900">Input Konsultasi Baru</h3>
                </div>
                <button type="button" class="text-gray-400 hover:text-gray-600" @click="showManualModal = false">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            <form action="{{ route('admin.consultations.store') }}" method="POST" class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                @csrf
                <div class="md:col-span-1">
                    <label class="text-xs font-semibold text-gray-600">Pilih Klien Terdaftar</label>
                    <select name="user_id" class="mt-1 w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400">
                        <option value="">-- Pilih Klien --</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ old('user_id') == $client->id ? 'selected' : '' }}>
                                {{ $client->name }} ({{ $client->email }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-600">Nama Kontak</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="mt-1 w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400" placeholder="Nama kontak klien">
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-600">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="mt-1 w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400" placeholder="email@klien.com">
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-600">No. WhatsApp</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="mt-1 w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400" placeholder="0812xxxxxxx">
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-600">Tanggal Konsultasi</label>
                    <input type="date" name="preferred_date" value="{{ old('preferred_date', now()->format('Y-m-d')) }}" class="mt-1 w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400" required>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-600">Waktu</label>
                    <input type="time" name="preferred_time" value="{{ old('preferred_time', '10:00') }}" class="mt-1 w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400" required>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-600">Tipe</label>
                    <select name="consultation_type" class="mt-1 w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400" required>
                        <option value="online" {{ old('consultation_type') === 'online' ? 'selected' : '' }}>Online (Video Call)</option>
                        <option value="offline" {{ old('consultation_type', 'online') === 'offline' ? 'selected' : '' }}>Offline (Kantor)</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-600">Target Hari-H (Opsional)</label>
                    <input type="date" name="event_date" value="{{ old('event_date') }}" class="mt-1 w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400">
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-600">Status Awal</label>
                    <select name="status" class="mt-1 w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400" required>
                        <option value="pending" {{ old('status', 'pending') === 'pending' ? 'selected' : '' }}>Pending (Menunggu konfirmasi admin)</option>
                        <option value="confirmed" {{ old('status') === 'confirmed' ? 'selected' : '' }}>Confirmed (Jadwal fix)</option>
                    </select>
                </div>
                <div class="md:col-span-3">
                    <label class="text-xs font-semibold text-gray-600">Catatan / Detail Permintaan</label>
                    <textarea name="message" rows="3" class="mt-1 w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400" placeholder="Opsional">{{ old('message') }}</textarea>
                </div>
                <div class="md:col-span-3 space-y-2">
                    <button type="submit" class="w-full gold-gradient text-white font-semibold px-6 py-3 rounded-xl text-sm shadow hover:shadow-lg">Simpan Konsultasi</button>
                    <p class="text-[11px] text-gray-500">Email otomatis dikirim (pending = email menunggu konfirmasi, confirmed = email jadwal fix + reminder otomatis).</p>
                </div>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-4">
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama/kode/email..."
                   class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 flex-1 min-w-48">
            <select name="status" class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                <option value="">Semua Status</option>
                @foreach(['pending'=>'Pending','confirmed'=>'Dikonfirmasi','done'=>'Selesai','cancelled'=>'Dibatalkan','converted'=>'Dikonversi'] as $v => $l)
                <option value="{{ $v }}" {{ request('status') == $v ? 'selected' : '' }}>{{ $l }}</option>
                @endforeach
            </select>
            <button type="submit" class="gold-gradient text-white font-semibold px-5 py-2.5 rounded-xl text-sm">
                <i class="fas fa-search mr-1"></i> Cari
            </button>
            @if(request()->hasAny(['search','status']))
            <a href="{{ route('admin.consultations.index') }}" class="border border-gray-200 text-gray-600 font-medium px-4 py-2.5 rounded-xl text-sm hover:bg-gray-50">Reset</a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="p-4 border-b">
            <h3 class="font-semibold text-gray-800">{{ $consultations->total() }} Konsultasi</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50"><tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Kode</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Nama</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Jadwal</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Tipe</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($consultations as $c)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $c->consultation_code }}</td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-800">{{ $c->name }}</p>
                            <p class="text-xs text-gray-500">{{ $c->email }} • {{ $c->phone }}</p>
                        </td>
                        <td class="px-4 py-3 text-gray-600 text-xs">
                            {{ $c->preferred_date->isoFormat('D MMM Y') }}<br>{{ $c->preferred_time }} WIB
                        </td>
                        <td class="px-4 py-3 text-gray-600 text-xs capitalize">{{ $c->consultation_type }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                {{ ['pending'=>'bg-yellow-100 text-yellow-700','confirmed'=>'bg-blue-100 text-blue-700','done'=>'bg-green-100 text-green-700','cancelled'=>'bg-red-100 text-red-600','converted'=>'bg-purple-100 text-purple-700'][$c->status] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $c->status_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1">
                                @if($c->status === 'pending')
                                <form action="{{ route('admin.consultations.status', $c->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="status" value="confirmed">
                                    <button class="text-xs font-medium px-2 py-1.5 bg-green-50 text-green-700 rounded-lg hover:bg-green-100">Konfirmasi</button>
                                </form>
                                @endif
                                <a href="{{ route('admin.consultations.show', $c->id) }}" class="text-xs font-medium px-2 py-1.5 gold-gradient text-white rounded-lg">Detail</a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-gray-400">Tidak ada konsultasi</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t">{{ $consultations->withQueryString()->links() }}</div>
    </div>
</div>
@endsection
