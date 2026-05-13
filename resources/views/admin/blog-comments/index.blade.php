@extends('layouts.admin')
@section('title', 'Manajemen Komentar Blog')
@section('page-title', 'Komentar Blog')
@section('breadcrumb', 'Komentar Blog')

@section('content')
<div class="space-y-5">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <h2 class="text-base font-semibold text-gray-800 mb-4">Filter Status</h2>
        <div class="flex gap-3">
            <a href="{{ route('admin.blog-comments.index') }}" 
               class="px-4 py-2 rounded-xl text-sm {{ !request('status') ? 'gold-gradient text-white font-semibold' : 'border border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                Semua
            </a>
            <a href="{{ route('admin.blog-comments.index', ['status' => 'pending']) }}" 
               class="px-4 py-2 rounded-xl text-sm {{ request('status') === 'pending' ? 'gold-gradient text-white font-semibold' : 'border border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                Menunggu Persetujuan
            </a>
            <a href="{{ route('admin.blog-comments.index', ['status' => 'approved']) }}" 
               class="px-4 py-2 rounded-xl text-sm {{ request('status') === 'approved' ? 'gold-gradient text-white font-semibold' : 'border border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                Sudah Disetujui
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-400 uppercase bg-gray-50 font-bold tracking-widest">
                    <tr>
                        <th class="px-6 py-4">Nama & Email</th>
                        <th class="px-6 py-4">Komentar</th>
                        <th class="px-6 py-4">Artikel</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($comments as $comment)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-800">{{ $comment->name }}</div>
                            <div class="text-xs text-gray-400">{{ $comment->email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="max-w-md truncate text-gray-600" title="{{ $comment->content }}">
                                {{ $comment->content }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('blog.show', $comment->post->slug) }}" target="_blank" class="text-blue-500 hover:underline">
                                {{ Str::limit($comment->post->title, 30) }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($comment->is_approved)
                                <span class="px-3 py-1 bg-green-50 text-green-600 text-[10px] font-bold uppercase tracking-widest rounded-full">Approved</span>
                            @else
                                <span class="px-3 py-1 bg-yellow-50 text-yellow-600 text-[10px] font-bold uppercase tracking-widest rounded-full">Pending</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-400">
                            {{ $comment->created_at->format('d M Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="flex justify-end gap-2">
                                @if(!$comment->is_approved)
                                <form action="{{ route('admin.blog-comments.approve', $comment->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg bg-green-50 text-green-600 hover:bg-green-600 hover:text-white transition-all" title="Setujui">
                                        <i class="fas fa-check text-xs"></i>
                                    </button>
                                </form>
                                @endif
                                <form action="{{ route('admin.blog-comments.destroy', $comment->id) }}" method="POST" onsubmit="return confirm('Hapus komentar ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-all" title="Hapus">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-400 italic">
                            Belum ada komentar yang masuk.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($comments->hasPages())
        <div class="p-4 border-t">
            {{ $comments->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
