@extends('layouts.admin')
@section('title', 'Portofolio')
@section('page-title', 'Portofolio')
@section('breadcrumb', 'Dashboard / Portofolio')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-sm p-5">
        <h3 class="text-lg font-semibold text-gray-800">Kelola Gambar Portofolio</h3>
        <p class="text-sm text-gray-500">Tambah dan atur tampilan gambar portofolio. Pilih aspek agar tampilan rapi seperti grid modern.</p>

        <form action="{{ route('admin.portfolio.store') }}" method="POST" enctype="multipart/form-data" class="mt-5 grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
            @csrf
            <div class="md:col-span-2">
                <label class="text-xs font-semibold text-gray-600">Gambar</label>
                <input type="file" name="image" accept="image/*" required class="mt-1 w-full border rounded-xl px-4 py-2.5 text-sm">
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600">Aspek</label>
                <select name="aspect" class="mt-1 w-full border rounded-xl px-4 py-2.5 text-sm">
                    <option value="square">Square</option>
                    <option value="portrait">Portrait</option>
                    <option value="landscape">Landscape</option>
                    <option value="wide">Wide</option>
                    <option value="tall">Tall</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600">Urutan</label>
                <input type="number" min="0" name="sort_order" value="{{ $nextSortOrder }}" class="mt-1 w-full border rounded-xl px-4 py-2.5 text-sm">
            </div>
            <div class="flex items-center gap-2">
                <input type="hidden" name="is_active" value="0">
                <label class="inline-flex items-center gap-2 text-sm text-gray-700 mt-6">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-yellow-500 focus:ring-yellow-400">
                    Aktif
                </label>
            </div>
            <div>
                <button type="submit" class="gold-gradient text-white font-semibold px-6 py-2.5 rounded-xl text-sm shadow hover:shadow-lg">Tambah</button>
            </div>
        </form>

        @if($errors->any())
            <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                <div class="font-semibold mb-1">Terjadi kesalahan:</div>
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="p-5">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse($images as $img)
                    <div class="border rounded-2xl overflow-hidden bg-white shadow-sm" x-data="{ open: false, mediaOpen: false }">
                        <div class="bg-gray-100">
                            <img src="{{ asset('storage/'.$img->image_path) }}" class="w-full h-48 object-cover" alt="">
                        </div>
                        <div class="p-4 space-y-5">
                            <div class="flex items-center justify-between">
                                <div class="text-sm font-semibold text-gray-800">#{{ $img->id }}</div>
                                <button type="button" @click="open = !open" class="px-4 py-2 rounded-xl text-sm font-semibold bg-gray-100 text-gray-800 hover:bg-gray-200">
                                    <span x-show="!open" x-cloak>Detail</span>
                                    <span x-show="open" x-cloak>Tutup</span>
                                </button>
                            </div>

                            <form x-show="open" x-cloak action="{{ route('admin.portfolio.update', $img) }}" method="POST" enctype="multipart/form-data" class="space-y-3 mt-3">
                                @csrf
                                @method('PUT')

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="text-xs font-semibold text-gray-600">Aspek</label>
                                        <select name="aspect" class="mt-1 w-full border rounded-xl px-3 py-2 text-sm">
                                            @foreach(['square','portrait','landscape','wide','tall'] as $asp)
                                                <option value="{{ $asp }}" @selected($img->aspect === $asp)>{{ ucfirst($asp) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold text-gray-600">Urutan</label>
                                        <input type="number" min="0" name="sort_order" value="{{ $img->sort_order }}" class="mt-1 w-full border rounded-xl px-3 py-2 text-sm">
                                    </div>
                                </div>

                                <div>
                                    <label class="text-xs font-semibold text-gray-600">Judul (opsional)</label>
                                    <input type="text" name="title" value="{{ $img->title }}" class="mt-1 w-full border rounded-xl px-3 py-2 text-sm">
                                </div>

                                <div>
                                    <label class="text-xs font-semibold text-gray-600">Caption (opsional)</label>
                                    <input type="text" name="caption" value="{{ $img->caption }}" class="mt-1 w-full border rounded-xl px-3 py-2 text-sm">
                                </div>

                                <div>
                                    <label class="text-xs font-semibold text-gray-600">Ganti Gambar (opsional)</label>
                                    <input type="file" name="image" accept="image/*" class="mt-1 w-full border rounded-xl px-3 py-2 text-sm">
                                </div>

                                <div class="flex items-center justify-between pt-2">
                                    <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                                        <input type="hidden" name="is_active" value="0">
                                        <input type="checkbox" name="is_active" value="1" @checked($img->is_active) class="rounded border-gray-300 text-yellow-500 focus:ring-yellow-400">
                                        Aktif
                                    </label>

                                    <div class="flex items-center gap-3">
                                        <button type="button" @click="mediaOpen = true" class="px-4 py-2 rounded-xl text-sm font-semibold bg-yellow-50 text-yellow-700 hover:bg-yellow-100">Media Tambahan</button>
                                        <button type="submit" class="px-4 py-2 rounded-xl text-sm font-semibold bg-gray-900 text-white hover:bg-gray-800">Simpan</button>
                                        <button type="submit" form="delete-portfolio-{{ $img->id }}" class="px-4 py-2 rounded-xl text-sm font-semibold bg-red-50 text-red-700 hover:bg-red-100" onclick="return confirm('Hapus gambar ini?');">Hapus</button>
                                    </div>
                                </div>
                            </form>

                            <div class="pt-4 border-t border-dashed" x-cloak x-show="mediaOpen" x-data="{ mediaType: 'image_upload' }">
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        <h4 class="text-sm font-semibold text-gray-800">Media Tambahan</h4>
                                        <p class="text-xs text-gray-500">Tambah beberapa foto atau video untuk card ini.</p>
                                    </div>
                                    <button type="button" class="text-xs text-gray-500" @click="mediaOpen = false">Tutup</button>
                                </div>
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        <h4 class="text-sm font-semibold text-gray-800">Media Tambahan</h4>
                                        <p class="text-xs text-gray-500">Tambah beberapa foto atau video untuk card ini.</p>
                                    </div>
                                    <span class="text-[11px] text-gray-400">{{ $img->mediaItems->count() }} item</span>
                                </div>

                                <div class="space-y-3">
                                    @forelse($img->mediaItems as $media)
                                        <div class="flex items-center gap-3 border border-gray-100 rounded-2xl p-3">
                                            <div class="w-20 h-14 rounded-xl overflow-hidden bg-gray-100 flex items-center justify-center">
                                                @if($media->media_type === 'image')
                                                    <img src="{{ $media->url }}" alt="Media" class="w-full h-full object-cover">
                                                @elseif($media->embed_url)
                                                    <i class="fas fa-video text-yellow-500"></i>
                                                @else
                                                    <video src="{{ $media->url }}" class="w-full h-full object-cover" muted></video>
                                                @endif
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-sm font-semibold text-gray-800 capitalize">{{ $media->media_type }}</p>
                                                <p class="text-xs text-gray-500">Urutan: {{ $media->sort_order }}</p>
                                                @if($media->media_type === 'video' && $media->video_url)
                                                    <p class="text-xs text-blue-500 truncate">{{ $media->video_url }}</p>
                                                @endif
                                            </div>
                                            <form action="{{ route('admin.portfolio.media.destroy', [$img, $media]) }}" method="POST" onsubmit="return confirm('Hapus media ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-9 h-9 rounded-full bg-red-50 text-red-600 hover:bg-red-100 flex items-center justify-center"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </div>
                                    @empty
                                        <p class="text-sm text-gray-500">Belum ada media tambahan. Gunakan formulir di bawah untuk menambah foto/video.</p>
                                    @endforelse
                                </div>

                                <form action="{{ route('admin.portfolio.media.store', $img) }}" method="POST" enctype="multipart/form-data" class="mt-4 bg-gray-50 rounded-2xl p-4 space-y-3 border border-dashed border-gray-200">
                                    @csrf
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="text-xs font-semibold text-gray-600">Jenis Media</label>
                                            <select name="type" x-model="mediaType" class="mt-1 w-full border rounded-xl px-3 py-2 text-sm">
                                                <option value="image_upload">Foto (upload)</option>
                                                <option value="video_upload">Video (upload)</option>
                                                <option value="video_url">Video (URL)</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="text-xs font-semibold text-gray-600">Urutan</label>
                                            <input type="number" min="0" name="sort_order" value="{{ ($img->mediaItems->max('sort_order') ?? -1) + 1 }}" class="mt-1 w-full border rounded-xl px-3 py-2 text-sm">
                                        </div>
                                    </div>
                                    <div x-show="mediaType !== 'video_url'" x-cloak>
                                        <label class="text-xs font-semibold text-gray-600">File Upload</label>
                                        <input type="file" name="media_upload" accept="image/*,video/mp4,video/webm" class="mt-1 w-full border rounded-xl px-3 py-2 text-sm">
                                        <p class="text-[11px] text-gray-500 mt-1">Gambar: JPG/PNG/WEBP (maks 5MB). Video: MP4/WEBM (maks 50MB).</p>
                                    </div>
                                    <div x-show="mediaType === 'video_url'" x-cloak>
                                        <label class="text-xs font-semibold text-gray-600">URL Video (YouTube/Vimeo/Drive)</label>
                                        <input type="url" name="video_url" placeholder="https://" class="mt-1 w-full border rounded-xl px-3 py-2 text-sm">
                                    </div>
                                    <div class="flex justify-end pt-2">
                                        <button type="submit" class="px-4 py-2 rounded-xl text-sm font-semibold bg-gray-900 text-white hover:bg-gray-800">Simpan Media</button>
                                    </div>
                                </form>

                            <form id="delete-portfolio-{{ $img->id }}" action="{{ route('admin.portfolio.destroy', $img) }}" method="POST" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full px-6 py-10 text-center text-gray-500 text-sm">Belum ada gambar portofolio.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
