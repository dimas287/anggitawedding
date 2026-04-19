@extends('layouts.admin')
@section('title', 'Hero Slides')
@section('page-title', 'Hero Slides')
@section('breadcrumb', 'Dashboard / Hero Slides')

@section('content')
<div class="space-y-6"
     x-data="heroSlideManager({
        defaults: { sort_order: {{ ($slides->max('sort_order') ?? 0) + 1 }} },
        routes: {
            store: '{{ route('admin.hero-slides.store') }}',
            update: '{{ route('admin.hero-slides.update', '__ID__') }}'
        }
     })">

    <div class="bg-white rounded-2xl shadow-sm px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Kelola Media Hero</h3>
            <p class="text-sm text-gray-500">Upload video atau foto untuk background slideshow landing page secara langsung dari dashboard.</p>
        </div>
        <button @click="openCreate()"
                class="inline-flex items-center gap-2 gold-gradient text-white font-semibold px-5 py-3 rounded-xl text-sm shadow hover:shadow-lg transition-all">
            <i class="fas fa-plus"></i> Tambah Slide
        </button>
    </div>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
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
                                <button @click='openEdit(@json($slide))'
                                        class="inline-flex items-center gap-1 text-sm text-yellow-600 hover:text-yellow-700 font-semibold">
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
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl" @click.away="close()">
            <div class="flex items-center justify-between p-5 border-b">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800" x-text="mode === 'create' ? 'Tambah Slide' : 'Edit Slide'"></h3>
                    <p class="text-xs text-gray-500" x-text="mode === 'create' ? 'Masukkan media baru untuk hero section.' : 'Perbarui informasi slide.'"></p>
                </div>
                <button class="text-gray-400 hover:text-gray-600" @click="close()"><i class="fas fa-times"></i></button>
            </div>
            <form :action="actionUrl()" method="POST" class="p-6 space-y-5" enctype="multipart/form-data">
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
</script>
@endpush
