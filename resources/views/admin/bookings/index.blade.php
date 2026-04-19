@extends('layouts.admin')
@section('title', 'Daftar Booking')
@section('page-title', 'Manajemen Booking')

@section('content')
<div x-data="bookingModal()" class="space-y-5">
    {{-- Filter --}}
    <div class="bg-white rounded-xl shadow-sm p-4">
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama/kode booking..."
                   class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 flex-1 min-w-48">
            <select name="status" class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                <option value="">Semua Status</option>
                @foreach(['pending'=>'Pending','dp_paid'=>'DP Terbayar','in_progress'=>'In Progress','completed'=>'Selesai','cancelled'=>'Dibatalkan'] as $v => $l)
                <option value="{{ $v }}" {{ request('status') == $v ? 'selected' : '' }}>{{ $l }}</option>
                @endforeach
            </select>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
            <button type="submit" class="gold-gradient text-white font-semibold px-5 py-2.5 rounded-xl text-sm hover:shadow-md transition-all">
                <i class="fas fa-search mr-1"></i> Cari
            </button>
            @if(request()->hasAny(['search','status','date_from','date_to']))
            <a href="{{ route('admin.bookings.index') }}" class="border border-gray-200 text-gray-600 font-medium px-4 py-2.5 rounded-xl text-sm hover:bg-gray-50">Reset</a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="p-4 border-b flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">{{ $bookings->total() }} Booking</h3>
            <button @click="open()" type="button" class="text-sm font-semibold px-4 py-2 rounded-xl border border-gray-200 text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                <i class="fas fa-plus-circle text-yellow-500"></i> Tambah Booking Manual
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Kode</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Pengantin</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Paket</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Tanggal Acara</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Total Bayar</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($bookings as $b)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $b->booking_code }}</td>
                        <td class="px-4 py-3">
                            <p class="font-semibold text-gray-800">{{ $b->couple_short_display }}</p>
                            <p class="text-xs text-gray-500">{{ $b->user->name }}</p>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $b->package->name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $b->event_date->isoFormat('D MMM Y') }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-800">Rp {{ number_format($b->total_paid, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                {{ ['pending'=>'bg-yellow-100 text-yellow-700','dp_paid'=>'bg-blue-100 text-blue-700','in_progress'=>'bg-indigo-100 text-indigo-700','completed'=>'bg-green-100 text-green-700','cancelled'=>'bg-red-100 text-red-600'][$b->status] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $b->status_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('admin.bookings.show', $b->id) }}" class="text-xs font-medium px-3 py-1.5 gold-gradient text-white rounded-lg hover:shadow-md transition-all">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-10 text-center text-gray-400">Tidak ada booking ditemukan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t">{{ $bookings->withQueryString()->links() }}</div>
    </div>

    {{-- Modal --}}
    <div x-show="isOpen" x-cloak class="fixed inset-0 z-40 flex items-center justify-center">
        <div class="absolute inset-0 bg-black/40" @click="close()"></div>
        <div class="relative bg-white w-full max-w-3xl rounded-2xl shadow-2xl flex flex-col max-h-[90vh]">
            <div class="flex items-center justify-between border-b px-6 py-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Tambah Booking Manual</h3>
                    <p class="text-sm text-gray-500">Input detail booking untuk klien yang sudah konfirmasi paket.</p>
                </div>
                <button @click="close()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            <div class="overflow-y-auto px-6 py-4 space-y-4" style="scrollbar-width: thin;">
                <form id="booking-manual-form"
                      action="{{ route('admin.bookings.store') }}"
                      method="POST"
                      class="grid grid-cols-1 md:grid-cols-2 gap-4"
                      x-data="{ clientMode: @js(old('client_mode', 'existing')) }">
                    @csrf
                    <div class="md:col-span-2">
                        <label class="text-xs font-semibold text-gray-600">Jenis Klien</label>
                        <div class="flex flex-wrap gap-4 mt-2 text-sm text-gray-700">
                            <label class="inline-flex items-center gap-2">
                                <input type="radio" name="client_mode" value="existing" x-model="clientMode" class="text-yellow-500 focus:ring-yellow-400">
                                Klien Terdaftar
                            </label>
                            <label class="inline-flex items-center gap-2">
                                <input type="radio" name="client_mode" value="new" x-model="clientMode" class="text-yellow-500 focus:ring-yellow-400">
                                Tambah Klien Baru
                            </label>
                        </div>
                    </div>
                    <div class="md:col-span-2" x-show="clientMode === 'existing'" x-cloak>
                        <label class="text-xs font-semibold text-gray-600">Pilih Klien</label>
                        <select name="user_id" class="mt-1 w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400"
                                :required="clientMode === 'existing'" :disabled="clientMode !== 'existing'">
                            <option value="">-- Pilih Klien --</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" @selected(old('user_id') == $client->id)>{{ $client->name }} ({{ $client->email }})</option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-gray-400 mt-1">Pilih akun klien yang sudah ada.</p>
                    </div>
                    <div class="md:col-span-2 grid grid-cols-1 gap-4" x-show="clientMode === 'new'" x-cloak>
                        <div>
                            <label class="text-xs font-semibold text-gray-600">Nama Klien Baru</label>
                            <input type="text" name="client_name" class="mt-1 w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400"
                                   :required="clientMode === 'new'" :disabled="clientMode !== 'new'" placeholder="Contoh: Clara Nabila"
                                   value="{{ old('client_name') }}">
                        </div>
                        <p class="text-[11px] text-gray-400">Email dan WhatsApp di bawah akan digunakan untuk membuat akun baru. Klien dapat reset password melalui fitur "Lupa Password".</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600">Nama Pengantin Pria</label>
                        <input type="text" name="groom_name" class="mt-1 w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400" required>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600">Nama Singkat Pria</label>
                        <input type="text" name="groom_short_name" class="mt-1 w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400" required placeholder="Contoh: Bagas">
                        <p class="text-[11px] text-gray-400 mt-1">Dipakai untuk rundown, koordinasi tim, dan materi publikasi.</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600">Nama Pengantin Wanita</label>
                        <input type="text" name="bride_name" class="mt-1 w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400" required>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600">Nama Singkat Wanita</label>
                        <input type="text" name="bride_short_name" class="mt-1 w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400" required placeholder="Contoh: Rani">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600">Paket</label>
                        <select name="package_id" class="mt-1 w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400" required>
                            <option value="">-- Pilih Paket --</option>
                            @foreach($packages as $package)
                                <option value="{{ $package->id }}">{{ $package->name }} (Rp {{ number_format($package->price, 0, ',', '.') }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600">Tanggal Acara</label>
                        <input type="date" name="event_date" min="{{ now()->format('Y-m-d') }}" class="mt-1 w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400" required>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600">Venue</label>
                        <input type="text" name="venue" class="mt-1 w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400" required>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600">Alamat Venue (Opsional)</label>
                        <textarea name="venue_address" rows="2" class="mt-1 w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400"></textarea>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600">No. WhatsApp</label>
                        <input type="text" name="phone" class="mt-1 w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400" required>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600">Email</label>
                        <input type="email" name="email" class="mt-1 w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs font-semibold text-gray-600">Catatan Tambahan</label>
                        <textarea name="notes" rows="3" class="mt-1 w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400" placeholder="Detail spesial atau preferensi klien"></textarea>
                    </div>
                    <div class="md:col-span-2 flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="close()" class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600">Batal</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl gold-gradient text-white font-semibold">Simpan Booking</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function bookingModal() {
    return {
        isOpen: false,
        open() { this.isOpen = true; document.body.classList.add('overflow-hidden'); },
        close() { this.isOpen = false; document.body.classList.remove('overflow-hidden'); }
    }
}
</script>
@endpush

@endsection
