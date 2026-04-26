@extends('layouts.admin')
@section('title', 'Paket Wedding')
@section('page-title', 'Paket Wedding')
@section('breadcrumb', 'Dashboard / Paket Wedding')

@section('content')
@if($errors->any())
    <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
        <div class="font-semibold mb-1">Terjadi kesalahan:</div>
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@php
    $tiers = ['silver' => 'Silver', 'gold' => 'Gold', 'premium' => 'Premium', 'platinum' => 'Platinum'];
    $categories = [
        'rumahan' => 'Paket Rumahan',
        'gedung' => 'Paket Gedung',
        'intimate' => 'Intimate Wedding',
        'rias' => 'Paket Rias & Wisuda',
        'lainnya' => 'Paket Lainnya',
    ];
    $maxMedia = \App\Models\PackageMediaItem::MAX_ITEMS_PER_PACKAGE;
@endphp

<div class="space-y-6" x-data="packageForm()" x-init="init()">
    <div class="bg-white rounded-xl shadow-sm p-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Kelola Paket Wedding</h3>
            <p class="text-sm text-gray-500">Tambah, ubah, atau arsipkan paket sesuai kebutuhan bisnis Anda.</p>
        </div>
        <button @click="openCreate(@js(['sort_order' => $nextSortOrder, 'is_active' => true, 'category' => 'rumahan', 'includes_digital_invitation' => true]))"
                class="inline-flex items-center justify-center gap-2 gold-gradient text-white font-semibold px-5 py-3 rounded-xl text-sm shadow hover:shadow-lg transition-all">
            <i class="fas fa-plus"></i> Tambah Paket
        </button>
    </div>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="px-6 py-3 text-left">Paket</th>
                        <th class="px-6 py-3 text-right">Harga</th>
                        <th class="px-6 py-3 text-center">Status</th>
                        <th class="px-6 py-3 text-center">Booking</th>
                        <th class="px-6 py-3 text-center">Urutan</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($packages as $package)
                        @php $isPopular = isset($popularPackageId) && $package->id === $popularPackageId; @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-gray-800">{{ $package->name }}</span>
                                        @if($isPopular)
                                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-700">Paling Populer</span>
                                        @endif
                                        @if(!$package->is_active)
                                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-gray-100 text-gray-500">Nonaktif</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        Tier: {{ $package->tier ? ucfirst($package->tier) : '—' }} • {{ count($package->feature_items) }} fitur
                                    </p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right font-semibold text-gray-800">
                                @if($package->hasActivePromo())
                                    <div class="flex flex-col items-end gap-1">
                                        <span class="text-xs uppercase font-semibold text-pink-600">{{ $package->promo_label }}</span>
                                        <div class="text-gray-400 line-through text-xs">{{ $package->formatted_price }}</div>
                                        <div class="text-lg text-yellow-600">{{ $package->formattedEffectivePrice }}</div>
                                    </div>
                                @else
                                    {{ $package->formatted_price }}
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $package->is_active ? 'bg-green-50 text-green-600' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $package->is_active ? 'Aktif' : 'Arsip' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center text-gray-700">{{ $package->popular_score ?? 0 }}</td>
                            <td class="px-6 py-4 text-center text-gray-700">{{ $package->sort_order }}</td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('admin.packages.poster', $package) }}" target="_blank"
                                   title="Unduh Poster Paket"
                                   class="inline-flex items-center gap-1 text-sm text-purple-600 hover:text-purple-700 font-semibold">
                                    <i class="fas fa-file-image"></i> Poster
                                </a>
                                <button @click="openEdit(@js($package))" class="inline-flex items-center gap-1 text-sm text-yellow-600 hover:text-yellow-700 font-semibold">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <form action="{{ route('admin.packages.destroy', $package) }}" method="POST" class="inline" onsubmit="return confirm('Hapus paket ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1 text-sm text-red-600 hover:text-red-700 font-semibold"
                                        @disabled($package->bookings_count > 0)
                                    >
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500 text-sm">Belum ada paket. Tambahkan paket baru untuk memulai.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div x-show="isOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl" @click.away="close()">
            <div class="flex items-center justify-between p-4 border-b">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800" x-text="form.id ? 'Edit Paket' : 'Tambah Paket'"></h3>
                    <p class="text-xs text-gray-500" x-text="form.id ? 'Perbarui informasi paket' : 'Masukkan detail paket baru'"></p>
                </div>
                <button class="text-gray-400 hover:text-gray-600" @click="close()"><i class="fas fa-times"></i></button>
            </div>
            <form :action="form.id ? routes.update.replace('PK_ID', form.id) : routes.store" method="POST" enctype="multipart/form-data" class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                @csrf
                <template x-if="form.id">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-semibold text-gray-600">Nama Paket</label>
                        <input type="text" name="name" x-model="form.name" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400" required>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600">Slug</label>
                        <input type="text" name="slug" x-model="form.slug" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400" placeholder="opsional">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600">Tier</label>
                        <select name="tier" x-model="form.tier" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400">
                            <option value="">Pilih tier...</option>
                            @foreach($tiers as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600">Kategori</label>
                        <select name="category" x-model="form.category" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400">
                            @foreach($categories as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600">Harga</label>
                        <input type="number" min="0" name="price" x-model="form.price" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400" required>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600">Urutan Tampil</label>
                        <input type="number" min="0" name="sort_order" x-model="form.sort_order" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400">
                    </div>
                    <div class="flex items-center gap-3 mt-7">
                        <div>
                            <label class="text-xs font-semibold text-gray-600">Undangan Digital</label>
                            <p class="text-[11px] text-gray-400">Tentukan apakah paket sudah include undangan digital</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" :checked="form.includes_digital_invitation ?? true" @change="form.includes_digital_invitation = $event.target.checked" class="rounded border-gray-300 text-yellow-500 focus:ring-yellow-400">
                            <span class="text-sm text-gray-700">Include</span>
                        </div>
                        <input type="hidden" name="includes_digital_invitation" :value="form.includes_digital_invitation ? 1 : 0">
                    </div>
                    <div class="flex items-center gap-2 mt-6">
                        <label class="text-xs font-semibold text-gray-600">Status</label>
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                            <input type="hidden" name="is_active_present" value="1">
                            <input type="checkbox" name="is_active" value="1" :checked="Boolean(form.is_active)" class="rounded border-gray-300 text-yellow-500 focus:ring-yellow-400">
                            Aktif
                        </label>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-600">Deskripsi Singkat</label>
                    <textarea name="description" rows="3" x-model="form.description" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400" placeholder="Ceritakan highlight paket" required></textarea>
                </div>

                <div x-data="featureBuilder(form)">
                    <label class="text-xs font-semibold text-gray-600">Fitur Paket</label>
                    <p class="text-[11px] text-gray-400 mb-3">Bagi fitur ke dalam section seperti pada brosur (misal: Dekorasi, Dokumentasi, Catering).</p>

                    <template x-for="(section, index) in sections" :key="section.id">
                        <div class="border rounded-2xl p-4 mb-4 bg-gray-50/60">
                            <div class="flex items-center gap-3 mb-3">
                                <input type="text" x-model="section.title" @input="updatePayload()" placeholder="Judul Section (opsional)" class="flex-1 border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400">
                                <button type="button" class="text-red-500 text-xs font-semibold" @click="removeSection(index)">
                                    <i class="fas fa-times mr-1"></i>Hapus
                                </button>
                            </div>
                            <label class="text-[11px] text-gray-500">Isi fitur (1 baris = 1 fitur)</label>
                            <textarea x-model="section.items" @input="updatePayload()" rows="4" class="mt-1 w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400 font-mono" placeholder="Dekorasi pelaminan 12 m&#10;Gate pintu masuk&#10;Backdrop photobooth"></textarea>
                        </div>
                    </template>

                    <div class="flex gap-3 mb-3">
                        <button type="button" class="px-4 py-2 rounded-xl border text-sm text-gray-700" @click="addSection()">
                            <i class="fas fa-plus mr-2"></i>Tambah Section
                        </button>
                        <button type="button" class="px-4 py-2 rounded-xl border text-sm text-gray-700" @click="importLegacy()">
                            <i class="fas fa-list mr-2"></i>Import dari textarea lama
                        </button>
                    </div>

                    <textarea name="features_input" rows="3" x-model="legacy" @input="syncLegacy()" class="w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400 font-mono text-xs mb-3" placeholder="Mode cepat: tulis 1 fitur per baris jika belum mau dibagi section"></textarea>
                    <input type="hidden" name="features_payload" :value="payload">
                </div>

                <input type="hidden" name="image" :value="form.image">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-semibold text-gray-600">Upload Galeri Foto/Video</label>
                        <input type="file" name="media_uploads[]" accept="image/*,video/mp4,video/webm,video/quicktime" multiple
                               x-ref="mediaUploadsInput"
                               @change="handleMediaSelection($event)"
                               class="mt-1 w-full text-sm text-gray-500 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-yellow-50 file:text-yellow-700 hover:file:bg-yellow-100">
                        <p class="text-[11px] text-gray-400 mt-1">Bisa pilih banyak file. Max 50MB per video.</p>
                        <p class="text-[11px] text-gray-500 mt-1">
                            Maksimal {{ $maxMedia }} media per paket. Slot tersisa
                            <span class="font-semibold" x-text="remainingMediaSlots()"></span>.
                        </p>
                        <template x-if="pendingUploads.length">
                            <div class="mt-3 space-y-2">
                                <p class="text-[11px] font-semibold text-gray-600">Media baru (belum tersimpan)</p>
                                <div class="space-y-2">
                                    <template x-for="(item, index) in pendingUploads" :key="item.id">
                                        <div class="flex items-center justify-between border rounded-xl px-3 py-2 text-xs bg-white">
                                            <div class="flex-1">
                                                <p class="font-semibold truncate" x-text="item.file.name"></p>
                                                <p class="text-[11px] text-gray-400" x-text="formatSize(item.file.size)"></p>
                                            </div>
                                            <button type="button" class="text-red-500 hover:text-red-600 ml-4" @click="removePendingUpload(index)">
                                                <i class="fas fa-times mr-1"></i>Batalkan
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600">URL Video (Opsional)</label>
                        <textarea name="media_video_urls" rows="3" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400" placeholder="https://youtube.com/...\nhttps://vimeo.com/...\nhttps://drive.google.com/..."></textarea>
                        <p class="text-[11px] text-gray-400 mt-1">Satu URL per baris.</p>
                    </div>
                </div>

                <template x-if="form.id">
                    <div class="mt-4 border rounded-2xl p-4 bg-gray-50/60">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <p class="text-xs font-semibold text-gray-600">Media Paket Saat Ini</p>
                                <p class="text-[11px] text-gray-400">Klik hapus untuk menghilangkan foto/video lama.</p>
                            </div>
                            <span class="text-[11px] text-gray-500" x-text="(form.media_items?.length || 0) + ' item'"></span>
                        </div>
                        <template x-if="!form.media_items || form.media_items.length === 0">
                            <div class="rounded-xl border border-dashed border-gray-200 p-4 text-center text-xs text-gray-400">
                                Belum ada media yang tersimpan.
                            </div>
                        </template>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3" x-show="form.media_items && form.media_items.length" x-cloak x-ref="mediaList">
                            <template x-for="media in form.media_items" :key="media.id">
                                <div class="border rounded-2xl overflow-hidden bg-white shadow-sm flex flex-col relative" :data-media-id="media.id">
                                    <button type="button" class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 media-drag-handle" title="Tarik untuk ubah urutan">
                                        <i class="fas fa-grip-vertical"></i>
                                    </button>
                                    <div class="relative aspect-video bg-gray-100 flex items-center justify-center overflow-hidden">
                                        <template x-if="media.media_type === 'image'">
                                            <img :src="media.url" alt="Media" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="media.media_type === 'video'">
                                            <div class="w-full h-full">
                                                <template x-if="media.embed_url">
                                                    <iframe class="w-full h-full" :src="media.embed_url" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                                </template>
                                                <template x-if="!media.embed_url">
                                                    <video class="w-full h-full object-cover" controls>
                                                        <source :src="media.url">
                                                    </video>
                                                </template>
                                            </div>
                                        </template>
                                        <span class="absolute top-2 left-2 text-[10px] uppercase tracking-wide px-2 py-0.5 rounded-full"
                                              :class="media.media_type === 'video' ? 'bg-purple-100 text-purple-600' : 'bg-blue-100 text-blue-600'"
                                              x-text="media.media_type === 'video' ? 'Video' : 'Foto'"></span>
                                    </div>
                                    <div class="flex items-center justify-between px-3 py-2 text-xs text-gray-600 border-t">
                                        <span class="truncate" x-text="media.embed_url ? 'Link' : 'Upload'"></span>
                                        <form method="POST" class="inline-flex items-center gap-1"
                                              :action="routes.mediaDelete.replace('PK_ID', form.id).replace('MEDIA_ID', media.id)"
                                              onsubmit="return confirm('Hapus media ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 font-semibold hover:text-red-600">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-semibold text-gray-600">Label Promo</label>
                        <input type="text" name="promo_label" x-model="form.promo_label" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400" placeholder="Contoh: PROMO 10%">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600">Diskon (%)</label>
                        <input type="number" step="0.01" min="0" max="100" name="promo_discount_percent" x-model="form.promo_discount_percent" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400" placeholder="Misal 15">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600">Berakhir Pada</label>
                        <input type="date" name="promo_expires_at" x-model="form.promo_expires_at" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600">Deskripsi Promo</label>
                        <textarea name="promo_description" rows="3" x-model="form.promo_description" class="mt-1 w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-yellow-400" placeholder="Detail tambahan promo"></textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t">
                    <button type="button" class="px-4 py-2 rounded-xl border text-sm text-gray-600 hover:bg-gray-50" @click="close()">Batal</button>
                    <button type="submit" class="gold-gradient text-white font-semibold px-6 py-2.5 rounded-xl text-sm shadow hover:shadow-lg">
                        <span x-text="form.id ? 'Update Paket' : 'Simpan Paket'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function packageForm() {
    return {
        isOpen: false,
        image_from_upload: false,
        form: {},
        pendingUploads: [],
        routes: {
            store: @js(route('admin.packages.store')),
            update: @js(route('admin.packages.update', ['package' => 'PK_ID'])),
            mediaDelete: @js(route('admin.packages.media.destroy', ['package' => 'PK_ID', 'media' => 'MEDIA_ID'])),
            mediaReorder: @js(route('admin.packages.media.reorder', ['package' => 'PK_ID'])),
        },
        init() {
            this.$watch('form.media_items', () => this.refreshSortable());
        },
        baseForm(defaults = {}) {
            return Object.assign({
                id: null,
                name: '',
                slug: '',
                tier: '',
                price: '',
                sort_order: defaults.sort_order ?? 0,
                is_active: defaults.is_active ?? true,
                description: '',
                features_input: '',
                features_sections: [],
                features_payload: '',
                includes_digital_invitation: defaults.includes_digital_invitation ?? true,
                image: '',
                image_preview: null,
                category: defaults.category ?? 'rumahan',
                promo_label: '',
                promo_description: '',
                promo_discount_percent: '',
                promo_expires_at: '',
                media_items: [],
                _refreshKey: Date.now(),
            }, defaults);
        },
        replaceForm(next) {
            Object.keys(this.form).forEach((key) => delete this.form[key]);
            Object.assign(this.form, next);
        },
        openCreate(defaults = {}) {
            this.replaceForm(this.baseForm(defaults));
            this.isOpen = true;
        },
        openEdit(pkg) {
            this.replaceForm({
                id: pkg.id,
                name: pkg.name ?? '',
                slug: pkg.slug ?? '',
                tier: pkg.tier ?? '',
                price: pkg.price ?? '',
                sort_order: pkg.sort_order ?? '',
                is_active: Boolean(pkg.is_active),
                description: pkg.description ?? '',
                features_input: flattenFeatures(pkg.features ?? []),
                features_sections: pkg.features ?? [],
                features_payload: JSON.stringify(pkg.features ?? []),
                includes_digital_invitation: pkg.includes_digital_invitation ?? true,
                image: pkg.image ?? '',
                image_preview: null,
                category: pkg.category ?? 'rumahan',
                promo_label: pkg.promo_label ?? '',
                promo_description: pkg.promo_description ?? '',
                promo_discount_percent: pkg.promo_discount_percent ?? '',
                promo_expires_at: pkg.promo_expires_at ?? '',
                media_items: pkg.media_items ?? [],
                _refreshKey: Date.now(),
            });
            this.isOpen = true;
            this.$nextTick(() => this.refreshSortable());
        },
        close() {
            this.isOpen = false;
            this.form = {};
            this.image_from_upload = false;
            this.pendingUploads = [];
        },
        previewImage(event) {
            const file = event.target.files?.[0];
            if (!file) return;
            this.form.image_preview = URL.createObjectURL(file);
            this.image_from_upload = true;
        },
        clearUploadedImage() {
            this.form.image_preview = null;
            this.image_from_upload = false;
            if (this.$refs.imageFileInput) {
                this.$refs.imageFileInput.value = '';
            }
        },
        updateUrlPreview() {
            if (!this.form.image) return;
            if (!this.image_from_upload) {
                this.form.image_preview = null;
            }
        },
        handleMediaSelection(event) {
            const files = Array.from(event.target.files || []);
            this.pendingUploads = files.map((file, index) => ({
                id: `${Date.now()}-${index}`,
                file,
            }));
        },
        removePendingUpload(index) {
            if (!this.$refs.mediaUploadsInput) return;
            this.pendingUploads.splice(index, 1);
            const remainingFiles = new DataTransfer();
            this.pendingUploads.forEach((item) => remainingFiles.items.add(item.file));
            this.$refs.mediaUploadsInput.files = remainingFiles.files;
        },
        formatSize(size) {
            if (!size && size !== 0) return '';
            const units = ['B', 'KB', 'MB', 'GB'];
            let idx = 0;
            let value = size;
            while (value >= 1024 && idx < units.length - 1) {
                value /= 1024;
                idx++;
            }
            return `${value.toFixed(idx === 0 ? 0 : 1)} ${units[idx]}`;
        },
        remainingMediaSlots() {
            const current = this.form.media_items?.length || 0;
            const pending = this.pendingUploads.length;
            return Math.max(0, {{ $maxMedia }} - current - pending);
        },
        refreshSortable() {
            if (!this.$refs.mediaList) return;
            if (this.sortable) {
                this.sortable.destroy();
            }
            if (!this.form.media_items || !this.form.media_items.length) {
                return;
            }
            if (typeof Sortable === 'undefined') {
                console.warn('Sortable belum termuat');
                return;
            }
            this.sortable = Sortable.create(this.$refs.mediaList, {
                handle: '.media-drag-handle',
                animation: 150,
                onEnd: () => this.persistMediaOrder(),
            });
        },
        persistMediaOrder() {
            if (!this.form.id || !this.$refs.mediaList) return;
            const orderedIds = Array.from(this.$refs.mediaList.querySelectorAll('[data-media-id]'))
                .map((el) => Number(el.dataset.mediaId));

            if (!orderedIds.length) return;

            const mediaMap = new Map((this.form.media_items || []).map((item) => [item.id, item]));
            this.form.media_items = orderedIds.map((id) => mediaMap.get(id)).filter(Boolean);

            fetch(this.routes.mediaReorder.replace('PK_ID', this.form.id), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ order: orderedIds }),
            });
        }
    }
}

