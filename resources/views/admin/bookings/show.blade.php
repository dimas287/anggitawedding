@extends('layouts.admin')
@section('title', $booking->couple_short_display)
@section('page-title', $booking->couple_short_display)
@section('breadcrumb', 'Booking / ' . $booking->booking_code)

@section('content')
<div class="space-y-5" x-data="{ activeTab: 'info', extraChargeOpen: false, deleteModal: false }">

    @php
        $isInvitationOnly = $booking->is_invitation_only;
        $paymentStatusMap = [
            'unpaid' => 'Belum Bayar',
            'dp_paid' => 'DP Terbayar',
            'partially_paid' => 'Cicilan',
            'paid_full' => 'Lunas',
        ];
        $statusOptions = $isInvitationOnly
            ? ['pending' => 'Menunggu Pembayaran', 'dp_paid' => 'Siap Publish', 'completed' => 'Published', 'cancelled' => 'Dibatalkan']
            : ['pending' => 'Pending', 'dp_paid' => 'DP Terbayar', 'in_progress' => 'In Progress', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'];
        $tabOptions = [
            ['id' => 'info', 'icon' => 'fa-info-circle', 'label' => 'Info'],
            ['id' => 'payment', 'icon' => 'fa-credit-card', 'label' => 'Pembayaran'],
            ['id' => 'consultation', 'icon' => 'fa-comments', 'label' => 'Konsultasi'],
            ['id' => 'vendor', 'icon' => 'fa-store', 'label' => 'Vendor'],
            ['id' => 'fitting', 'icon' => 'fa-ruler-combined', 'label' => 'Fitting & Wardrobe'],
            ['id' => 'rundown', 'icon' => 'fa-list-ol', 'label' => 'Rundown'],
            ['id' => 'financial', 'icon' => 'fa-coins', 'label' => 'Keuangan'],
            ['id' => 'document', 'icon' => 'fa-folder', 'label' => 'Dokumen'],
            ['id' => 'invitation', 'icon' => 'fa-envelope', 'label' => 'Undangan'],
        ];
        if ($isInvitationOnly) {
            $tabOptions = array_values(array_filter($tabOptions, fn ($tab) => in_array($tab['id'], ['info', 'payment', 'invitation'])));
        }
    @endphp

    {{-- Status Bar --}}
    <div class="bg-white rounded-xl shadow-sm p-4 flex items-center justify-between gap-4 flex-wrap">
        <div class="flex items-center gap-3">
            <span class="px-3 py-1.5 rounded-full text-sm font-semibold
                {{ ['pending'=>'bg-yellow-100 text-yellow-700','dp_paid'=>'bg-blue-100 text-blue-700','in_progress'=>'bg-indigo-100 text-indigo-700','completed'=>'bg-green-100 text-green-700','cancelled'=>'bg-red-100 text-red-600'][$booking->status] ?? 'bg-gray-100 text-gray-600' }}">
                {{ $booking->status_label }}
            </span>
            @if($isInvitationOnly)
                <span class="px-3 py-1.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">Undangan Digital</span>
            @endif
            <span class="px-3 py-1.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                Pembayaran: {{ $paymentStatusMap[$booking->payment_status] ?? ucwords(str_replace('_', ' ', $booking->payment_status)) }}
            </span>
            <span class="text-sm text-gray-500">{{ $booking->booking_code }}</span>
        </div>

{{-- Edit Vendor Modal --}}
<div id="vendorEditModal" class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-800">Edit Vendor</h3>
            <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeVendorEditModal()"><i class="fas fa-times"></i></button>
        </div>
        <form id="vendorEditForm" class="space-y-4" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="admin_password" value="">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-medium text-gray-600">Kategori</label>
                    <input type="text" name="category" id="vendorEditCategory" class="mt-1 w-full border border-gray-200 rounded-lg px-3 py-2 text-sm" required>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-600">Nama Vendor</label>
                    <input type="text" name="vendor_name" id="vendorEditName" class="mt-1 w-full border border-gray-200 rounded-lg px-3 py-2 text-sm" required>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-600">Kontak</label>
                    <input type="text" name="contact" id="vendorEditContact" class="mt-1 w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-600">Status</label>
                    <select name="status" id="vendorEditStatus" class="mt-1 w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        @foreach(['assigned'=>'Assigned','confirmed'=>'Confirmed','done'=>'Done','cancelled'=>'Cancelled'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-medium text-gray-600">Biaya (Rp)</label>
                    <input type="number" min="0" name="cost" id="vendorEditCost" class="mt-1 w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-600">Catatan</label>
                    <input type="text" name="notes" id="vendorEditNotes" class="mt-1 w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>
            <div>
                <label class="text-xs font-medium text-gray-600">Bukti Pembayaran Vendor (opsional)</label>
                <div class="space-y-1 mt-1 border border-dashed border-gray-300 rounded-xl p-3">
                    <input type="file" name="proof_attachment" id="vendorEditProof" accept="image/*,application/pdf" class="w-full text-xs text-gray-600">
                    <p class="text-[11px] text-gray-400">Format JPG/PNG/WEBP/PDF • maks 5MB</p>
                    <a id="vendorCurrentProofLink" href="#" target="_blank" class="text-xs text-amber-600 font-semibold hover:text-amber-800 hidden">Lihat bukti saat ini</a>
                </div>
            </div>
            <div class="flex gap-2 pt-2">
                <button type="button" onclick="closeVendorEditModal()" class="flex-1 border border-gray-200 text-gray-600 py-2 rounded-lg text-sm">Batal</button>
                <button type="button" onclick="submitEditVendor()" class="flex-1 gold-gradient text-white font-semibold py-2 rounded-lg text-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>
        <div class="flex items-center gap-2 flex-wrap">
            <form action="{{ route('admin.bookings.status', $booking->id) }}" method="POST" class="flex gap-2">
                @csrf @method('PUT')
                <select name="status" class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                    @foreach($statusOptions as $v => $l)
                        <option value="{{ $v }}" {{ $booking->status == $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
                <button type="submit" class="gold-gradient text-white font-semibold px-4 py-2 rounded-xl text-sm">Update</button>
            </form>
            <a href="{{ route('admin.reports.event', $booking->id) }}" data-no-loader class="text-sm font-medium px-4 py-2 bg-gray-50 text-gray-600 rounded-xl hover:bg-gray-100"><i class="fas fa-file-pdf mr-1"></i> Laporan Event</a>
            <a href="{{ route('admin.reports.invoice', $booking->id) }}" data-no-loader class="text-sm font-medium px-4 py-2 bg-gray-50 text-gray-600 rounded-xl hover:bg-gray-100"><i class="fas fa-file-pdf mr-1"></i> Invoice</a>
            <form action="{{ route('admin.bookings.invoice-email', $booking->id) }}" method="POST" onsubmit="return confirm('Kirim invoice ke email klien sekarang?');">
                @csrf
                <button type="submit" class="text-sm font-semibold px-4 py-2 border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 flex items-center gap-1">
                    <i class="fas fa-paper-plane text-yellow-500"></i> Kirim Invoice via Email
                </button>
            </form>
            @if($booking->status === 'cancelled')
                <button type="button" @click="deleteModal = true" class="text-sm font-semibold px-4 py-2 rounded-xl border border-red-200 text-red-600 hover:bg-red-50 flex items-center gap-1">
                    <i class="fas fa-trash"></i> Hapus Booking
                </button>
            @endif
        </div>
    </div>

    {{-- Tabs --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="border-b flex overflow-x-auto">
            @foreach($tabOptions as $tab)
            <button @click="activeTab = '{{ $tab['id'] }}'"
                    :class="activeTab === '{{ $tab['id'] }}' ? 'border-b-2 border-yellow-500 text-yellow-600 bg-yellow-50' : 'text-gray-500 hover:text-gray-700'"
                    class="flex items-center gap-1.5 px-4 py-3 text-xs font-semibold whitespace-nowrap transition-colors flex-shrink-0">
                <i class="fas {{ $tab['icon'] }}"></i> {{ $tab['label'] }}
            </button>
            @endforeach
        </div>

        {{-- Info Tab --}}
        <div x-show="activeTab === 'info'" class="p-6" x-data="{ editAdmin:false, editClient:false }">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2">
                    <h3 class="font-semibold text-gray-800 text-base mb-4">Data Booking</h3>
                    <form action="{{ route('admin.bookings.info', $booking->id) }}" method="POST" class="space-y-4 text-sm">
                        @csrf @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs text-gray-500">Pengantin Pria</label>
                                <input type="text" name="groom_name" value="{{ old('groom_name', $booking->groom_name) }}" class="mt-1 w-full border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-yellow-400">
                            </div>
                            <div>
                                <label class="text-xs text-gray-500">Nama Singkat Pria</label>
                                <input type="text" name="groom_short_name" value="{{ old('groom_short_name', $booking->groom_short_name) }}" class="mt-1 w-full border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-yellow-400" placeholder="Contoh: Dim">
                                <p class="text-[11px] text-gray-400 mt-1">Ditampilkan sebagai panggilan internal dan materi undangan.</p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500">Pengantin Wanita</label>
                                <input type="text" name="bride_name" value="{{ old('bride_name', $booking->bride_name) }}" class="mt-1 w-full border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-yellow-400">
                            </div>
                            <div>
                                <label class="text-xs text-gray-500">Nama Singkat Wanita</label>
                                <input type="text" name="bride_short_name" value="{{ old('bride_short_name', $booking->bride_short_name) }}" class="mt-1 w-full border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-yellow-400" placeholder="Contoh: Lis">
                                <p class="text-[11px] text-gray-400 mt-1">Wajib diisi agar tampil di semua dokumen.</p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500">Tanggal Acara</label>
                                <input type="date" name="event_date" value="{{ old('event_date', $booking->event_date->format('Y-m-d')) }}" class="mt-1 w-full border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-yellow-400">
                            </div>
                            @if(!$isInvitationOnly)
                                <div>
                                    <label class="text-xs text-gray-500">Paket</label>
                                    <select name="package_id" class="mt-1 w-full border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-yellow-400">
                                        @foreach($packages as $pkg)
                                            <option value="{{ $pkg->id }}" {{ $pkg->id == $booking->package_id ? 'selected' : '' }}>{{ $pkg->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @else
                                <div>
                                    <label class="text-xs text-gray-500">Paket</label>
                                    <input type="text" value="Undangan Digital" readonly class="mt-1 w-full border border-gray-100 bg-gray-50 rounded-xl px-3 py-2 text-gray-500">
                                </div>
                            @endif
                            @if($isInvitationOnly)
                                <div>
                                    <label class="text-xs text-gray-500">Template Undangan</label>
                                    <input type="text" value="{{ optional(optional($booking->invitation)->template)->name ?? '-' }}" readonly class="mt-1 w-full border border-gray-100 bg-gray-50 rounded-xl px-3 py-2 text-gray-600">
                                </div>
                            @else
                                <div>
                                    <label class="text-xs text-gray-500">Venue</label>
                                    <input type="text" name="venue" value="{{ old('venue', $booking->venue) }}" class="mt-1 w-full border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-yellow-400">
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500">Alamat Venue</label>
                                    <input type="text" name="venue_address" value="{{ old('venue_address', $booking->venue_address) }}" class="mt-1 w-full border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-yellow-400">
                                </div>
                            @endif
                            <div>
                                <label class="text-xs text-gray-500">No. HP</label>
                                <input type="text" name="phone" value="{{ old('phone', $booking->phone) }}" class="mt-1 w-full border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-yellow-400">
                            </div>
                            <div>
                                <label class="text-xs text-gray-500">Email</label>
                                <input type="email" name="email" value="{{ old('email', $booking->email ?? $booking->user->email) }}" class="mt-1 w-full border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-yellow-400">
                            </div>
                            @if($isInvitationOnly && $booking->invitation)
                                <div class="md:col-span-2">
                                    <label class="text-xs text-gray-500">Link Undangan</label>
                                    <div class="mt-1 flex gap-2">
                                        <input type="text" value="{{ $booking->invitation->public_url }}" readonly class="flex-1 border border-gray-100 bg-gray-50 rounded-xl px-3 py-2 text-gray-600">
                                        <a href="{{ $booking->invitation->public_url }}" target="_blank" class="px-3 py-2 rounded-xl text-xs font-semibold text-purple-600 border border-purple-200">Buka</a>
                                    </div>
                                </div>
                            @endif
                            <div class="md:col-span-2">
                                <label class="text-xs text-gray-500">Catatan Umum</label>
                                <textarea name="notes" rows="2" class="mt-1 w-full border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-yellow-400">{{ old('notes', $booking->notes) }}</textarea>
                            </div>
                        </div>
                        <button type="submit" class="text-sm font-semibold px-4 py-2 gold-gradient text-white rounded-xl">Simpan Perubahan</button>
                        @if($booking->client_notes)
                        <div class="pt-2 text-sm text-gray-600">
                            <span class="text-xs uppercase text-gray-400">Catatan untuk Klien:</span>
                            <p class="font-medium text-gray-800 whitespace-pre-line mt-1">{{ $booking->client_notes }}</p>
                        </div>
                        @endif
                    </form>
                </div>
                <div class="space-y-6">
                    <div>
                        <div class="flex items-center justify-between gap-4 mb-1">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Catatan Internal Admin</p>
                            <button type="button" class="text-xs text-yellow-600 font-semibold whitespace-nowrap" @click="editAdmin = !editAdmin" x-text="editAdmin ? 'Batal' : 'Edit'">Edit</button>
                        </div>
                        <p class="text-sm text-gray-800 whitespace-pre-line" x-show="!editAdmin" x-cloak>{{ $booking->admin_notes ?: 'Belum ada catatan internal.' }}</p>
                        <form x-show="editAdmin" x-cloak action="{{ route('admin.bookings.notes', $booking->id) }}" method="POST" class="space-y-2">
                            @csrf @method('PUT')
                            <textarea name="admin_notes" rows="4" class="w-full border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-yellow-400">{{ $booking->admin_notes }}</textarea>
                            <input type="hidden" name="client_notes" value="{{ $booking->client_notes }}">
                            <div class="flex justify-end gap-2">
                                <button type="button" class="px-3 py-1.5 text-xs text-gray-500" @click="editAdmin = false">Batal</button>
                                <button type="submit" class="px-4 py-1.5 text-xs font-semibold gold-gradient text-white rounded-xl">Simpan</button>
                            </div>
                        </form>
                    </div>
                    <div>
                        <div class="flex items-center justify-between gap-4 mb-1">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Catatan untuk Dashboard Klien</p>
                            <button type="button" class="text-yellow-600 font-semibold whitespace-nowrap" @click="editClient = !editClient" x-text="editClient ? 'Batal' : 'Edit'">Edit</button>
                        </div>
                        <p class="text-sm text-gray-800 whitespace-pre-line" x-show="!editClient" x-cloak>{{ $booking->client_notes ?: 'Belum ada catatan untuk klien.' }}</p>
                        <form x-show="editClient" x-cloak action="{{ route('admin.bookings.notes', $booking->id) }}" method="POST" class="space-y-2">
                            @csrf @method('PUT')
                            <textarea name="client_notes" rows="4" class="w-full border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-yellow-400">{{ $booking->client_notes }}</textarea>
                            <input type="hidden" name="admin_notes" value="{{ $booking->admin_notes }}">
                            <div class="flex justify-end gap-2">
                                <button type="button" class="px-3 py-1.5 text-xs text-gray-500" @click="editClient = false">Batal</button>
                                <button type="submit" class="px-4 py-1.5 text-xs font-semibold gold-gradient text-white rounded-xl">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Payment Tab --}}
        <div x-show="activeTab === 'payment'" class="p-6">
            @php $extraTotal = $booking->active_extra_charges_total; @endphp
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm text-gray-500">{{ $isInvitationOnly ? 'Harga Undangan' : 'Total Paket' }}: <strong>Rp {{ number_format($booking->package_price, 0, ',', '.') }}</strong></p>
                    <p class="text-sm text-gray-500">Biaya Tambahan: <strong class="text-amber-600">Rp {{ number_format($extraTotal, 0, ',', '.') }}</strong></p>
                    <p class="text-sm text-gray-500">Terbayar: <strong class="text-green-600">Rp {{ number_format($booking->total_paid, 0, ',', '.') }}</strong></p>
                    <p class="text-sm text-gray-500">Grand Total: <strong>Rp {{ number_format($booking->package_price + $extraTotal, 0, ',', '.') }}</strong></p>
                    <p class="text-sm text-gray-500">Sisa: <strong class="text-red-500">Rp {{ number_format(max(0, ($booking->package_price + $extraTotal) - $booking->total_paid), 0, ',', '.') }}</strong></p>
                    @if($isInvitationOnly)
                        <div class="mt-3 text-xs px-3 py-2 bg-purple-50 text-purple-700 rounded-xl inline-flex items-center gap-2">
                            <i class="fas fa-envelope-open-text"></i>
                            Pembayaran Undangan Digital • harus lunas sebelum publish
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Status Publikasi: <strong>{{ optional($booking->invitation)->is_published ? 'Sudah Publish' : 'Belum Publish' }}</strong></p>
                    @endif
                </div>
                @unless($isInvitationOnly)
                    <button type="button" @click="extraChargeOpen = true" class="inline-flex items-center gap-2 border border-amber-200 text-amber-700 px-4 py-2 rounded-xl text-sm font-semibold hover:bg-amber-50">
                        <i class="fas fa-plus"></i> Biaya Tambahan
                    </button>
                @endunless
            </div>
            <div class="space-y-2 mb-6">
                <div class="flex items-center justify-between">
                    <h4 class="font-semibold text-gray-800 text-sm">Daftar Biaya Tambahan</h4>
                </div>
                @forelse($booking->extraCharges as $charge)
                    <div class="p-4 bg-amber-50 rounded-xl text-sm space-y-3" x-data="{ editing:false }">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="font-semibold text-gray-800">{{ $charge->title }}</p>
                                @if($charge->notes)
                                    <p class="text-xs text-gray-500">{{ $charge->notes }}</p>
                                @endif
                            </div>
                            <div class="text-right space-y-1">
                                <p class="font-bold {{ $charge->amount < 0 ? 'text-red-600' : 'text-amber-700' }}">
                                    {{ $charge->amount < 0 ? '−' : '' }}Rp {{ number_format(abs($charge->amount), 0, ',', '.') }}
                                </p>
                                <div class="flex items-center justify-end gap-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $charge->amount < 0 ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ $charge->amount < 0 ? 'Pengurangan' : 'Penambahan' }}
                                    </span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ match($charge->status) {
                                        'paid' => 'bg-green-100 text-green-700',
                                        'billed' => 'bg-blue-100 text-blue-700',
                                        'waived' => 'bg-gray-100 text-gray-500',
                                        default => 'bg-amber-100 text-amber-700',
                                    } }}">{{ ucfirst($charge->status) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 text-xs">
                            <button type="button" class="text-yellow-700 font-semibold" @click="editing = !editing" x-text="editing ? 'Batal' : 'Edit'">Edit</button>
                            <form action="{{ route('admin.bookings.extra-charge.delete', [$booking->id, $charge->id]) }}" method="POST" onsubmit="return confirm('Hapus biaya tambahan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 font-semibold">Hapus</button>
                            </form>
                        </div>
                        <form x-show="editing" x-cloak action="{{ route('admin.bookings.extra-charge.update', [$booking->id, $charge->id]) }}" method="POST" class="space-y-3 bg-white rounded-xl p-3 border border-amber-100">
                            @csrf
                            @method('PUT')
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="text-xs text-gray-500">Judul</label>
                                    <input type="text" name="title" value="{{ $charge->title }}" class="mt-1 w-full border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-amber-400">
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <label class="text-xs text-gray-500">Jumlah</label>
                                        <input type="number" name="amount" value="{{ abs($charge->amount) }}" class="mt-1 w-full border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-amber-400">
                                    </div>
                                    <div>
                                        <label class="text-xs text-gray-500">Tipe</label>
                                        <select name="charge_type" class="mt-1 w-full border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-amber-400">
                                            <option value="addition" {{ $charge->amount >= 0 ? 'selected' : '' }}>Penambahan</option>
                                            <option value="discount" {{ $charge->amount < 0 ? 'selected' : '' }}>Pengurangan</option>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500">Status</label>
                                    <select name="status" class="mt-1 w-full border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-amber-400">
                                        @foreach(['pending'=>'Pending','billed'=>'Ditagihkan','paid'=>'Lunas','waived'=>'Dihapus'] as $value => $label)
                                            <option value="{{ $value }}" {{ $charge->status === $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500">Catatan</label>
                                <textarea name="notes" rows="2" class="mt-1 w-full border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-amber-400">{{ $charge->notes }}</textarea>
                            </div>
                            <div class="text-right">
                                <button type="submit" class="px-4 py-2 text-xs font-semibold gold-gradient text-white rounded-xl">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                @empty
                    <p class="text-xs text-gray-400">Belum ada biaya tambahan untuk booking ini.</p>
                @endforelse
            </div>
            <div class="space-y-3 mb-6">
                @forelse($booking->payments as $pay)
                <div class="bg-gray-50 rounded-xl p-4 text-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex-1">
                        <p class="font-medium text-gray-800">{{ $pay->payment_code }}</p>
                        <p class="text-xs text-gray-500">{{ ucfirst($pay->type) }} • {{ $pay->method }} • {{ $pay->paid_at?->format('d M Y H:i') }}</p>
                    </div>
                    <div class="md:text-right">
                        <p class="font-bold {{ $pay->status==='success'?'text-green-600':'text-yellow-600' }}">Rp {{ number_format($pay->amount, 0, ',', '.') }}</p>
                        <span class="text-xs px-2 py-0.5 rounded-full inline-block mt-1 md:mt-0 {{ $pay->status==='success'?'bg-green-100 text-green-700':'bg-yellow-100 text-yellow-700' }}">{{ ucfirst($pay->status) }}</span>
                    </div>
                    <div class="flex items-center gap-3 text-xs w-full md:w-auto md:justify-end flex-wrap">
                        @if($pay->proof_url)
                            <a href="{{ $pay->proof_url }}" target="_blank" class="inline-flex items-center gap-1 text-amber-600 hover:text-amber-800 font-semibold">
                                <i class="fas fa-paperclip"></i> Lihat Bukti
                            </a>
                        @else
                            <span class="text-gray-400 italic">Belum ada bukti</span>
                        @endif
                        <button type="button"
                                class="text-blue-500 hover:text-blue-700"
                                data-payment-id="{{ $pay->id }}"
                                data-payment-code="{{ $pay->payment_code }}"
                                data-payment-amount="{{ $pay->amount }}"
                                data-payment-type="{{ $pay->type }}"
                                data-payment-method="{{ $pay->method }}"
                                data-payment-status="{{ $pay->status }}"
                                data-payment-notes="{{ e($pay->notes) }}"
                                data-payment-proof="{{ $pay->proof_url ?? '' }}"
                                onclick="openEditPayment(this)"><i class="fas fa-edit"></i></button>
                        <button onclick="confirmDeletePayment({{ $pay->id }})" class="text-red-400 hover:text-red-600"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
                @empty<p class="text-gray-400 text-sm">Belum ada pembayaran</p>
                @endforelse
            </div>
            <div class="border-t pt-4">
                <h4 class="font-semibold text-gray-800 mb-3 text-sm">Tambah Pembayaran Offline</h4>
                <form action="{{ route('admin.bookings.payment-offline', $booking->id) }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 sm:grid-cols-5 gap-3">
                    @csrf
                    <input type="number" name="amount" placeholder="Jumlah (Rp)" required min="1000" class="border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                    @if($isInvitationOnly)
                        <input type="hidden" name="type" value="full">
                        <div class="border border-gray-200 rounded-xl px-3 py-2.5 text-sm flex items-center bg-purple-50 text-purple-700 font-semibold">
                            Pembayaran Undangan (Full)
                        </div>
                    @else
                        <select name="type" class="border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                            <option value="dp">DP</option>
                            <option value="installment">Cicilan</option>
                            <option value="full">Pelunasan</option>
                            <option value="offline">Offline</option>
                        </select>
                    @endif
                    <input type="text" name="notes" placeholder="Keterangan" class="border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                    <div class="sm:col-span-2 space-y-1">
                        <p class="text-xs font-semibold text-gray-600">Bukti Pembayaran</p>
                        <label class="flex flex-col text-xs text-gray-500 border border-dashed border-gray-300 rounded-xl px-3 py-2.5 bg-white">
                            <span class="font-semibold text-gray-700 text-sm">Unggah Bukti Transfer</span>
                            <input type="file" name="proof_attachment" accept="image/*,application/pdf" class="mt-1 text-xs text-gray-600">
                            <span class="mt-1 text-[11px] text-gray-400">Format JPG/PNG/WEBP/PDF • maks 5MB</span>
                        </label>
                    </div>
                    <button type="submit" class="gold-gradient text-white font-semibold py-2.5 rounded-xl text-sm">Tambah</button>
                </form>
            </div>
        </div>

        {{-- Vendor Tab --}}
        <div x-show="activeTab === 'vendor'" class="p-6">
            <h4 class="font-semibold text-gray-800 mb-3 text-sm">Vendor Ter-assign</h4>
            <div class="space-y-2 mb-5">
                @forelse($booking->vendors as $v)
                <div class="p-3 bg-gray-50 rounded-xl text-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-gray-800">{{ $v->vendor_name }}</p>
                            <p class="text-xs text-gray-500">{{ $v->category }} • {{ $v->contact ?: '–' }}</p>
                            <div class="text-[11px] text-gray-500 flex items-center gap-2 mt-1">
                                @if($v->proof_url)
                                    <a href="{{ $v->proof_url }}" target="_blank" class="inline-flex items-center gap-1 text-amber-600 font-semibold"><i class="fas fa-paperclip"></i> Bukti Transfer</a>
                                @else
                                    <span class="italic">Belum ada bukti pembayaran vendor</span>
                                @endif
                                @if($v->notes)
                                    <span>• {{ $v->notes }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-gray-700 font-semibold">Rp {{ number_format($v->cost, 0, ',', '.') }}</p>
                            <span class="px-2 py-0.5 mt-1 inline-block rounded-full text-xs {{ $v->status==='confirmed' ? 'bg-green-100 text-green-700' : ($v->status==='done' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700') }}">{{ ucfirst($v->status) }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 mt-3 text-xs">
                        <button type="button"
                                class="text-blue-500 hover:text-blue-700"
                                data-vendor-id="{{ $v->id }}"
                                data-vendor-category="{{ $v->category }}"
                                data-vendor-name="{{ $v->vendor_name }}"
                                data-vendor-contact="{{ $v->contact }}"
                                data-vendor-status="{{ $v->status }}"
                                data-vendor-cost="{{ $v->cost }}"
                                data-vendor-notes="{{ e($v->notes) }}"
                                data-vendor-proof="{{ $v->proof_url ?? '' }}"
                                data-update-url="{{ route('admin.vendors.booking-update', $v->id) }}"
                                onclick="openEditVendor(this)"><i class="fas fa-edit"></i> Edit</button>
                        <button type="button"
                                class="text-red-500 hover:text-red-700"
                                data-delete-url="{{ route('admin.vendors.booking-remove', $v->id) }}"
                                onclick="confirmDeleteVendor(this)"><i class="fas fa-trash"></i> Hapus</button>
                    </div>
                </div>
                @empty<p class="text-gray-400 text-sm">Belum ada vendor</p>
                @endforelse
            </div>
            <div class="border-t pt-4">
                <h4 class="font-semibold text-gray-800 mb-3 text-sm">Assign Vendor Baru</h4>
                <form action="{{ route('admin.vendors.assign', $booking->id) }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    @csrf
                    <input type="text" name="category" placeholder="Kategori (Foto, Band, dll)" required class="border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                    <input type="text" name="vendor_name" placeholder="Nama vendor" required class="border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                    <input type="text" name="contact" placeholder="Kontak vendor" class="border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                    <input type="number" name="cost" placeholder="Biaya (Rp)" min="0" class="border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                    <input type="text" name="notes" placeholder="Catatan" class="border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                    <div class="sm:col-span-3 space-y-1">
                        <p class="text-xs font-semibold text-gray-600">Bukti Pembayaran Vendor (opsional)</p>
                        <label class="flex flex-col text-xs text-gray-500 border border-dashed border-gray-300 rounded-xl px-3 py-2.5 bg-white">
                            <span class="font-semibold text-gray-700 text-sm">Unggah Bukti Transfer vendor</span>
                            <input type="file" name="proof_attachment" accept="image/*,application/pdf" class="mt-1 text-xs text-gray-600">
                            <span class="mt-1 text-[11px] text-gray-400">Format JPG/PNG/WEBP/PDF • maks 5MB</span>
                        </label>
                    </div>
                    <button type="submit" class="sm:col-span-3 gold-gradient text-white font-semibold py-2.5 rounded-xl text-sm">Assign</button>
                </form>
            </div>
        </div>

        {{-- Fitting & Wardrobe Tab --}}
        <div x-show="activeTab === 'fitting'" class="p-6 space-y-6" x-data="{ fittingModal:false, wardrobeModal:false }">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between px-5 py-4 border-b">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Jadwal Fitting</p>
                        <h4 class="text-lg font-semibold text-gray-800">Monitoring Jadwal Fitting Klien</h4>
                    </div>
                    <button type="button" @click="fittingModal = true"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-xl gold-gradient text-white shadow">
                        <i class="fas fa-plus-circle"></i> Tambah Jadwal Fitting
                    </button>
                </div>
                <div class="p-5 overflow-x-auto">
                    @if($booking->fittings->isEmpty())
                        <p class="text-sm text-gray-400 text-center py-6">Belum ada jadwal fitting yang tercatat.</p>
                    @else
                        <table class="w-full text-sm">
                            <thead class="text-xs uppercase text-gray-500 bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left">Tanggal & Waktu</th>
                                    <th class="px-3 py-2 text-left">Lokasi</th>
                                    <th class="px-3 py-2 text-left">Fokus</th>
                                    <th class="px-3 py-2 text-left">Catatan</th>
                                    <th class="px-3 py-2 text-left">PIC</th>
                                    <th class="px-3 py-2 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($booking->fittings as $fitting)
                                    <tr class="hover:bg-gray-50">
                                        @php
                                            $formattedSchedule = optional($fitting->scheduled_at)
                                                ? $fitting->scheduled_at->copy()->locale(app()->getLocale())->isoFormat('dddd, D MMM Y - HH:mm')
                                                : '-';
                                        @endphp
                                        <td class="px-3 py-3 font-semibold text-gray-800">{{ $formattedSchedule }}</td>
                                        <td class="px-3 py-3 text-gray-700">{{ $fitting->location ?: 'Belum ditentukan' }}</td>
                                        <td class="px-3 py-3 text-gray-700">{{ $fitting->focus ?: '–' }}</td>
                                        <td class="px-3 py-3 text-gray-600">{{ $fitting->notes ?: '–' }}</td>
                                        <td class="px-3 py-3 text-gray-500 text-xs">{{ optional($fitting->creator)->name ?? 'Admin' }}</td>
                                        <td class="px-3 py-3 text-center">
                                            <form action="{{ route('admin.bookings.fitting.delete', [$booking->id, $fitting->id]) }}" method="POST" onsubmit="return confirm('Hapus jadwal fitting ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-semibold">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between px-5 py-4 border-b">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Wardrobe & Aksesoris</p>
                        <h4 class="text-lg font-semibold text-gray-800">Inventaris Busana untuk Booking ini</h4>
                    </div>
                    <button type="button" @click="wardrobeModal = true"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-xl border border-gray-200 text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-plus"></i> Tambah Wardrobe
                    </button>
                </div>
                <div class="p-5 overflow-x-auto">
                    @if($booking->wardrobeItems->isEmpty())
                        <p class="text-sm text-gray-400 text-center py-6">Belum ada data wardrobe yang dicatat.</p>
                    @else
                        <table class="w-full text-sm">
                            <thead class="text-xs uppercase text-gray-500 bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left">Item</th>
                                    <th class="px-3 py-2 text-left">Untuk</th>
                                    <th class="px-3 py-2 text-left">Kategori</th>
                                    <th class="px-3 py-2 text-left">Ukuran</th>
                                    <th class="px-3 py-2 text-left">Warna</th>
                                    <th class="px-3 py-2 text-left">Aksesoris</th>
                                    <th class="px-3 py-2 text-left">Catatan</th>
                                    <th class="px-3 py-2 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($booking->wardrobeItems as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-3 font-semibold text-gray-800">{{ $item->item_name }}</td>
                                        <td class="px-3 py-3 text-gray-700">{{ $item->wearer ?: '–' }}</td>
                                        <td class="px-3 py-3 text-gray-700">{{ $item->category ?: '–' }}</td>
                                        <td class="px-3 py-3 text-gray-700">{{ $item->size ?: '–' }}</td>
                                        <td class="px-3 py-3 text-gray-700">{{ $item->color ?: '–' }}</td>
                                        <td class="px-3 py-3 text-gray-600">{{ $item->accessories ?: '–' }}</td>
                                        <td class="px-3 py-3 text-gray-600">{{ $item->notes ?: '–' }}</td>
                                        <td class="px-3 py-3 text-center">
                                            <form action="{{ route('admin.bookings.wardrobe.delete', [$booking->id, $item->id]) }}" method="POST" onsubmit="return confirm('Hapus item wardrobe ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-semibold">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            {{-- Modals --}}
            <div x-show="fittingModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-black/50" @click="fittingModal = false"></div>
                <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800">Tambah Jadwal Fitting</h4>
                            <p class="text-sm text-gray-500">Isi detail jadwal fitting baru untuk klien.</p>
                        </div>
                        <button type="button" class="text-gray-400 hover:text-gray-600" @click="fittingModal = false"><i class="fas fa-times"></i></button>
                    </div>
                    <form action="{{ route('admin.bookings.fitting.store', $booking->id) }}" method="POST" class="space-y-3 text-sm">
                        @csrf
                        <div>
                            <label class="text-xs text-gray-500">Tanggal & Waktu</label>
                            <input type="datetime-local" name="scheduled_at" class="mt-1 w-full border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-yellow-400" required>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="text-xs text-gray-500">Lokasi</label>
                                <input type="text" name="location" class="mt-1 w-full border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-yellow-400" placeholder="Studio / Rumah Klien">
                            </div>
                            <div>
                                <label class="text-xs text-gray-500">Fokus</label>
                                <input type="text" name="focus" class="mt-1 w-full border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-yellow-400" placeholder="Gaun Akad, Aksesoris, dll">
                            </div>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">Catatan</label>
                            <textarea name="notes" rows="2" class="mt-1 w-full border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-yellow-400" placeholder="Detail ukuran, reminder, dsb"></textarea>
                        </div>
                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" class="px-4 py-2 text-xs font-semibold text-gray-500" @click="fittingModal = false">Batal</button>
                            <button type="submit" class="px-5 py-2 text-xs font-semibold rounded-xl gold-gradient text-white">Simpan Jadwal</button>
                        </div>
                    </form>
                </div>
            </div>

            <div x-show="wardrobeModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-black/50" @click="wardrobeModal = false"></div>
                <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800">Tambah Data Wardrobe</h4>
                            <p class="text-sm text-gray-500">Catat busana atau aksesoris yang akan digunakan.</p>
                        </div>
                        <button type="button" class="text-gray-400 hover:text-gray-600" @click="wardrobeModal = false"><i class="fas fa-times"></i></button>
                    </div>
                    <form action="{{ route('admin.bookings.wardrobe.store', $booking->id) }}" method="POST" class="space-y-3 text-sm">
                        @csrf
                        <div>
                            <label class="text-xs text-gray-500">Nama Item <span class="text-red-500">*</span></label>
                            <input type="text" name="item_name" class="mt-1 w-full border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-yellow-400" required>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="text-xs text-gray-500">Untuk/Pemakai</label>
                                <input type="text" name="wearer" class="mt-1 w-full border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-yellow-400" placeholder="Pengantin Pria / Ibu">
                            </div>
                            <div>
                                <label class="text-xs text-gray-500">Kategori</label>
                                <input type="text" name="category" class="mt-1 w-full border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-yellow-400" placeholder="Gaun, Beskap, Aksesoris">
                            </div>
                            <div>
                                <label class="text-xs text-gray-500">Ukuran</label>
                                <input type="text" name="size" class="mt-1 w-full border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-yellow-400" placeholder="M, L, custom">
                            </div>
                            <div>
                                <label class="text-xs text-gray-500">Warna</label>
                                <input type="text" name="color" class="mt-1 w-full border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-yellow-400" placeholder="Putih Gading">
                            </div>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">Aksesoris Pendukung</label>
                            <textarea name="accessories" rows="2" class="mt-1 w-full border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-yellow-400" placeholder="Selendang, Tiara, Bros"></textarea>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">Catatan</label>
                            <textarea name="notes" rows="2" class="mt-1 w-full border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-yellow-400" placeholder="Detail alterasi, jadwal pick-up"></textarea>
                        </div>
                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" class="px-4 py-2 text-xs font-semibold text-gray-500" @click="wardrobeModal = false">Batal</button>
                            <button type="submit" class="px-5 py-2 text-xs font-semibold rounded-xl gold-gradient text-white">Simpan Item</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Rundown Tab --}}
        <div x-show="activeTab === 'rundown'" class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h4 class="font-semibold text-gray-800">Rundown Acara</h4>
                <div>
                    <a href="{{ route('admin.rundown.pdf', $booking->id) }}" class="text-xs font-medium px-3 py-2 bg-red-50 text-red-600 rounded-xl hover:bg-red-100"><i class="fas fa-file-pdf mr-1"></i> Export PDF</a>
                </div>
            </div>
            <div class="overflow-x-auto mb-5">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-3 py-2 text-left font-semibold text-gray-700">Waktu</th>
                            <th class="px-3 py-2 text-left font-semibold text-gray-700">Kegiatan</th>
                            <th class="px-3 py-2 text-left font-semibold text-gray-700">PIC</th>
                            <th class="px-3 py-2 text-left font-semibold text-gray-700">Durasi</th>
                            <th class="px-3 py-2 text-left font-semibold text-gray-700">Keterangan</th>
                            <th class="px-3 py-2 text-center font-semibold text-gray-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="rundown-list">
                        @forelse($booking->rundowns->sortBy('sort_order') as $rd)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-3 py-2">
                                <form action="{{ route('admin.rundown.update', $rd->id) }}" method="POST" class="flex items-center gap-1">
                                    @csrf @method('PUT')
                                    <input type="time" name="time" value="{{ $rd->time }}" required class="border border-gray-200 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-yellow-400">
                                </form>
                            </td>
                            <td class="px-3 py-2">
                                <form action="{{ route('admin.rundown.update', $rd->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <input type="text" name="activity" value="{{ $rd->activity }}" required class="border border-gray-200 rounded px-2 py-1 text-xs w-full focus:outline-none focus:ring-1 focus:ring-yellow-400">
                                </form>
                            </td>
                            <td class="px-3 py-2">
                                <form action="{{ route('admin.rundown.update', $rd->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <input type="text" name="pic" value="{{ $rd->pic }}" placeholder="PIC" class="border border-gray-200 rounded px-2 py-1 text-xs w-full focus:outline-none focus:ring-1 focus:ring-yellow-400">
                                </form>
                            </td>
                            <td class="px-3 py-2">
                                <form action="{{ route('admin.rundown.update', $rd->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <input type="number" name="duration_minutes" value="{{ $rd->duration_minutes }}" placeholder="mnt" min="1" class="border border-gray-200 rounded px-2 py-1 text-xs w-20 focus:outline-none focus:ring-1 focus:ring-yellow-400">
                                </form>
                            </td>
                            <td class="px-3 py-2">
                                <form action="{{ route('admin.rundown.update', $rd->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <input type="text" name="notes" value="{{ $rd->notes }}" placeholder="Keterangan" class="border border-gray-200 rounded px-2 py-1 text-xs w-full focus:outline-none focus:ring-1 focus:ring-yellow-400">
                                </form>
                            </td>
                            <td class="px-3 py-2 text-center">
                                <button onclick="saveInline(this, {{ $rd->id }})" class="text-xs font-medium px-2 py-1 bg-green-50 text-green-600 rounded hover:bg-green-100 mr-1">Simpan</button>
                                <form action="{{ route('admin.rundown.destroy', $rd->id) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="text-xs text-red-400 hover:text-red-600"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-4 text-gray-400 text-sm">Belum ada rundown</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t pt-4">
                <h4 class="font-semibold text-gray-800 mb-3 text-sm">Tambah Item Rundown</h4>
                <form action="{{ route('admin.rundown.store', $booking->id) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-6 gap-3">
                    @csrf
                    <input type="time" name="time" required class="border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                    <input type="text" name="activity" placeholder="Kegiatan" required class="border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 sm:col-span-2">
                    <input type="text" name="pic" placeholder="PIC" class="border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                    <input type="number" name="duration_minutes" placeholder="Durasi (mnt)" min="1" class="border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                    <input type="text" name="notes" placeholder="Keterangan" class="border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                    <button type="submit" class="sm:col-span-6 gold-gradient text-white font-semibold py-2.5 rounded-xl text-sm">Tambah</button>
                </form>
            </div>
        </div>

        {{-- Consultation Tab --}}
        <div x-show="activeTab === 'consultation'" class="p-6">
            <div class="space-y-3">
                @forelse($booking->consultations as $c)
                <div class="p-4 border border-gray-100 rounded-xl text-sm">
                    <div class="flex items-center justify-between mb-2">
                        <p class="font-semibold text-gray-800">{{ $c->consultation_code }}</p>
                        <span class="px-2 py-0.5 rounded-full text-xs {{ $c->status==='done'?'bg-green-100 text-green-700':($c->status==='confirmed'?'bg-blue-100 text-blue-700':'bg-yellow-100 text-yellow-700') }}">{{ $c->status_label }}</span>
                    </div>
                    <p class="text-gray-500 text-xs">{{ $c->preferred_date->isoFormat('D MMM Y') }} • {{ $c->preferred_time }} • {{ ucfirst($c->consultation_type) }}</p>
                    @if($c->meeting_notes)<p class="mt-2 text-gray-600 bg-gray-50 rounded-lg p-2 text-xs">{{ $c->meeting_notes }}</p>@endif
                </div>
                @empty<p class="text-gray-400 text-sm">Belum ada konsultasi</p>
                @endforelse
            </div>
        </div>

        {{-- Financial Tab --}}
        <div x-show="activeTab === 'financial'" class="p-6">
            <div class="flex gap-4 mb-4 text-sm">
                <div class="bg-green-50 rounded-xl p-3 flex-1 text-center"><p class="text-xs text-gray-500">Pemasukan</p><p class="font-bold text-green-600">Rp {{ number_format($booking->financialTransactions->where('type','income')->sum('amount'), 0, ',', '.') }}</p></div>
                <div class="bg-red-50 rounded-xl p-3 flex-1 text-center"><p class="text-xs text-gray-500">Pengeluaran</p><p class="font-bold text-red-500">Rp {{ number_format($booking->financialTransactions->where('type','expense')->sum('amount'), 0, ',', '.') }}</p></div>
            </div>
            <div class="space-y-2">
                @forelse($booking->financialTransactions->sortByDesc('transaction_date') as $tx)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl text-sm">
                    <div><p class="font-medium text-gray-800">{{ $tx->description }}</p><p class="text-xs text-gray-500">{{ $tx->category }} • {{ $tx->transaction_date }}</p></div>
                    <span class="{{ $tx->type==='income'?'text-green-600':'text-red-500' }} font-semibold">{{ $tx->type==='income'?'+':'-' }}Rp {{ number_format($tx->amount, 0, ',', '.') }}</span>
                </div>
                @empty<p class="text-gray-400 text-sm">Belum ada transaksi</p>
                @endforelse
            </div>
        </div>

        {{-- Documents Tab --}}
        <div x-show="activeTab === 'document'" class="p-6">
            <div class="space-y-2 mb-4">
                @forelse($booking->documents as $doc)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl text-sm">
                    <div class="flex items-center gap-2"><i class="fas fa-file text-blue-400"></i><div><p class="font-medium text-gray-800">{{ $doc->name }}</p><p class="text-xs text-gray-500">{{ ucfirst($doc->category) }} • {{ number_format($doc->file_size/1024, 0) }} KB</p></div></div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs {{ $doc->is_visible_to_client?'text-green-600':'text-gray-400' }}">{{ $doc->is_visible_to_client?'Visible':'Hidden' }}</span>
                        <a href="{{ route('user.document.download', $doc->id) }}" class="text-blue-500 text-xs hover:underline"><i class="fas fa-download"></i></a>
                    </div>
                </div>
                @empty<p class="text-gray-400 text-sm">Belum ada dokumen</p>
                @endforelse
            </div>
        </div>

        {{-- Invitation Tab --}}
        <div x-show="activeTab === 'invitation'" class="p-6">
            @if($booking->invitation)
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="font-semibold text-gray-800">{{ $booking->invitation->slug }}</p>
                    <p class="text-xs text-gray-500">Status: {{ $booking->invitation->is_published ? '✅ Aktif' : '⏸ Draft' }} • {{ $booking->invitation->view_count }} views • {{ $booking->invitation->rsvps->count() }} RSVP</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('admin.invitation.client', $booking->id) }}" class="text-xs font-medium px-3 py-2 gold-gradient text-white rounded-xl">Kelola Undangan</a>
                    @if($booking->invitation->is_published)
                    <a href="{{ route('invitation.show', $booking->invitation->slug) }}" target="_blank" class="text-xs font-medium px-3 py-2 bg-blue-50 text-blue-600 rounded-xl">Buka</a>
                    @endif
                </div>
            </div>
            @else
            <p class="text-gray-400 text-sm">Belum ada undangan dibuat</p>
            @endif
        </div>
    </div>

    {{-- Extra Charge Modal --}}
    <div x-show="extraChargeOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg" @click.away="extraChargeOpen = false">
            <div class="flex items-center justify-between p-5 border-b">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Tambah Biaya Tambahan</h3>
                    <p class="text-xs text-gray-500">Catat biaya ekstra yang perlu ditagihkan ke klien.</p>
                </div>
                <button class="text-gray-400 hover:text-gray-600" @click="extraChargeOpen = false"><i class="fas fa-times"></i></button>
            </div>
            <form action="{{ route('admin.bookings.extra-charge', $booking->id) }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="text-xs font-semibold text-gray-600">Judul Biaya</label>
                    <input type="text" name="title" required class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400" placeholder="Contoh: Upgrade dekorasi">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-semibold text-gray-600">Jumlah (Rp)</label>
                        <input type="number" name="amount" required min="1000" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400" placeholder="5000000">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600">Tipe Biaya</label>
                        <select name="charge_type" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400">
                            <option value="addition" selected>Penambahan</option>
                            <option value="discount">Pengurangan</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-600">Catatan</label>
                    <textarea name="notes" rows="3" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400" placeholder="Detail tambahan biaya"></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t">
                    <button type="button" class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50" @click="extraChargeOpen = false">Batal</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl gold-gradient text-white font-semibold">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete booking modal -->
    <div x-show="deleteModal" x-cloak class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="flex items-center justify-between px-5 py-4 border-b">
                <div>
                    <p class="text-sm font-semibold text-gray-800">Hapus Booking</p>
                    <p class="text-xs text-gray-500">Tindakan ini permanen dan tidak dapat dibatalkan.</p>
                </div>
                <button type="button" class="text-gray-400 hover:text-gray-600" @click="deleteModal = false"><i class="fas fa-times"></i></button>
            </div>
            <form action="{{ route('admin.bookings.destroy', $booking->id) }}" method="POST" class="p-5 space-y-4">
                @csrf
                @method('DELETE')
                <p class="text-sm text-gray-600">Masukkan password admin Anda untuk menghapus booking <span class="font-semibold">{{ $booking->booking_code }}</span>. Pastikan status sudah dibatalkan dan seluruh data penting telah dicadangkan.</p>
                <div>
                    <label class="text-xs font-semibold text-gray-600">Password Admin</label>
                    <input type="password" name="admin_password" class="mt-1 w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-red-400" placeholder="••••••••" required>
                </div>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" class="px-4 py-2 rounded-xl border text-sm text-gray-600 hover:bg-gray-50" @click="deleteModal = false">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-sm font-semibold bg-red-600 text-white hover:bg-red-700">Hapus Booking</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Payment Modal --}}
<div id="editPaymentModal" class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Edit Pembayaran</h3>
        <form id="editPaymentForm" class="space-y-4" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="payment_id" id="editPaymentId">

            <div>
                <label class="text-xs font-medium text-gray-600">Jumlah (Rp)</label>
                <input type="number" id="editPaymentAmount" name="amount" required min="1000" class="mt-1 w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-medium text-gray-600">Tipe</label>
                    <select id="editPaymentType" name="type" class="mt-1 w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="dp">DP</option>
                        <option value="installment">Cicilan</option>
                        <option value="full">Pelunasan</option>
                        <option value="offline">Offline</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-600">Metode</label>
                    <select id="editPaymentMethod" name="method" class="mt-1 w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="cash">Cash</option>
                        <option value="transfer">Transfer</option>
                        <option value="midtrans">Midtrans</option>
                        <option value="qris">QRIS</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-medium text-gray-600">Status</label>
                    <select id="editPaymentStatus" name="status" class="mt-1 w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="pending">Pending</option>
                        <option value="success">Success</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-600">Keterangan</label>
                    <input type="text" id="editPaymentNotes" name="notes" class="mt-1 w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>

            <div>
                <label class="text-xs font-medium text-gray-600">Bukti Pembayaran (opsional)</label>
                <div class="space-y-1 mt-1 border border-dashed border-gray-300 rounded-xl p-3">
                    <input type="file" id="editPaymentProof" name="proof_attachment" accept="image/*,application/pdf" class="w-full text-xs text-gray-600">
                    <p class="text-[11px] text-gray-400">Format JPG/PNG/WEBP/PDF • maks 5MB</p>
                    <a id="currentProofLink" href="#" target="_blank" class="text-xs text-amber-600 font-semibold hover:text-amber-800 hidden">Lihat bukti saat ini</a>
                </div>
            </div>

            <div class="flex gap-2 pt-2">
                <button type="button" onclick="closeEditPaymentModal()" class="flex-1 border border-gray-200 text-gray-600 py-2 rounded-lg text-sm">Batal</button>
                <button type="button" onclick="submitEditPayment()" id="submitEditPaymentBtn" class="flex-1 gold-gradient text-white font-semibold py-2 rounded-lg text-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Admin Password Confirmation Modal --}}
<div id="adminPasswordModal" class="fixed inset-0 z-[70] bg-black/60 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
        <h3 class="font-semibold text-gray-800 mb-2" id="adminPasswordModalTitle">Verifikasi Admin</h3>
        <p class="text-sm text-gray-500 mb-4" id="adminPasswordModalDesc">Masukkan password admin untuk melanjutkan.</p>
        <div class="space-y-3">
            <div>
                <label class="text-xs font-medium text-gray-600">Password Admin</label>
                <input type="password" id="adminPasswordInput" class="mt-1 w-full border border-gray-200 rounded-lg px-3 py-2 text-sm" placeholder="Masukkan password admin">
                <p class="text-xs text-red-500 mt-1 hidden" id="adminPasswordError"></p>
            </div>
            <div class="flex gap-2 pt-2">
                <button type="button" onclick="closeAdminPasswordModal()" class="flex-1 border border-gray-200 text-gray-600 py-2 rounded-lg text-sm">Batal</button>
                <button type="button" onclick="confirmAdminPassword()" id="confirmAdminPasswordBtn" class="flex-1 gold-gradient text-white font-semibold py-2 rounded-lg text-sm">Verifikasi</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let editingPaymentId = null;
let pendingAction = null;

async function saveInline(btn, id) {
    const row = btn.closest('tr');
    const inputs = row.querySelectorAll('input, textarea');
    const formData = new FormData();
    formData.append('_method', 'PUT');
    formData.append('_token', '{{ csrf_token() }}');
    inputs.forEach(inp => {
        formData.append(inp.name, inp.value);
    });

    btn.disabled = true;
    btn.textContent = 'Menyimpan...';
    const res = await fetch(`/admin/rundown/${id}`, { method: 'POST', body: formData });
    const data = await res.json();
    if (data.success) {
        btn.textContent = 'Tersimpan';
        window.AnggitaStatusModal?.show({ type: 'success', message: 'Rundown berhasil disimpan.' });
        setTimeout(() => {
            btn.disabled = false;
            btn.textContent = 'Simpan';
        }, 1500);
    } else {
        btn.disabled = false;
        btn.textContent = 'Simpan';
        window.AnggitaStatusModal?.show({ type: 'error', message: data.error || 'Gagal menyimpan rundown.' });
    }
}

function openEditPayment(source) {
    const dataset = source?.dataset ?? {};
    const payment = {
        id: dataset.paymentId,
        amount: dataset.paymentAmount,
        type: dataset.paymentType,
        method: dataset.paymentMethod,
        status: dataset.paymentStatus,
        notes: dataset.paymentNotes,
        proof_url: dataset.paymentProof,
    };

    editingPaymentId = payment.id;
    document.getElementById('editPaymentId').value = payment.id;
    document.getElementById('editPaymentAmount').value = payment.amount || '';
    document.getElementById('editPaymentType').value = payment.type || 'dp';
    document.getElementById('editPaymentMethod').value = payment.method || 'cash';
    document.getElementById('editPaymentStatus').value = payment.status || 'pending';
    document.getElementById('editPaymentNotes').value = payment.notes || '';
    document.getElementById('editPaymentProof').value = '';

    const proofLink = document.getElementById('currentProofLink');
    if (payment.proof_url) {
        proofLink.href = payment.proof_url;
        proofLink.classList.remove('hidden');
    } else {
        proofLink.classList.add('hidden');
    }

    document.getElementById('editPaymentModal').classList.remove('hidden');
}

function closeEditPaymentModal() {
    document.getElementById('editPaymentModal').classList.add('hidden');
    editingPaymentId = null;
}

async function submitEditPayment() {
    if (!editingPaymentId) return;
    pendingAction = { type: 'update', paymentId: editingPaymentId };
    openAdminPasswordModal('Menyimpan perubahan pembayaran');
}

function confirmDeletePayment(id) {
    pendingAction = { type: 'delete', paymentId: id };
    openAdminPasswordModal('Menghapus pembayaran');
}

let vendorEditModalVisible = false;

function openEditVendor(button) {
    const form = document.getElementById('vendorEditForm');
    form.reset();
    form.dataset.updateUrl = button.dataset.updateUrl;

    document.getElementById('vendorEditCategory').value = button.dataset.vendorCategory || '';
    document.getElementById('vendorEditName').value = button.dataset.vendorName || '';
    document.getElementById('vendorEditContact').value = button.dataset.vendorContact || '';
    document.getElementById('vendorEditStatus').value = button.dataset.vendorStatus || 'assigned';
    document.getElementById('vendorEditCost').value = button.dataset.vendorCost || 0;
    document.getElementById('vendorEditNotes').value = button.dataset.vendorNotes || '';
    document.getElementById('vendorEditProof').value = '';

    const proofLink = document.getElementById('vendorCurrentProofLink');
    if (button.dataset.vendorProof) {
        proofLink.href = button.dataset.vendorProof;
        proofLink.classList.remove('hidden');
    } else {
        proofLink.classList.add('hidden');
    }

    document.getElementById('vendorEditModal').classList.remove('hidden');
    vendorEditModalVisible = true;
}

function closeVendorEditModal() {
    document.getElementById('vendorEditModal').classList.add('hidden');
    vendorEditModalVisible = false;
}

function submitEditVendor() {
    const form = document.getElementById('vendorEditForm');
    pendingAction = { type: 'vendor_update', url: form.dataset.updateUrl };
    openAdminPasswordModal('Menyimpan perubahan vendor');
}

function confirmDeleteVendor(button) {
    pendingAction = { type: 'vendor_delete', url: button.dataset.deleteUrl };
    openAdminPasswordModal('Menghapus vendor');
}

function openAdminPasswordModal(actionLabel = 'Melanjutkan aksi admin') {
    document.getElementById('adminPasswordModalTitle').textContent = actionLabel;
    document.getElementById('adminPasswordModalDesc').textContent = 'Masukkan password admin untuk melanjutkan.';
    document.getElementById('adminPasswordInput').value = '';
    document.getElementById('adminPasswordError').classList.add('hidden');
    document.getElementById('adminPasswordModal').classList.remove('hidden');
    document.getElementById('adminPasswordInput').focus();
}

function closeAdminPasswordModal(clearAction = true) {
    document.getElementById('adminPasswordModal').classList.add('hidden');
    document.getElementById('adminPasswordError').classList.add('hidden');
    if (clearAction) {
        pendingAction = null;
    }
}

async function confirmAdminPassword() {
    const passwordInput = document.getElementById('adminPasswordInput');
    const password = passwordInput.value.trim();
    const errorEl = document.getElementById('adminPasswordError');
    const btn = document.getElementById('confirmAdminPasswordBtn');

    if (!pendingAction) {
        closeAdminPasswordModal();
        return;
    }

    if (!password) {
        errorEl.textContent = 'Password admin wajib diisi.';
        errorEl.classList.remove('hidden');
        return;
    }

    errorEl.classList.add('hidden');
    btn.disabled = true;
    btn.textContent = 'Memverifikasi...';

    try {
        if (pendingAction.type === 'update') {
            const formData = new FormData(document.getElementById('editPaymentForm'));
            formData.append('admin_password', password);

            const res = await fetch(`/admin/payments/${pendingAction.paymentId}`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });
            const data = await res.json();
            if (data.success) {
                closeAdminPasswordModal();
                window.AnggitaStatusModal?.show({ type: 'success', message: 'Pembayaran berhasil diperbarui.' });
                return location.reload();
            }
            throw new Error(data.error || 'Terjadi kesalahan.');
        }

        if (pendingAction.type === 'delete') {
            const body = `_method=DELETE&_token={{ csrf_token() }}&admin_password=${encodeURIComponent(password)}`;
            const res = await fetch(`/admin/payments/${pendingAction.paymentId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body
            });
            const data = await res.json();
            if (data.success) {
                closeAdminPasswordModal();
                window.AnggitaStatusModal?.show({ type: 'success', message: 'Pembayaran berhasil dihapus.' });
                return location.reload();
            }
            throw new Error(data.error || 'Terjadi kesalahan.');
        }

        if (pendingAction.type === 'vendor_update') {
            const form = document.getElementById('vendorEditForm');
            const url = pendingAction.url;
            const formData = new FormData(form);
            formData.append('_method', 'PUT');
            formData.append('admin_password', password);

            const res = await fetch(url, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData,
            });
            const data = await res.json();
            if (data.success) {
                closeVendorEditModal();
                closeAdminPasswordModal();
                window.AnggitaStatusModal?.show({ type: 'success', message: 'Vendor berhasil diperbarui.' });
                return location.reload();
            }
            throw new Error(data.error || 'Terjadi kesalahan.');
        }

        if (pendingAction.type === 'vendor_delete') {
            const body = `_method=DELETE&_token={{ csrf_token() }}&admin_password=${encodeURIComponent(password)}`;
            const res = await fetch(pendingAction.url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body,
            });
            const data = await res.json();
            if (data.success) {
                closeAdminPasswordModal();
                window.AnggitaStatusModal?.show({ type: 'success', message: 'Vendor berhasil dihapus.' });
                return location.reload();
            }
            throw new Error(data.error || 'Terjadi kesalahan.');
        }
    } catch (error) {
        errorEl.textContent = error.message;
        errorEl.classList.remove('hidden');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Verifikasi';
    }
}
</script>
@endpush
