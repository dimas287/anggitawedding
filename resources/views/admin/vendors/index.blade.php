@extends('layouts.admin')
@section('title', 'Manajemen Vendor')
@section('page-title', 'Manajemen Vendor')

@section('content')
<div class="space-y-5"
     x-data="{
        addOpen: false,
        editOpen: false,
        vendorUpdateBase: '{{ url('/admin/vendor') }}',
        editForm: {
            action: '',
            name: '',
            category: '',
            phone: '',
            email: '',
            base_price: '',
            is_active: 1,
            description: ''
        },
        openEdit(vendor) {
            this.editForm = {
                action: `${this.vendorUpdateBase}/${vendor.id}`,
                name: vendor.name || '',
                category: vendor.category || '',
                phone: vendor.phone || '',
                email: vendor.email || '',
                base_price: vendor.base_price ?? '',
                is_active: vendor.is_active ? 1 : 0,
                description: vendor.description || ''
            };
            this.editOpen = true;
        }
     }">
    <div class="flex items-center justify-between">
        <div></div>
        <button @click="addOpen = true" class="gold-gradient text-white font-semibold px-5 py-2.5 rounded-xl text-sm hover:shadow-md">
            <i class="fas fa-plus mr-2"></i> Tambah Vendor
        </button>
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-xl shadow-sm p-4">
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama vendor..."
                   class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 flex-1">
            <select name="category" class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>
            <button type="submit" class="gold-gradient text-white font-semibold px-5 py-2.5 rounded-xl text-sm">Cari</button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50"><tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Nama Vendor</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Kategori</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Kontak</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Harga Dasar</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($vendors as $vendor)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $vendor->name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $vendor->category }}</td>
                        <td class="px-4 py-3 text-gray-600 text-xs">{{ $vendor->phone }}<br>{{ $vendor->email }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">{{ $vendor->base_price ? 'Rp '.number_format($vendor->base_price,0,',','.') : '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs {{ $vendor->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $vendor->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                <button type="button"
                                        class="w-8 h-8 rounded-full bg-yellow-50 text-yellow-600 hover:bg-yellow-100 flex items-center justify-center"
                                        @click="openEdit(@js([
                                            'id' => $vendor->id,
                                            'name' => $vendor->name,
                                            'category' => $vendor->category,
                                            'phone' => $vendor->phone,
                                            'email' => $vendor->email,
                                            'base_price' => $vendor->base_price,
                                            'is_active' => $vendor->is_active,
                                            'description' => $vendor->description,
                                        ]))">
                                    <i class="fas fa-pen text-xs"></i>
                                </button>
                                <form action="{{ route('admin.vendors.destroy', $vendor->id) }}" method="POST" onsubmit="return confirm('Hapus vendor ini?')">
                                    @csrf @method('DELETE')
                                    <button class="w-8 h-8 rounded-full bg-red-50 text-red-500 hover:bg-red-100 flex items-center justify-center">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-gray-400">Belum ada vendor</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t">{{ $vendors->withQueryString()->links() }}</div>
    </div>

    {{-- Add Vendor Modal --}}
    <div x-show="addOpen" x-cloak class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div @click.outside="addOpen = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Tambah Vendor Baru</h3>
            <form action="{{ route('admin.vendors.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <input type="text" name="name" placeholder="Nama Vendor *" required class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                <input type="text" name="category" placeholder="Kategori (Fotografer, Band, Catering, dll) *" required class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                <div class="grid grid-cols-2 gap-3">
                    <input type="tel" name="phone" placeholder="No. HP" class="border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                    <input type="email" name="email" placeholder="Email" class="border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                </div>
                <input type="number" name="base_price" placeholder="Harga Dasar (Rp)" min="0" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                <textarea name="description" rows="2" placeholder="Deskripsi" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 resize-none"></textarea>
                <div class="flex gap-2 pt-2">
                    <button type="button" @click="addOpen = false" class="flex-1 border border-gray-200 text-gray-600 py-3 rounded-xl text-sm font-medium hover:bg-gray-50">Batal</button>
                    <button type="submit" class="flex-1 gold-gradient text-white font-semibold py-3 rounded-xl text-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Vendor Modal --}}
    <div x-show="editOpen" x-cloak class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div @click.outside="editOpen = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Edit Vendor</h3>
            <form method="POST" :action="editForm.action" enctype="multipart/form-data" class="space-y-3">
                @csrf
                @method('PUT')
                <input type="text" name="name" placeholder="Nama Vendor *" required class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400" x-model="editForm.name">
                <input type="text" name="category" placeholder="Kategori *" required class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400" x-model="editForm.category">
                <div class="grid grid-cols-2 gap-3">
                    <input type="tel" name="phone" placeholder="No. HP" class="border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400" x-model="editForm.phone">
                    <input type="email" name="email" placeholder="Email" class="border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400" x-model="editForm.email">
                </div>
                <input type="number" name="base_price" placeholder="Harga Dasar (Rp)" min="0" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400" x-model="editForm.base_price">
                <textarea name="description" rows="2" placeholder="Deskripsi" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 resize-none" x-model="editForm.description"></textarea>
                <select name="is_active" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400" x-model="editForm.is_active">
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
                <div class="flex gap-2 pt-2">
                    <button type="button" @click="editOpen = false" class="flex-1 border border-gray-200 text-gray-600 py-3 rounded-xl text-sm font-medium hover:bg-gray-50">Batal</button>
                    <button type="submit" class="flex-1 gold-gradient text-white font-semibold py-3 rounded-xl text-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
