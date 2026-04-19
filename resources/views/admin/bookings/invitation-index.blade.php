@extends('layouts.admin')
@section('title', 'Booking Undangan Digital')
@section('page-title', 'Booking Undangan Digital')

@section('content')
<div x-data="invitationBookingModal()" class="space-y-5">
    <div class="bg-white rounded-xl shadow-sm p-4">
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama/kode booking..."
                   class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 flex-1 min-w-48">
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400">
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400">
            <button type="submit" class="bg-purple-600 text-white font-semibold px-5 py-2.5 rounded-xl text-sm hover:shadow-md transition-all">
                <i class="fas fa-search mr-1"></i> Cari
            </button>
            @if(request()->hasAny(['search','date_from','date_to']))
            <a href="{{ route('admin.invitation-bookings.index') }}" class="border border-gray-200 text-gray-600 font-medium px-4 py-2.5 rounded-xl text-sm hover:bg-gray-50">Reset</a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="p-4 border-b flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">{{ $bookings->total() }} Booking Undangan</h3>
            <button @click="open()" type="button" class="text-sm font-semibold px-4 py-2 rounded-xl border border-gray-200 text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                <i class="fas fa-plus-circle text-purple-500"></i> Tambah Booking Undangan
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Kode</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Pengantin</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Template</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Harga</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($bookings as $book)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $book->booking_code }}</td>
                        <td class="px-4 py-3">
                            <p class="font-semibold text-gray-800">{{ $book->groom_name }} & {{ $book->bride_name }}</p>
                            <p class="text-xs text-gray-500">{{ optional($book->user)->name }}</p>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ optional(optional($book->invitation)->template)->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $book->event_date->isoFormat('D MMM Y') }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-800">Rp {{ number_format($book->package_price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                {{ ['pending'=>'bg-yellow-100 text-yellow-700','dp_paid'=>'bg-blue-100 text-blue-700','completed'=>'bg-green-100 text-green-700'][$book->status] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $book->status_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('admin.bookings.show', $book->id) }}" class="text-xs font-medium px-3 py-1.5 bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100 transition-all">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-10 text-center text-gray-400">Belum ada booking undangan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t">{{ $bookings->withQueryString()->links() }}</div>
    </div>

    <div x-show="isOpen" x-cloak class="fixed inset-0 z-40 flex items-center justify-center">
        <div class="absolute inset-0 bg-black/40" @click="close()"></div>
        <div class="relative bg-white w-full max-w-2xl rounded-2xl shadow-2xl">
            <div class="flex items-center justify-between border-b px-6 py-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Tambah Booking Undangan Digital</h3>
                    <p class="text-sm text-gray-500">Input data klien dan pilih template undangan.</p>
                </div>
                <button @click="close()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            <div class="overflow-y-auto px-6 py-4" style="max-height: 75vh">
                <form action="{{ route('admin.invitation-bookings.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="text-xs font-semibold text-gray-600">Klien</label>
                        <select name="user_id" class="mt-1 w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-purple-400" required>
                            <option value="">-- Pilih Klien --</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->name }} ({{ $client->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs font-semibold text-gray-600">Nama Pengantin Pria</label>
                            <input type="text" name="groom_name" class="mt-1 w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-purple-400" required>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-600">Nama Pengantin Wanita</label>
                            <input type="text" name="bride_name" class="mt-1 w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-purple-400" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs font-semibold text-gray-600">Nama Panggilan Pria</label>
                            <input type="text" name="groom_short_name" class="mt-1 w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-purple-400" placeholder="Contoh: Iqbal" required>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-600">Nama Panggilan Wanita</label>
                            <input type="text" name="bride_short_name" class="mt-1 w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-purple-400" placeholder="Contoh: Rika" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs font-semibold text-gray-600">Tanggal Launching</label>
                            <input type="date" name="event_date" class="mt-1 w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-purple-400" required>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-600">Template</label>
                            <select name="template_id" class="mt-1 w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-purple-400" required>
                                <option value="">-- Pilih Template --</option>
                                @foreach($templates as $template)
                                    <option value="{{ $template->id }}">{{ $template->name }} (Rp {{ number_format($template->effective_price ?? $template->price, 0, ',', '.') }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600">Harga Pembayaran</label>
                        <input type="number" name="price" min="0" step="1000" class="mt-1 w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-purple-400" required>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600">Catatan Tambahan (opsional)</label>
                        <textarea name="notes" rows="3" class="mt-1 w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-purple-400"></textarea>
                    </div>
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="close()" class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600">Batal</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-purple-600 text-white font-semibold hover:bg-purple-700">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function invitationBookingModal() {
    return {
        isOpen: false,
        open() { this.isOpen = true; document.body.classList.add('overflow-hidden'); },
        close() { this.isOpen = false; document.body.classList.remove('overflow-hidden'); }
    }
}
</script>
@endpush
@endsection
