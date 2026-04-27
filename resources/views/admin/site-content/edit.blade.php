@extends('layouts.admin')
@section('title', 'Konten Landing & Footer')
@section('page-title', 'Konten Landing Page & Footer')
@section('breadcrumb', 'Dashboard / Konten Landing & Footer')

@section('content')
<div class="space-y-8">
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl p-4">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-600 text-sm rounded-xl p-4">
            <div class="font-semibold mb-2">Periksa kembali input Anda:</div>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Maintenance Mode Management --}}
    <section id="maintenance-section" class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
        <div class="p-6 border-b border-gray-50">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-tools text-purple-600"></i>
                Pusat Kontrol Maintenance
            </h3>
            <p class="text-sm text-gray-500">Kelola ketersediaan fitur dan akses website secara keseluruhan.</p>
        </div>
        <div class="p-0">
            <form action="{{ route('admin.site-content.maintenance') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2">
                    {{-- Invitation Maintenance --}}
                    <div class="p-8 border-b md:border-b-0 md:border-r border-gray-100 space-y-8 bg-white">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-purple-50 flex items-center justify-center text-purple-600 text-xl">
                                <i class="fas fa-envelope-open-text"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 leading-tight">Maintenance Undangan</h4>
                                <p class="text-[11px] text-gray-500 uppercase tracking-widest mt-0.5">Invitation Features Only</p>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div class="p-4 rounded-2xl border {{ $maintenanceMode ? 'bg-amber-50 border-amber-200' : 'bg-gray-50 border-gray-100' }} transition-colors">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-semibold text-gray-700">Status</span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="hidden" name="invitation_maintenance_mode" value="0">
                                        <input type="checkbox" name="invitation_maintenance_mode" value="1" class="sr-only peer" {{ $maintenanceMode ? 'checked' : '' }}>
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500 leading-relaxed">
                                    {{ $maintenanceMode ? 'Fitur katalog dan booking undangan sedang ditutup.' : 'Semua fitur undangan berjalan normal.' }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Pesan Pengunjung</label>
                                <textarea name="invitation_maintenance_message" rows="3" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all text-sm" placeholder="Contoh: Kami sedang mengupdate engine undangan...">{{ $maintenanceMessage }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Global Maintenance --}}
                    <div class="p-8 space-y-8 bg-gray-50/30">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-red-50 flex items-center justify-center text-red-600 text-xl">
                                <i class="fas fa-globe"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 leading-tight">Maintenance Global</h4>
                                <p class="text-[11px] text-gray-500 uppercase tracking-widest mt-0.5">Whole Site (Landing, etc)</p>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div class="p-4 rounded-2xl border {{ $globalMaintenanceMode ? 'bg-red-50 border-red-200' : 'bg-gray-50 border-gray-100' }} transition-colors">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-semibold text-gray-700">Status</span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="hidden" name="global_maintenance_mode" value="0">
                                        <input type="checkbox" name="global_maintenance_mode" value="1" class="sr-only peer" {{ $globalMaintenanceMode ? 'checked' : '' }}>
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500 leading-relaxed">
                                    {{ $globalMaintenanceMode ? 'Seluruh website saat ini tertutup untuk publik.' : 'Website dapat diakses normal oleh siapa saja.' }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Pesan Pengunjung</label>
                                <textarea name="global_maintenance_message" rows="3" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all text-sm" placeholder="Contoh: Kami sedang memindahkan server...">{{ $globalMaintenanceMessage }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6 bg-white border-t border-gray-50 flex justify-end">
                    <button type="submit" class="px-8 py-3 bg-gray-900 text-white rounded-xl text-sm font-semibold hover:bg-black transition-all shadow-xl hover:-translate-y-1 active:translate-y-0 flex items-center gap-2">
                        <i class="fas fa-save text-xs opacity-50"></i>
                        Simpan Semua Pengaturan Mode
                    </button>
                </div>
            </form>
        </div>
    </section>

    {{-- Hero Media Manager --}}
    <section id="hero-media" class="bg-white rounded-2xl shadow-sm p-6 space-y-5" x-data="heroSlideManager({
        defaults: { sort_order: {{ ($slides->max('sort_order') ?? 0) + 1 }} },
        routes: {
            store: '{{ route('admin.hero-slides.store') }}',
            update: '{{ route('admin.hero-slides.update', '__ID__') }}'
        }
    })">
        <div class="flex flex-col gap-1 md:flex-row md:items-center md:justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Hero Media</h3>
                <p class="text-sm text-gray-500">Kelola video/foto yang tampil sebagai background slideshow.</p>
            </div>
            <button @click="openCreate()" class="inline-flex items-center gap-2 gold-gradient text-white font-semibold px-4 py-2.5 rounded-xl text-sm shadow hover:shadow-lg transition-all">
                <i class="fas fa-plus"></i> Tambah Slide
            </button>
        </div>

        <div class="rounded-2xl border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs tracking-widest">
                        <tr>
                            <th class="px-6 py-3 text-left">Preview</th>
                            <th class="px-6 py-3 text-left">Konten</th>
                            <th class="px-6 py-3 text-center">Media</th>
                            <th class="px-6 py-3 text-center">Urutan</th>
                            <th class="px-6 py-3 text-center">Status</th>
                            <th class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($slides as $slide)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="w-40 rounded-xl overflow-hidden border border-gray-100 shadow-sm">
                                        @if($slide->media_type === 'video')
                                            <video src="{{ $slide->resolved_media_url }}" class="w-full h-28 object-cover" autoplay muted loop></video>
                                        @else
                                            <img src="{{ $slide->resolved_media_url }}" alt="Preview" class="w-full h-28 object-cover">
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-gray-800">{{ $slide->title ?? '—' }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $slide->subtitle ?? 'Tidak ada deskripsi tambahan' }}</p>
                                    @if($slide->cta_label && $slide->cta_url)
                                        <p class="text-xs text-yellow-600 mt-2"><i class="fas fa-link mr-1"></i>{{ $slide->cta_label }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center text-gray-700 capitalize">{{ $slide->media_type }}</td>
                                <td class="px-6 py-4 text-center text-gray-700">{{ $slide->sort_order }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $slide->is_active ? 'bg-green-50 text-green-600' : 'bg-gray-100 text-gray-500' }}">
                                        {{ $slide->is_active ? 'Aktif' : 'Arsip' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <button @click='openEdit(@json($slide))' class="inline-flex items-center gap-1 text-sm text-yellow-600 hover:text-yellow-700 font-semibold">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <form action="{{ route('admin.hero-slides.destroy', $slide) }}" method="POST" class="inline" onsubmit="return confirm('Hapus slide ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 text-sm text-red-600 hover:text-red-700 font-semibold">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500 text-sm">Belum ada slide. Klik "Tambah Slide" untuk mulai menampilkan media pada hero landing page.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal -->
        <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col" @click.away="close()">
                <div class="flex items-center justify-between p-5 border-b flex-shrink-0">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800" x-text="mode === 'create' ? 'Tambah Slide' : 'Edit Slide'"></h3>
                        <p class="text-xs text-gray-500" x-text="mode === 'create' ? 'Masukkan media baru untuk hero section.' : 'Perbarui informasi slide.'"></p>
                    </div>
                    <button class="text-gray-400 hover:text-gray-600" @click="close()"><i class="fas fa-times"></i></button>
                </div>
                <form :action="actionUrl()" method="POST" class="p-6 space-y-5 overflow-y-auto" enctype="multipart/form-data">
                    @csrf
                    <template x-if="mode === 'edit'">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-semibold text-gray-600">Judul / Tagline</label>
                            <input type="text" name="title" x-model="form.title" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400" placeholder="Highlight slide">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-600">Subjudul</label>
                            <input type="text" name="subtitle" x-model="form.subtitle" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400" placeholder="Deskripsi singkat">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-600">Tipe Media</label>
                            <select name="media_type" x-model="form.media_type" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400">
                                <option value="image">Gambar</option>
                                <option value="video">Video</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-600">Urutan Tampil</label>
                            <input type="number" min="0" name="sort_order" x-model.number="form.sort_order" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-600">CTA Label</label>
                            <input type="text" name="cta_label" x-model="form.cta_label" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400" placeholder="Contoh: Booking Sekarang">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-600">CTA URL</label>
                            <input type="url" name="cta_url" x-model="form.cta_url" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400" placeholder="https://...">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-semibold text-gray-600">Upload Media</label>
                            <input type="file" name="media_upload" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400">
                            <p class="text-[11px] text-gray-500 mt-1">Gambar: JPG/PNG/WEBP (maks 5MB) • Video: MP4/WEBM (maks 50MB). Kosongkan jika ingin memakai URL.</p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-600">Atau URL Media</label>
                            <input type="url" name="media_url" x-model="form.media_url" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400" placeholder="https://...">
                            <p class="text-[11px] text-gray-500 mt-1">Gunakan URL jika memakai video hosted (YouTube tidak didukung, gunakan file MP4 langsung).</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="hidden" name="is_active" value="0">
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" name="is_active" value="1" :checked="Boolean(form.is_active)" class="rounded border-gray-300 text-yellow-500 focus:ring-yellow-400">
                            Tampilkan slide ini
                        </label>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <button type="button" class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50" @click="close()">Batal</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl gold-gradient text-white font-semibold">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    {{-- Brand & Identity --}}
    @php
        $logoMainUrl = !empty($brand['logo_main']) ? asset('storage/'.$brand['logo_main']) : null;
        $logoLightUrl = !empty($brand['logo_light']) ? asset('storage/'.$brand['logo_light']) : null;
        $logoIconUrl = !empty($brand['logo_icon']) ? asset('storage/'.$brand['logo_icon']) : null;
    @endphp
    <form id="brand" action="{{ route('admin.site-content.brand') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-sm p-6 space-y-5">
        @csrf
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Brand & Logo</h3>
                <p class="text-sm text-gray-500">Atur nama brand, tagline, serta logo utama/varian untuk digunakan di seluruh situs.</p>
            </div>
            <button type="submit" class="px-5 py-2.5 rounded-xl gold-gradient text-white text-sm font-semibold">Simpan Branding</button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="text-xs font-semibold text-gray-600">Nama Brand</label>
                <input type="text" name="brand_name" value="{{ old('brand_name', $brand['brand_name'] ?? 'Anggita') }}" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400" placeholder="Anggita">
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600">Tagline</label>
                <input type="text" name="tagline" value="{{ old('tagline', $brand['tagline'] ?? 'Wedding Organizer') }}" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400" placeholder="Wedding Organizer">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div>
                <label class="text-xs font-semibold text-gray-600 flex items-center justify-between">
                    Logo Utama (background terang)
                    @if($logoMainUrl)
                        <a href="{{ $logoMainUrl }}" target="_blank" class="text-[11px] text-yellow-600">Lihat</a>
                    @endif
                </label>
                <input type="file" name="logo_main" accept="image/*" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400">
                <p class="text-[11px] text-gray-500 mt-1">PNG/WEBP transparan, max 2MB.</p>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600 flex items-center justify-between">
                    Logo Versi Light (untuk background gelap)
                    @if($logoLightUrl)
                        <a href="{{ $logoLightUrl }}" target="_blank" class="text-[11px] text-yellow-600">Lihat</a>
                    @endif
                </label>
                <input type="file" name="logo_light" accept="image/*" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400">
                <p class="text-[11px] text-gray-500 mt-1">PNG/WEBP putih, max 2MB.</p>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600 flex items-center justify-between">
                    Ikon / Favicon
                    @if($logoIconUrl)
                        <a href="{{ $logoIconUrl }}" target="_blank" class="text-[11px] text-yellow-600">Lihat</a>
                    @endif
                </label>
                <input type="file" name="logo_icon" accept="image/*" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400">
                <p class="text-[11px] text-gray-500 mt-1">PNG 1:1, max 1MB.</p>
            </div>
        </div>

        <div class="bg-gray-50 rounded-2xl p-4 grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
            <div class="flex flex-col items-center gap-2">
                <div class="w-28 h-28 rounded-2xl border border-dashed border-gray-300 flex items-center justify-center bg-white">
                    @if($logoMainUrl)
                        <img src="{{ $logoMainUrl }}" alt="Logo" class="max-w-full max-h-full object-contain">
                    @else
                        <span class="text-xs text-gray-400">Belum ada logo</span>
                    @endif
                </div>
                <p class="text-xs text-gray-500">Preview background terang</p>
            </div>
            <div class="flex flex-col items-center gap-2">
                <div class="w-28 h-28 rounded-2xl border border-dashed border-gray-300 flex items-center justify-center bg-gray-900">
                    @if($logoLightUrl || $logoMainUrl)
                        <img src="{{ $logoLightUrl ?? $logoMainUrl }}" alt="Logo Light" class="max-w-full max-h-full object-contain">
                    @else
                        <span class="text-xs text-gray-400">Belum ada logo</span>
                    @endif
                </div>
                <p class="text-xs text-gray-500">Preview background gelap</p>
            </div>
            <div class="flex flex-col items-center gap-2">
                <div class="w-20 h-20 rounded-full border border-dashed border-gray-300 flex items-center justify-center bg-white">
                    @if($logoIconUrl)
                        <img src="{{ $logoIconUrl }}" alt="Ikon" class="max-w-full max-h-full object-contain">
                    @else
                        <span class="text-[10px] text-gray-400 text-center">Icon</span>
                    @endif
                </div>
                <p class="text-xs text-gray-500">Ikon aplikasi / favicon</p>
            </div>
        </div>
    </form>

    {{-- Hero Copy --}}
    <form id="hero-copy" action="{{ route('admin.site-content.hero') }}" method="POST" class="bg-white rounded-2xl shadow-sm p-6 space-y-5">
        @csrf
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Hero Section Copy & CTA</h3>
                <p class="text-sm text-gray-500">Atur badge, deskripsi fallback, dan CTA yang ditampilkan.</p>
            </div>
            <button type="submit" class="px-5 py-2.5 rounded-xl gold-gradient text-white text-sm font-semibold">Simpan Hero Copy</button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="text-xs font-semibold text-gray-600">Badge</label>
                <input type="text" name="hero[badge]" value="{{ old('hero.badge', $hero['badge'] ?? '') }}" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400">
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600">Subjudul fallback</label>
                <input type="text" name="hero[fallback_subtitle]" value="{{ old('hero.fallback_subtitle', $hero['fallback_subtitle'] ?? '') }}" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400">
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600">CTA Primer - Label</label>
                <input type="text" name="hero[primary_cta_label]" value="{{ old('hero.primary_cta_label', $hero['primary_cta_label'] ?? '') }}" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400">
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600">CTA Primer - URL</label>
                <input type="url" name="hero[primary_cta_url]" value="{{ old('hero.primary_cta_url', $hero['primary_cta_url'] ?? '') }}" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400">
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600">CTA Sekunder - Label</label>
                <input type="text" name="hero[secondary_cta_label]" value="{{ old('hero.secondary_cta_label', $hero['secondary_cta_label'] ?? '') }}" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400">
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600">CTA Sekunder - URL</label>
                <input type="url" name="hero[secondary_cta_url]" value="{{ old('hero.secondary_cta_url', $hero['secondary_cta_url'] ?? '') }}" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400">
            </div>
        </div>
    </form>

    {{-- Dream Section --}}
    @php
        $dreamHighlights = old('dream.highlights', $dream['highlights'] ?? []);
    @endphp
    <form id="dream-section" action="{{ route('admin.site-content.dream') }}" method="POST" enctype="multipart/form-data"
          class="bg-white rounded-2xl shadow-sm p-6 space-y-6"
          x-data="dreamManager({ highlights: $el.dataset.highlights ? JSON.parse($el.dataset.highlights) : [] })"
          data-highlights='@json($dreamHighlights)'>
        @csrf
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Section "Wujudkan Pernikahan"</h3>
                <p class="text-sm text-gray-500">Atur heading, deskripsi, CTA, highlight, dan media.</p>
            </div>
            <button type="submit" class="px-5 py-2.5 rounded-xl gold-gradient text-white text-sm font-semibold">Simpan Section</button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="text-xs font-semibold text-gray-600">Eyebrow / Label kecil</label>
                <input type="text" name="dream[eyebrow]" value="{{ old('dream.eyebrow', $dream['eyebrow'] ?? '') }}" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400">
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600">Judul utama</label>
                <input type="text" name="dream[heading]" value="{{ old('dream.heading', $dream['heading'] ?? '') }}" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400">
            </div>
        </div>
        <div>
            <label class="text-xs font-semibold text-gray-600">Deskripsi</label>
            <textarea name="dream[description]" class="mt-1 w-full border rounded-2xl px-4 py-3 focus:ring-2 focus:ring-yellow-400" rows="3">{{ old('dream.description', $dream['description'] ?? '') }}</textarea>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="text-xs font-semibold text-gray-600">CTA Primer Label</label>
                <input type="text" name="dream[primary_cta_label]" value="{{ old('dream.primary_cta_label', $dream['primary_cta_label'] ?? '') }}" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400">
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600">CTA Primer URL</label>
                <input type="text" name="dream[primary_cta_url]" value="{{ old('dream.primary_cta_url', $dream['primary_cta_url'] ?? '') }}" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400">
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600">CTA Sekunder Label</label>
                <input type="text" name="dream[secondary_cta_label]" value="{{ old('dream.secondary_cta_label', $dream['secondary_cta_label'] ?? '') }}" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400">
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600">CTA Sekunder URL</label>
                <input type="text" name="dream[secondary_cta_url]" value="{{ old('dream.secondary_cta_url', $dream['secondary_cta_url'] ?? '') }}" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400">
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="text-xs font-semibold text-gray-600">URL Gambar Section</label>
                <input type="url" name="dream[hero_image]" value="{{ old('dream.hero_image', $dream['hero_image'] ?? '') }}" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400">
                <p class="text-[11px] text-gray-500 mt-1">Atau upload file gambar di bawah ini.</p>
                <input type="file" name="dream[hero_image_file]" accept="image/*" class="mt-2 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400">
                @if(!empty($dream['hero_image']))
                    <p class="text-[11px] text-yellow-600 mt-1"><a href="{{ $dream['hero_image'] }}" target="_blank">Lihat gambar saat ini</a></p>
                @endif
            </div>
            <div class="grid grid-cols-1 gap-3">
                <label class="text-xs font-semibold text-gray-600">Highlight Card</label>
                <input type="text" name="dream[highlight_card][title]" value="{{ old('dream.highlight_card.title', $dream['highlight_card']['title'] ?? '') }}" placeholder="Judul" class="w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400">
                <input type="text" name="dream[highlight_card][subtitle]" value="{{ old('dream.highlight_card.subtitle', $dream['highlight_card']['subtitle'] ?? '') }}" placeholder="Subjudul" class="w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400">
                <input type="text" name="dream[highlight_card][quote]" value="{{ old('dream.highlight_card.quote', $dream['highlight_card']['quote'] ?? '') }}" placeholder="Quote" class="w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400">
            </div>
        </div>

        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h4 class="text-sm font-semibold text-gray-700">Highlight Items</h4>
                <button type="button" class="text-xs font-semibold text-yellow-600" @click="addHighlight()"><i class="fas fa-plus mr-1"></i>Tambah highlight</button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <template x-for="(item, index) in highlights" :key="index">
                    <div class="border rounded-2xl p-4 space-y-3 relative">
                        <button type="button" class="absolute top-2 right-2 text-gray-400 hover:text-red-500" @click="removeHighlight(index)"><i class="fas fa-times"></i></button>
                        <div>
                            <label class="text-[11px] font-semibold text-gray-500 flex items-center justify-between">
                                Ikon
                                <template x-if="item.icon">
                                    <i :class="`fas ${item.icon} text-yellow-600`" class="text-xs"></i>
                                </template>
                            </label>
                            <select class="mt-1 w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400" 
                                    :name="`dream[highlights][${index}][icon]`" 
                                    x-model="item.icon">
                                <option value="fa-heart">❤️ Heart</option>
                                <option value="fa-ring">💍 Ring</option>
                                <option value="fa-calendar-check">📅 Calendar</option>
                                <option value="fa-envelope-open-text">✉️ Envelope</option>
                                <option value="fa-camera">📸 Camera</option>
                                <option value="fa-video">🎥 Video</option>
                                <option value="fa-map-marked-alt">📍 Map / Location</option>
                                <option value="fa-user-friends">👥 Guests / Couple</option>
                                <option value="fa-music">🎵 Music</option>
                                <option value="fa-birthday-cake">🎂 Cake</option>
                                <option value="fa-glass-cheers">🥂 Cheers</option>
                                <option value="fa-star">⭐ Star</option>
                                <option value="fa-gem">💎 Gem</option>
                                <option value="fa-crown">👑 Crown</option>
                                <option value="fa-gift">🎁 Gift</option>
                                <option value="fa-church">⛪ Church / Venue</option>
                                <option value="fa-home">🏠 Home / Stay</option>
                                <option value="fa-utensils">🍴 Catering / Food</option>
                                <option value="fa-magic">✨ Magic / Event</option>
                                <option value="fa-clock">⏰ Time</option>
                                <option value="fa-dove">🕊️ Dove / Peace</option>
                                <option value="fa-check-circle">✅ Accomplished</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[11px] font-semibold text-gray-500">Judul</label>
                            <input type="text" class="mt-1 w-full border rounded-xl px-3 py-2 text-sm" :name="`dream[highlights][${index}][title]`" x-model="item.title">
                        </div>
                        <div>
                            <label class="text-[11px] font-semibold text-gray-500">Deskripsi</label>
                            <textarea class="mt-1 w-full border rounded-xl px-3 py-2 text-sm" rows="2" :name="`dream[highlights][${index}][desc]`" x-model="item.desc"></textarea>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </form>

    {{-- Highlight Media Cards --}}
    <section id="highlight-cards" class="bg-white rounded-2xl shadow-sm p-6 space-y-5" x-data="highlightCardManager({
        routes: {
            store: '{{ route('admin.site-content.highlight-cards.store') }}',
            update: '{{ route('admin.site-content.highlight-cards.update', '__ID__') }}'
        },
        defaults: { sort_order: {{ ($highlightCards->max('sort_order') ?? 0) + 1 }} }
    })">
        <div class="flex flex-col gap-1 md:flex-row md:items-center md:justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-layer-group text-yellow-600 mr-1"></i>
                    Highlight Media Cards
                </h3>
                <p class="text-sm text-gray-500">Kartu foto pasangan yang muncul bertumpuk saat pengunjung scroll di landing page. Min. 2 kartu untuk efek scroll-pin.</p>
            </div>
            <button @click="openCreate()" class="inline-flex items-center gap-2 gold-gradient text-white font-semibold px-4 py-2.5 rounded-xl text-sm shadow hover:shadow-lg transition-all">
                <i class="fas fa-plus"></i> Tambah Kartu
            </button>
        </div>

        <div class="rounded-2xl border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs tracking-widest">
                        <tr>
                            <th class="px-6 py-3 text-left">Preview</th>
                            <th class="px-6 py-3 text-left">Info</th>
                            <th class="px-6 py-3 text-center">Urutan</th>
                            <th class="px-6 py-3 text-center">Status</th>
                            <th class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($highlightCards as $card)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="w-32 h-20 rounded-xl overflow-hidden border border-gray-100 shadow-sm bg-gray-100">
                                        @if($card->resolved_image_url)
                                            <img src="{{ $card->resolved_image_url }}" alt="{{ $card->title }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-xs text-gray-400"><i class="fas fa-image text-xl"></i></div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-gray-800">{{ $card->title }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $card->subtitle ?? '—' }}</p>
                                    @if($card->quote)
                                        <p class="text-xs text-yellow-600 mt-1"><i class="fas fa-star mr-1"></i>{{ Str::limit($card->quote, 50) }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center text-gray-700">{{ $card->sort_order }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $card->is_active ? 'bg-green-50 text-green-600' : 'bg-gray-100 text-gray-500' }}">
                                        {{ $card->is_active ? 'Aktif' : 'Arsip' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <button @click='openEdit(@json($card))' class="inline-flex items-center gap-1 text-sm text-yellow-600 hover:text-yellow-700 font-semibold">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <form action="{{ route('admin.site-content.highlight-cards.destroy', $card) }}" method="POST" class="inline" onsubmit="return confirm('Hapus kartu ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 text-sm text-red-600 hover:text-red-700 font-semibold">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500 text-sm">
                                    <i class="fas fa-layer-group text-3xl text-gray-300 mb-3 block"></i>
                                    Belum ada highlight card. Tambahkan minimal 2 kartu untuk mengaktifkan efek scroll bertumpuk.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Create/Edit -->
        <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col" @click.away="close()">
                <div class="flex items-center justify-between p-5 border-b flex-shrink-0">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800" x-text="mode === 'create' ? 'Tambah Highlight Card' : 'Edit Highlight Card'"></h3>
                        <p class="text-xs text-gray-500">Upload foto pasangan dengan info singkat.</p>
                    </div>
                    <button class="text-gray-400 hover:text-gray-600" @click="close()"><i class="fas fa-times"></i></button>
                </div>
                <form :action="actionUrl()" method="POST" class="p-6 space-y-5 overflow-y-auto" enctype="multipart/form-data">
                    @csrf
                    <template x-if="mode === 'edit'">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-semibold text-gray-600">Nama Pasangan / Judul *</label>
                            <input type="text" name="title" x-model="form.title" required class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400" placeholder="Anisa & Rizky">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-600">Venue / Detail</label>
                            <input type="text" name="subtitle" x-model="form.subtitle" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400" placeholder="The Grand Ballroom • Surabaya">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-600">Quote / Testimoni Singkat</label>
                            <input type="text" name="quote" x-model="form.quote" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400" placeholder="&quot;Hari terbaik dalam hidup kami&quot;">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-600">Urutan Tampil</label>
                            <input type="number" min="0" name="sort_order" x-model.number="form.sort_order" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-semibold text-gray-600">Upload Foto</label>
                            <input type="file" name="image_upload" accept="image/*" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400">
                            <p class="text-[11px] text-gray-500 mt-1">JPG/PNG/WEBP maks 5MB. Portrait disarankan.</p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-600">Atau URL Gambar</label>
                            <input type="url" name="image_url" x-model="form.image_url" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400" placeholder="https://...">
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="hidden" name="is_active" value="0">
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" name="is_active" value="1" :checked="Boolean(form.is_active)" class="rounded border-gray-300 text-yellow-500 focus:ring-yellow-400">
                            Tampilkan kartu ini
                        </label>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <button type="button" class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50" @click="close()">Batal</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl gold-gradient text-white font-semibold">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    {{-- Stats --}}
    <form id="landing-stats" action="{{ route('admin.site-content.stats') }}" method="POST" class="bg-white rounded-2xl shadow-sm p-6 space-y-5">
        @csrf
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Statistik Landing</h3>
                <p class="text-sm text-gray-500">Tampilkan angka performa landing page.</p>
            </div>
            <button type="submit" class="px-5 py-2.5 rounded-xl gold-gradient text-white text-sm font-semibold">Simpan Statistik</button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @foreach(['events' => 'Event Sukses', 'clients' => 'Pasangan Bahagia', 'templates' => 'Template Undangan', 'years' => 'Tahun Pengalaman'] as $key => $label)
                <div>
                    <label class="text-xs font-semibold text-gray-600">{{ $label }}</label>
                    <input type="number" min="0" name="stats[{{ $key }}]" value="{{ old("stats.$key", $stats[$key] ?? 0) }}" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400">
                </div>
            @endforeach
        </div>
    </form>

    {{-- Portfolio Stats --}}
    <form id="portfolio-stats" action="{{ route('admin.site-content.portfolio-stats') }}" method="POST" class="bg-white rounded-2xl shadow-sm p-6 space-y-5">
        @csrf
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Statistik Portofolio</h3>
                <p class="text-sm text-gray-500">Atur angka seperti vendor terpercaya dan rating rata-rata.</p>
            </div>
            <button type="submit" class="px-5 py-2.5 rounded-xl gold-gradient text-white text-sm font-semibold">Simpan Statistik</button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @foreach($portfolioStats as $key => $stat)
                <div class="border border-gray-100 rounded-2xl p-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-[0.2em]">{{ $stat['label'] }}</span>
                        <span class="text-[11px] text-gray-400">{{ \Illuminate\Support\Str::title(str_replace('_', ' ', $key)) }}</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs font-semibold text-gray-600">Label</label>
                            <input type="text" name="stats[{{ $key }}][label]" value="{{ old("stats.$key.label", $stat['label']) }}" class="mt-1 w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-600">Nilai</label>
                            <input type="number" step="0.1" min="0" name="stats[{{ $key }}][value]" value="{{ old("stats.$key.value", $stat['value']) }}" class="mt-1 w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs font-semibold text-gray-600">Suffix</label>
                            <input type="text" name="stats[{{ $key }}][suffix]" value="{{ old("stats.$key.suffix", $stat['suffix'] ?? '') }}" class="mt-1 w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400" maxlength="5">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-600">Desimal</label>
                            <input type="number" min="0" max="2" name="stats[{{ $key }}][decimals]" value="{{ old("stats.$key.decimals", $stat['decimals'] ?? 0) }}" class="mt-1 w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </form>

    {{-- Footer --}}
    <form id="footer" action="{{ route('admin.site-content.footer') }}" method="POST" class="bg-white rounded-2xl shadow-sm p-6 space-y-5">
        @csrf
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Footer</h3>
                <p class="text-sm text-gray-500">Atur deskripsi, alamat, kontak & sosial media.</p>
            </div>
            <button type="submit" class="px-5 py-2.5 rounded-xl gold-gradient text-white text-sm font-semibold">Simpan Footer</button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="text-xs font-semibold text-gray-600">Deskripsi</label>
                <textarea name="footer[description]" class="mt-1 w-full border rounded-2xl px-4 py-3 focus:ring-2 focus:ring-yellow-400" rows="3">{{ old('footer.description', $footer['description'] ?? '') }}</textarea>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600">Alamat</label>
                <textarea name="footer[address]" class="mt-1 w-full border rounded-2xl px-4 py-3 focus:ring-2 focus:ring-yellow-400" rows="3">{{ old('footer.address', $footer['address'] ?? '') }}</textarea>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600">Link Google Maps / Alamat</label>
                <input type="url" name="footer[address_url]" value="{{ old('footer.address_url', $footer['address_url'] ?? '') }}" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400" placeholder="https://maps.google.com/...">
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600">Email</label>
                <input type="email" name="footer[email]" value="{{ old('footer.email', $footer['email'] ?? '') }}" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400">
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600">Nomor WhatsApp (display)</label>
                <input type="text" name="footer[phone_display]" value="{{ old('footer.phone_display', $footer['phone_display'] ?? '') }}" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400">
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600">Link WhatsApp / Call</label>
                <input type="url" name="footer[phone_link]" value="{{ old('footer.phone_link', $footer['phone_link'] ?? '') }}" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400">
            </div>
            <div class="grid grid-cols-2 gap-4">
                @foreach(['instagram' => 'Instagram', 'whatsapp' => 'WhatsApp', 'facebook' => 'Facebook', 'tiktok' => 'TikTok'] as $key => $label)
                    <div>
                        <label class="text-xs font-semibold text-gray-600">{{ $label }} URL</label>
                        <input type="url" name="footer[socials][{{ $key }}]" value="{{ old("footer.socials.$key", $footer['socials'][$key] ?? '') }}" class="mt-1 w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400">
                    </div>
                @endforeach
            </div>
        </div>
    </form>

    {{-- Consultation Notification Settings --}}
    <form id="consultation-settings" action="{{ route('admin.site-content.consultation') }}" method="POST" class="bg-white rounded-2xl shadow-sm p-6 space-y-5">
        @csrf
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Notifikasi Konsultasi</h3>
                <p class="text-sm text-gray-500">Atur email penerima notifikasi ketika ada konsultasi baru dari klien.</p>
            </div>
            <button type="submit" class="px-5 py-2.5 rounded-xl gold-gradient text-white text-sm font-semibold">Simpan Pengaturan</button>
        </div>

        <div class="grid grid-cols-1 gap-5">
            <div>
                <label class="text-xs font-semibold text-gray-600 flex items-center justify-between">
                    Email Penerima Admin
                    <span class="text-[11px] text-gray-400">Pisahkan dengan koma untuk banyak penerima</span>
                </label>
                <input type="text" name="consultation_settings[admin_email]" value="{{ old('consultation_settings.admin_email', $consultationSettings['admin_email'] ?? '') }}"
                       class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400"
                       placeholder="admin1@domain.com, admin2@domain.com">
            </div>
        </div>

        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 text-sm text-amber-700">
            <p><strong>Catatan:</strong> Email ini akan menerima notifikasi setiap kali ada permintaan konsultasi baru dan perlu melakukan konfirmasi secara manual.</p>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function heroSlideManager(config) {
    return {
        modalOpen: false,
        mode: 'create',
        routes: config.routes,
        defaults: {
            id: null,
            title: '',
            subtitle: '',
            media_type: 'image',
            media_url: '',
            sort_order: config.defaults.sort_order || 0,
            cta_label: '',
            cta_url: '',
            is_active: true,
        },
        form: {},
        openCreate() {
            this.mode = 'create';
            this.form = Object.assign({}, this.defaults);
            this.form.sort_order = config.defaults.sort_order || 0;
            this.modalOpen = true;
        },
        openEdit(slide) {
            this.mode = 'edit';
            this.form = {
                id: slide.id,
                title: slide.title ?? '',
                subtitle: slide.subtitle ?? '',
                media_type: slide.media_type ?? 'image',
                media_url: slide.media_url ?? '',
                sort_order: slide.sort_order ?? 0,
                cta_label: slide.cta_label ?? '',
                cta_url: slide.cta_url ?? '',
                is_active: slide.is_active,
            };
            this.modalOpen = true;
        },
        close() {
            this.modalOpen = false;
            this.mode = 'create';
        },
        actionUrl() {
            if (this.mode === 'edit' && this.form.id) {
                return this.routes.update.replace('__ID__', this.form.id);
            }
            return this.routes.store;
        }
    };
}

function dreamManager(config) {
    return {
        highlights: config.highlights.length ? config.highlights : [{ icon: 'fa-heart', title: '', desc: '' }],
        addHighlight() {
            this.highlights.push({ icon: 'fa-heart', title: '', desc: '' });
        },
        removeHighlight(index) {
            if (this.highlights.length === 1) return;
            this.highlights.splice(index, 1);
        }
    };
}

function highlightCardManager(config) {
    return {
        modalOpen: false,
        mode: 'create',
        routes: config.routes,
        defaults: {
            id: null,
            title: '',
            subtitle: '',
            quote: '',
            image_url: '',
            sort_order: config.defaults.sort_order || 0,
            is_active: true,
        },
        form: {},
        openCreate() {
            this.mode = 'create';
            this.form = Object.assign({}, this.defaults);
            this.modalOpen = true;
        },
        openEdit(card) {
            this.mode = 'edit';
            this.form = {
                id: card.id,
                title: card.title ?? '',
                subtitle: card.subtitle ?? '',
                quote: card.quote ?? '',
                image_url: card.image_url ?? '',
                sort_order: card.sort_order ?? 0,
                is_active: card.is_active,
            };
            this.modalOpen = true;
        },
        close() {
            this.modalOpen = false;
            this.mode = 'create';
        },
        actionUrl() {
            if (this.mode === 'edit' && this.form.id) {
                return this.routes.update.replace('__ID__', this.form.id);
            }
            return this.routes.store;
        }
    };
}

document.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    const targetId = params.get('section');
    if (!targetId) return;
    const section = document.getElementById(targetId);
    if (section) {
        section.scrollIntoView({ behavior: 'smooth', block: 'start' });
        section.classList.add('ring-2', 'ring-yellow-400', 'ring-offset-2', 'ring-offset-gray-50');
        setTimeout(() => {
            section.classList.remove('ring-2', 'ring-yellow-400', 'ring-offset-2', 'ring-offset-gray-50');
        }, 2000);
    }
});
</script>
@endpush
