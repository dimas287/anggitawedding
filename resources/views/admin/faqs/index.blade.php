@extends('layouts.admin')

@section('title', 'FAQ Management')
@section('page-title', 'Manajemen FAQ')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Daftar FAQ</h2>
            <p class="text-sm text-gray-500">Kelola pertanyaan yang sering diajukan oleh calon klien Anda.</p>
        </div>
        <a href="{{ route('admin.faqs.create') }}" class="gold-gradient text-white px-5 py-2.5 rounded-xl font-semibold shadow-lg hover:opacity-90 transition-all flex items-center gap-2">
            <i class="fas fa-plus"></i> FAQ Baru
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
                        <th class="px-6 py-4 text-xs uppercase tracking-widest text-gray-500 font-semibold w-16">Urutan</th>
                        <th class="px-6 py-4 text-xs uppercase tracking-widest text-gray-500 font-semibold">Pertanyaan & Jawaban</th>
                        <th class="px-6 py-4 text-xs uppercase tracking-widest text-gray-500 font-semibold w-24">Status</th>
                        <th class="px-6 py-4 text-xs uppercase tracking-widest text-gray-500 font-semibold w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($faqs as $faq)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 text-center font-bold text-gray-400">
                            {{ $faq->sort_order }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-800 mb-1">{{ $faq->question }}</div>
                            <div class="text-xs text-gray-500 line-clamp-2">{{ $faq->answer }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($faq->is_active)
                            <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-600 uppercase">Aktif</span>
                            @else
                            <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-gray-100 text-gray-400 uppercase">Non-Aktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.faqs.edit', $faq) }}" class="p-2 rounded-lg hover:bg-yellow-50 text-yellow-600 transition-colors" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.faqs.destroy', $faq) }}" method="POST" onsubmit="return confirm('Hapus FAQ ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="p-2 rounded-lg hover:bg-red-50 text-red-600 transition-colors" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                            <i class="fas fa-question-circle text-4xl mb-3 block"></i>
                            <p>Belum ada FAQ. Tambahkan pertanyaan pertama Anda!</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
