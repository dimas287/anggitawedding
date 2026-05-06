@extends('layouts.admin')

@section('title', 'Instagram Feed Management')
@section('page-title', 'Manajemen Instagram Feed')

@section('content')
<div class="space-y-6" x-data="instagramManager()">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Momen di Instagram</h2>
            <p class="text-sm text-gray-500">Kelola postingan Instagram yang tampil di landing page.</p>
        </div>
        <button @click="openCreate()" class="gold-gradient text-white px-5 py-2.5 rounded-xl font-semibold shadow-lg hover:opacity-90 transition-all flex items-center gap-2">
            <i class="fas fa-plus"></i> Tambah Postingan
        </button>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-xl relative flex items-center gap-3">
        <i class="fas fa-check-circle"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @forelse($posts as $post)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden group">
            <div class="aspect-square relative overflow-hidden bg-gray-100">
                <img src="{{ $post->resolved_image_url }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-3">
                    <button @click='openEdit(@json($post))' class="w-10 h-10 rounded-full bg-white text-gray-800 flex items-center justify-center hover:bg-yellow-500 hover:text-white transition-all">
                        <i class="fas fa-edit"></i>
                    </button>
                    <form action="{{ route('admin.instagram-posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Hapus postingan ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="w-10 h-10 rounded-full bg-white text-red-600 flex items-center justify-center hover:bg-red-600 hover:text-white transition-all">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
                <div class="absolute top-3 right-3">
                    @if($post->is_active)
                    <span class="px-2 py-1 rounded-lg text-[9px] font-bold bg-green-500 text-white shadow-lg uppercase">Aktif</span>
                    @else
                    <span class="px-2 py-1 rounded-lg text-[9px] font-bold bg-gray-500 text-white shadow-lg uppercase">Draft</span>
                    @endif
                </div>
                <div class="absolute bottom-3 left-3 bg-black/50 backdrop-blur-md px-2 py-1 rounded text-[10px] text-white">
                    Order: {{ $post->sort_order }}
                </div>
            </div>
            <div class="p-4">
                <p class="text-xs text-gray-500 line-clamp-2 mb-3 italic">"{{ $post->caption ?? 'Tanpa caption' }}"</p>
                <a href="{{ $post->instagram_url }}" target="_blank" class="text-[10px] font-bold text-yellow-600 uppercase tracking-widest flex items-center gap-1 hover:underline">
                    <i class="fab fa-instagram"></i> Lihat di Instagram
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-full py-12 text-center text-gray-400 bg-white rounded-2xl border border-dashed border-gray-200">
            <i class="fab fa-instagram text-4xl mb-3 block opacity-20"></i>
            <p>Belum ada postingan yang ditambahkan.</p>
        </div>
        @endforelse
    </div>

    {{-- MODAL --}}
    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="modalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="modalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-middle bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form :action="editMode ? '{{ route('admin.instagram-posts.index') }}/' + form.id : '{{ route('admin.instagram-posts.store') }}'" method="POST" enctype="multipart/form-data">
                    @csrf
                    <template x-if="editMode">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-bold text-gray-900" x-text="editMode ? 'Edit Postingan' : 'Tambah Postingan Baru'"></h3>
                            <button type="button" @click="closeModal()" class="text-gray-400 hover:text-gray-500">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Link Postingan Instagram</label>
                                <div class="flex gap-2">
                                    <input type="url" name="instagram_url" x-model="form.instagram_url" required class="flex-1 px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-yellow-500/20 focus:border-yellow-500 transition-all text-sm" placeholder="https://www.instagram.com/p/...">
                                    <button type="button" @click="fetchMetadata()" class="px-4 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all" :disabled="loading">
                                        <i class="fas" :class="loading ? 'fa-spinner fa-spin' : 'fa-magic'"></i>
                                    </button>
                                </div>
                                <p class="text-[10px] text-gray-400 mt-1">Klik tongkat ajaib untuk menarik data otomatis (percobaan).</p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Upload Foto (Wajib jika tarik otomatis gagal)</label>
                                <input type="file" name="media_upload" class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm">
                                <p class="text-[10px] text-gray-400 mt-1">JPG, PNG, WEBP. Maks 5MB.</p>
                            </div>

                            <div x-show="previewUrl" class="mt-2">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2">Preview Gambar Terdeteksi</label>
                                <img :src="previewUrl" class="w-full h-40 object-cover rounded-xl border">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Caption Singkat</label>
                                <textarea name="caption" x-model="form.caption" rows="2" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-yellow-500/20 focus:border-yellow-500 transition-all text-sm" placeholder="Caption yang tampil di web..."></textarea>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Urutan</label>
                                    <input type="number" name="sort_order" x-model="form.sort_order" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-yellow-500/20 focus:border-yellow-500 transition-all text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Status</label>
                                    <select name="is_active" x-model="form.is_active" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-yellow-500/20 focus:border-yellow-500 transition-all text-sm">
                                        <option value="1">Aktif (Tampil)</option>
                                        <option value="0">Draft (Sembunyi)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                        <button type="button" @click="closeModal()" class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-semibold text-sm hover:bg-gray-100 transition-all">Batal</button>
                        <button type="submit" class="px-8 py-2.5 rounded-xl gold-gradient text-white font-semibold text-sm shadow-lg hover:opacity-90 transition-all">Simpan Postingan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function instagramManager() {
    return {
        modalOpen: false,
        editMode: false,
        loading: false,
        previewUrl: '',
        form: {
            id: null,
            instagram_url: '',
            caption: '',
            sort_order: 0,
            is_active: 1
        },
        openCreate() {
            this.editMode = false;
            this.form = {
                id: null,
                instagram_url: '',
                caption: '',
                sort_order: 0,
                is_active: 1
            };
            this.previewUrl = '';
            this.modalOpen = true;
        },
        openEdit(post) {
            this.editMode = true;
            this.form = {
                id: post.id,
                instagram_url: post.instagram_url,
                caption: post.caption || '',
                sort_order: post.sort_order,
                is_active: post.is_active
            };
            this.previewUrl = post.resolved_image_url;
            this.modalOpen = true;
        },
        closeModal() {
            this.modalOpen = false;
        },
        async fetchMetadata() {
            if (!this.form.instagram_url) {
                alert('Masukkan link Instagram terlebih dahulu.');
                return;
            }
            this.loading = true;
            try {
                const response = await fetch('{{ route("admin.instagram-posts.fetch") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ url: this.form.instagram_url })
                });
                const data = await response.json();
                if (data.success) {
                    this.previewUrl = data.image_url;
                    if (!this.form.caption) {
                        this.form.caption = data.caption;
                    }
                } else {
                    alert(data.message);
                }
            } catch (e) {
                alert('Gagal tarik data. Silakan upload gambar manual.');
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endpush
