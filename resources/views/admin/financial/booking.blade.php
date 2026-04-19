@extends('layouts.admin')
@section('title', 'Keuangan Booking')
@section('page-title', 'Keuangan: ' . $booking->groom_name . ' & ' . $booking->bride_name)

@section('content')
<div class="space-y-5">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-green-400">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Total Pemasukan</p>
            <p class="text-2xl font-bold text-green-600">Rp {{ number_format($booking->financialTransactions->where('type','income')->sum('amount'), 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-red-400">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Total Pengeluaran</p>
            <p class="text-2xl font-bold text-red-500">Rp {{ number_format($booking->financialTransactions->where('type','expense')->sum('amount'), 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-yellow-400">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Profit Booking</p>
            @php $profit = $booking->financialTransactions->where('type','income')->sum('amount') - $booking->financialTransactions->where('type','expense')->sum('amount'); @endphp
            <p class="text-2xl font-bold {{ $profit >= 0 ? 'text-green-600' : 'text-red-500' }}">Rp {{ number_format($profit, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden" x-data="{ addOpen: false }">
        <div class="p-4 border-b flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">Transaksi – {{ $booking->booking_code }}</h3>
            <div class="flex gap-2">
                <a href="{{ route('admin.financial.export-pdf') }}?booking_id={{ $booking->id }}" class="text-xs font-medium px-3 py-2 bg-red-50 text-red-600 rounded-xl hover:bg-red-100">
                    <i class="fas fa-file-pdf mr-1"></i> Export PDF
                </a>
                <button @click="addOpen = true" class="text-xs font-medium px-3 py-2 gold-gradient text-white rounded-xl hover:shadow-md">
                    <i class="fas fa-plus mr-1"></i> Tambah
                </button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50"><tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Deskripsi</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Kategori</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Jumlah</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Tipe</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($booking->financialTransactions->sortByDesc('transaction_date') as $tx)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-600 text-xs">{{ \Carbon\Carbon::parse($tx->transaction_date)->isoFormat('D MMM Y') }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $tx->description }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $tx->category ?? '-' }}</td>
                        <td class="px-4 py-3 text-right font-semibold {{ $tx->type==='income'?'text-green-600':'text-red-500' }}">
                            {{ $tx->type==='income'?'+':'-' }}Rp {{ number_format($tx->amount, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs {{ $tx->type==='income'?'bg-green-100 text-green-700':'bg-red-100 text-red-600' }}">
                                {{ $tx->type==='income'?'Masuk':'Keluar' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <form action="{{ route('admin.financial.destroy', $tx->id) }}" method="POST" onsubmit="return confirm('Hapus transaksi?')">
                                @csrf @method('DELETE')
                                <button class="text-red-400 hover:text-red-600 text-xs"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-gray-400">Belum ada transaksi</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Add Modal --}}
        <div x-show="addOpen" x-cloak class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
            <div @click.outside="addOpen = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Tambah Transaksi</h3>
                <form action="{{ route('admin.financial.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                    <div class="grid grid-cols-2 gap-3">
                        <select name="type" required class="border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                            <option value="income">Pemasukan</option>
                            <option value="expense">Pengeluaran</option>
                        </select>
                        <input type="date" name="transaction_date" required value="{{ date('Y-m-d') }}" class="border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                    </div>
                    <input type="text" name="description" placeholder="Deskripsi *" required class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                    <div class="grid grid-cols-2 gap-3">
                        <input type="number" name="amount" placeholder="Jumlah (Rp) *" required min="0" class="border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        <input type="text" name="category" placeholder="Kategori" class="border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                    </div>
                    <textarea name="notes" rows="2" placeholder="Catatan" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 resize-none"></textarea>
                    <div class="flex gap-2 pt-2">
                        <button type="button" @click="addOpen = false" class="flex-1 border border-gray-200 text-gray-600 py-3 rounded-xl text-sm font-medium hover:bg-gray-50">Batal</button>
                        <button type="submit" class="flex-1 gold-gradient text-white font-semibold py-3 rounded-xl text-sm">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
