@extends('layouts.app')
@section('title', 'Statistik RSVP')
@section('page-title', 'Statistik RSVP')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-2 sm:grid-cols-6 gap-4">
        <div class="bg-white rounded-xl p-5 text-center shadow-sm"><p class="text-3xl font-bold text-indigo-600">{{ $stats['view_count'] ?? 0 }}</p><p class="text-sm text-gray-500 mt-1">Total Views</p></div>
        <div class="bg-white rounded-xl p-5 text-center shadow-sm"><p class="text-3xl font-bold text-purple-600">{{ $stats['views_total'] ?? 0 }}</p><p class="text-sm text-gray-500 mt-1">View Records</p></div>
        <div class="bg-white rounded-xl p-5 text-center shadow-sm"><p class="text-3xl font-bold text-blue-600">{{ $stats['total'] }}</p><p class="text-sm text-gray-500 mt-1">Total RSVP</p></div>
        <div class="bg-white rounded-xl p-5 text-center shadow-sm"><p class="text-3xl font-bold text-green-600">{{ $stats['hadir'] }}</p><p class="text-sm text-gray-500 mt-1">Hadir</p></div>
        <div class="bg-white rounded-xl p-5 text-center shadow-sm"><p class="text-3xl font-bold text-red-500">{{ $stats['tidak_hadir'] }}</p><p class="text-sm text-gray-500 mt-1">Tidak Hadir</p></div>
        <div class="bg-white rounded-xl p-5 text-center shadow-sm"><p class="text-3xl font-bold text-yellow-600">{{ $stats['total_guests'] }}</p><p class="text-sm text-gray-500 mt-1">Total Tamu</p></div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="p-5 border-b flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">Daftar RSVP</h3>
            <span class="text-sm text-gray-500">{{ $rsvps->count() }} respons</span>
        </div>
        @if($rsvps->isEmpty())
        <div class="text-center py-10 text-gray-400">
            <i class="fas fa-users text-4xl mb-3 block"></i>
            <p class="text-sm">Belum ada RSVP masuk</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50"><tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Nama</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">No. HP</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Jumlah Tamu</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Kehadiran</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Pesan</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Waktu</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($rsvps as $rsvp)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $rsvp->name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $rsvp->phone ?? '-' }}</td>
                        <td class="px-4 py-3 text-center text-gray-600">{{ $rsvp->guests_count }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $rsvp->attendance==='hadir'?'bg-green-100 text-green-700':($rsvp->attendance==='tidak_hadir'?'bg-red-100 text-red-600':'bg-yellow-100 text-yellow-700') }}">
                                {{ ucfirst(str_replace('_',' ',$rsvp->attendance)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 max-w-xs">{{ Str::limit($rsvp->message, 60) ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-400 text-xs">{{ $rsvp->created_at->diffForHumans() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    <!-- Guestbook Section -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden mt-6">
        <div class="p-5 border-b flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">Kartu Ucapan (Guestbook)</h3>
            <span class="text-sm text-gray-500">{{ ($guestbooks ?? collect())->count() }} ucapan</span>
        </div>
        @if(!isset($guestbooks) || $guestbooks->isEmpty())
        <div class="text-center py-10 text-gray-400">
            <i class="fas fa-comment-dots text-4xl mb-3 block"></i>
            <p class="text-sm">Belum ada ucapan masuk</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50"><tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Nama</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Pesan & Doa</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Waktu</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($guestbooks as $msg)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-800 whitespace-nowrap">{{ $msg->name }}</td>
                        <td class="px-4 py-3 text-gray-600 max-w-lg break-words">{{ $msg->message }}</td>
                        <td class="px-4 py-3 text-gray-400 text-xs whitespace-nowrap">{{ $msg->created_at->diffForHumans() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden mt-6">
        <div class="p-5 border-b flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">Kunjungan Terakhir</h3>
            <span class="text-sm text-gray-500">{{ ($visitors ?? collect())->count() }} data</span>
        </div>
        @if(!isset($visitors) || $visitors->isEmpty())
        <div class="text-center py-10 text-gray-400">
            <i class="fas fa-eye text-4xl mb-3 block"></i>
            <p class="text-sm">Belum ada kunjungan tercatat</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50"><tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">IP</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">User Agent</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Waktu</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($visitors as $v)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $v->ip_address ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-600 max-w-md break-words">{{ Str::limit($v->user_agent, 90) ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-400 text-xs">{{ $v->created_at->diffForHumans() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
