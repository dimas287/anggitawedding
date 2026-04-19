@extends('layouts.admin')

@section('title', 'Blog / Artikel')
@section('page-title', 'Manajemen Blog')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Daftar Artikel</h2>
            <p class="text-sm text-gray-500">Kelola konten berita dan inspirasi pernikahan untuk SEO website Anda.</p>
        </div>
        <a href="{{ route('admin.posts.create') }}" class="gold-gradient text-white px-5 py-2.5 rounded-xl font-semibold shadow-lg hover:opacity-90 transition-all flex items-center gap-2">
            <i class="fas fa-plus"></i> Artikel Baru
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-xl relative flex items-center gap-3">
        <i class="fas fa-check-circle"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-6 py-4 text-xs uppercase tracking-widest text-gray-500 font-semibold">Artikel</th>
                        <th class="px-6 py-4 text-xs uppercase tracking-widest text-gray-500 font-semibold">Kategori</th>
                        <th class="px-6 py-4 text-xs uppercase tracking-widest text-gray-500 font-semibold">Status</th>
                        <th class="px-6 py-4 text-xs uppercase tracking-widest text-gray-500 font-semibold">Tgl Publish</th>
                        <th class="px-6 py-4 text-xs uppercase tracking-widest text-gray-500 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($posts as $post)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                @if($post->thumbnail)
                                <img src="{{ asset('storage/' . $post->thumbnail) }}" class="w-12 h-12 rounded-lg object-cover">
                                @else
                                <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center">
                                    <i class="fas fa-image text-gray-300"></i>
                                </div>
                                @endif
                                <div>
                                    <div class="font-semibold text-gray-800">{{ $post->title }}</div>
                                    <div class="text-xs text-gray-400">{{ Str::limit($post->excerpt, 40) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-600 uppercase tracking-wider">
                                {{ $post->category ?? 'Lainnya' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($post->is_published)
                            <span class="flex items-center gap-1.5 text-green-600 text-xs font-medium">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Published
                            </span>
                            @else
                            <span class="flex items-center gap-1.5 text-gray-400 text-xs font-medium">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span> Draft
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $post->published_at ? $post->published_at->format('d M Y') : '-' }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.posts.edit', $post) }}" class="p-2 rounded-lg hover:bg-yellow-50 text-yellow-600 transition-colors" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Hapus artikel ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="p-2 rounded-lg hover:bg-red-50 text-red-600 transition-colors" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                <a href="{{ route('blog.show', $post->slug) }}" target="_blank" class="p-2 rounded-lg hover:bg-blue-50 text-blue-600 transition-colors" title="Lihat">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                            <i class="fas fa-newspaper text-4xl mb-3 block"></i>
                            <p>Belum ada artikel. Mulai menulis sekarang!</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($posts->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            {{ $posts->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
