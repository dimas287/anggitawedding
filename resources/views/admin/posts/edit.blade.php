@extends('layouts.admin')

@section('title', 'Edit Artikel')
@section('page-title', 'Edit Artikel')

@push('head')
<script src="https://cdn.ckeditor.com/ckeditor5/41.2.1/classic/ckeditor.js"></script>
<style>
    .ck-editor__editable_inline {
        min-height: 400px;
    }
    .ck.ck-editor__main>.ck-editor__editable {
        background-color: white;
        border-bottom-left-radius: 12px !important;
        border-bottom-right-radius: 12px !important;
    }
    .ck.ck-editor__top .ck-sticky-panel .ck-toolbar {
        border-top-left-radius: 12px !important;
        border-top-right-radius: 12px !important;
        border-bottom: 1px solid #f3f4f6 !important;
    }
    .ck.ck-focused {
        border-color: #D4AF37 !important;
        box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.25) !important;
    }
</style>
@endpush

@section('content')
<div class="max-w-5xl mx-auto pb-12">
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('admin.posts.index') }}" class="text-sm text-gray-500 hover:text-gray-800 flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar
        </a>
    </div>

    <form action="{{ route('admin.posts.update', $post) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Judul --}}
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">Judul Artikel *</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $post->title) }}" required
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-500/20 transition-all outline-none text-lg font-bold">
                    @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Kategori --}}
                <div>
                    <label for="category" class="block text-sm font-semibold text-gray-700 mb-2">Kategori</label>
                    <input type="text" name="category" id="category" value="{{ old('category', $post->category) }}" placeholder="Contoh: Tips, Inspirasi, Dekorasi"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-500/20 transition-all outline-none">
                </div>

                {{-- Status --}}
                <div>
                    <label for="is_published" class="block text-sm font-semibold text-gray-700 mb-2">Status Publikasi</label>
                    <select name="is_published" id="is_published" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-500/20 transition-all outline-none">
                        <option value="0" {{ old('is_published', $post->is_published) == '0' ? 'selected' : '' }}>Draft</option>
                        <option value="1" {{ old('is_published', $post->is_published) == '1' ? 'selected' : '' }}>Publish Sekarang</option>
                    </select>
                </div>

                {{-- Thumbnail --}}
                <div class="md:col-span-2">
                    <label for="thumbnail" class="block text-sm font-semibold text-gray-700 mb-2">Update Gambar Thumbnail</label>
                    <div class="flex items-center gap-6">
                        @if($post->thumbnail)
                        <div class="relative group">
                            <img src="{{ asset('storage/' . $post->thumbnail) }}" class="w-32 h-20 rounded-lg object-cover border border-gray-100 shadow-sm">
                            <p class="text-[9px] text-center mt-1 text-gray-400">Preview Saat Ini</p>
                        </div>
                        @endif
                        <div class="flex-1">
                            <input type="file" name="thumbnail" id="thumbnail" accept="image/*"
                                   class="w-full px-4 py-2.5 rounded-xl border border-gray-200 cursor-pointer text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-yellow-50 file:text-yellow-700 hover:file:bg-yellow-100">
                            <p class="text-[10px] text-gray-400 mt-2 italic">Format: JPG, PNG, WEBP. Maks 2MB. Kosongkan jika tidak diubah.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Ringkasan --}}
            <div>
                <label for="excerpt" class="block text-sm font-semibold text-gray-700 mb-2">Ringkasan Pendek (Tampil di Slide Depan)</label>
                <textarea name="excerpt" id="excerpt" rows="2"
                          class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-500/20 transition-all outline-none resize-none"
                          placeholder="Ringkasan singkat maksimal 200 karakter...">{{ old('excerpt', $post->excerpt) }}</textarea>
            </div>

            {{-- Konten --}}
            <div class="editor-container">
                <label for="content" class="block text-sm font-semibold text-gray-700 mb-2 font-poppins">Isi Artikel Lengkap *</label>
                <textarea name="content" id="content">{{ old('content', $post->content) }}</textarea>
            </div>
        </div>

        {{-- SEO Section --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 space-y-6">
            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-search text-yellow-500"></i> Optimasi SEO (Opsional)
            </h3>
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label for="meta_title" class="block text-sm font-semibold text-gray-700 mb-2">SEO Title Tag</label>
                    <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title', $post->meta_title) }}" placeholder="Judul yang tampil di Google"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-500/20 transition-all outline-none">
                </div>
                <div>
                    <label for="meta_description" class="block text-sm font-semibold text-gray-700 mb-2">Meta Description</label>
                    <textarea name="meta_description" id="meta_description" rows="3"
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-500/20 transition-all outline-none resize-none"
                               placeholder="Deskripsi singkat untuk hasil pencarian Google...">{{ old('meta_description', $post->meta_description) }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4">
            <button type="submit" class="gold-gradient text-white px-10 py-4 rounded-xl font-bold font-poppins shadow-lg hover:opacity-90 transition-all flex items-center gap-2">
                <i class="fas fa-save"></i> Perbarui Artikel
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    class MyUploadAdapter {
        constructor(loader) {
            this.loader = loader;
        }

        upload() {
            return this.loader.file
                .then(file => new Promise((resolve, reject) => {
                    this._initRequest();
                    this._initListeners(resolve, reject, file);
                    this._sendRequest(file);
                }));
        }

        abort() {
            if (this.xhr) {
                this.xhr.abort();
            }
        }

        _initRequest() {
            const xhr = this.xhr = new XMLHttpRequest();
            xhr.open('POST', "{{ route('admin.posts.upload') }}", true);
            xhr.setRequestHeader('x-csrf-token', '{{ csrf_token() }}');
            xhr.responseType = 'json';
        }

        _initListeners(resolve, reject, file) {
            const xhr = this.xhr;
            const loader = this.loader;
            const genericErrorText = `Couldn't upload file: ${file.name}.`;

            xhr.addEventListener('error', () => reject(genericErrorText));
            xhr.addEventListener('abort', () => reject());
            xhr.addEventListener('load', () => {
                const response = xhr.response;
                if (!response || response.error) {
                    return reject(response && response.error ? response.error : genericErrorText);
                }
                resolve({
                    default: response.url
                });
            });

            if (xhr.upload) {
                xhr.upload.addEventListener('progress', evt => {
                    if (evt.lengthComputable) {
                        loader.uploadTotal = evt.total;
                        loader.uploaded = evt.loaded;
                    }
                });
            }
        }

        _sendRequest(file) {
            const data = new FormData();
            data.append('file', file);
            this.xhr.send(data);
        }
    }

    function MyCustomUploadAdapterPlugin(editor) {
        editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
            return new MyUploadAdapter(loader);
        };
    }

    ClassicEditor
        .create(document.querySelector('#content'), {
            extraPlugins: [MyCustomUploadAdapterPlugin],
            toolbar: [
                'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|',
                'imageUpload', 'insertTable', 'mediaEmbed', 'undo', 'redo'
            ],
            image: {
                toolbar: [
                    'imageStyle:inline', 'imageStyle:block', 'imageStyle:side', '|',
                    'toggleImageCaption', 'imageTextAlternative'
                ]
            }
        })
        .catch(error => {
            console.error(error);
        });
</script>
@endpush