function featureBuilder(form) {
    return {
        sections: [],
        legacy: form.features_input ?? '',
        payload: form.features_payload ?? '',
        init() {
            this.bootstrap();
            this.$watch(() => form._refreshKey, () => this.bootstrap());
        },
        bootstrap() {
            this.legacy = form.features_input ?? '';
            this.sections = this.normalizeSections(form.features_sections ?? form.features ?? []);
            if (!this.sections.length) {
                this.addSection(false);
            }
            this.updatePayload();
        },
        normalizeSections(raw) {
            if (!Array.isArray(raw) || !raw.length) {
                return [];
            }

            if (typeof raw[0] === 'string') {
                return [{
                    id: this.makeId(0),
                    title: '',
                    items: raw.join('\n'),
                }];
            }

            return raw.map((section, index) => ({
                id: this.makeId(index),
                title: section?.title ?? '',
                items: Array.isArray(section?.items) ? section.items.join('\n') : (section?.items ?? ''),
            }));
        },
        addSection(triggerUpdate = true) {
            this.sections.push({ id: this.makeId(), title: '', items: '' });
            if (triggerUpdate) {
                this.updatePayload();
            }
        },
        removeSection(index) {
            this.sections.splice(index, 1);
            if (!this.sections.length) {
                this.addSection(false);
            }
            this.updatePayload();
        },
        importLegacy() {
            const items = (this.legacy || '')
                .split(/\r?\n/)
                .map(item => item.trim())
                .filter(Boolean);
            if (!items.length) {
                return;
            }
            this.sections.push({ id: this.makeId(), title: '', items: items.join('\n') });
            this.legacy = '';
            this.syncLegacy();
            this.updatePayload();
        },
        updatePayload() {
            const sanitized = this.sections
                .map(section => {
                    const title = section.title?.trim() || null;
                    const items = (section.items || '')
                        .split(/\r?\n/)
                        .map(item => item.trim())
                        .filter(Boolean);

                    return { title, items };
                })
                .filter(section => section.title || section.items.length);

            this.payload = sanitized.length ? JSON.stringify(sanitized) : '';
            form.features_payload = this.payload;
            form.features_sections = sanitized;
        },
        syncLegacy() {
            form.features_input = this.legacy;
        },
        makeId(seed = 0) {
            return `${Date.now()}-${Math.random()}-${seed}`;
        }
    }
}

function flattenFeatures(raw) {
    if (!Array.isArray(raw) || !raw.length) {
        return '';
    }

    if (typeof raw[0] === 'string') {
        return raw.join('\n');
    }

    return raw
        .flatMap(section => Array.isArray(section?.items) ? section.items : [])
        .join('\n');
}
</script>
@endpush
