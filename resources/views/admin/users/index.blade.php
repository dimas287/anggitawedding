@php use Illuminate\Support\Str; @endphp
@extends('layouts.admin')
@section('title', 'Daftar Klien')
@section('page-title', 'Daftar Klien')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl shadow-sm p-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Klien Terdaftar</h2>
            <p class="text-sm text-gray-500">Pantau siapa saja yang sudah membuat akun dan status booking mereka.</p>
        </div>
        <form method="GET" class="flex gap-2">
            <div class="relative">
                <input type="text" name="q" value="{{ request('q') }}"
                       class="w-64 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400"
                       placeholder="Cari nama/email/no WA">
                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400"><i class="fas fa-search"></i></span>
            </div>
            @if(request('q'))
                <a href="{{ route('admin.clients.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Reset</a>
            @endif
        </form>
    </div>

    <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-5">
        @forelse($clients as $client)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex flex-col gap-3">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm text-gray-500">Klien</p>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $client->name }}</h3>
                        <p class="text-xs text-gray-400">Terdaftar {{ $client->created_at->format('d M Y') }}</p>
                    </div>
                    <a href="{{ route('admin.clients.show', $client) }}" class="text-xs px-3 py-1.5 rounded-full bg-gray-100 text-gray-600 hover:bg-gray-900 hover:text-white transition">Detail</a>
                </div>
                <div class="text-sm text-gray-600 space-y-1">
                    <p><i class="fas fa-envelope text-gray-400 mr-2"></i>{{ $client->email }}</p>
                    @if($client->phone)
                        <p><i class="fas fa-phone text-gray-400 mr-2"></i>{{ $client->phone }}</p>
                    @else
                        <p class="text-red-500 text-xs"><i class="fas fa-exclamation-circle mr-1"></i>No. WA belum diisi</p>
                    @endif
                    @if($client->address)
                        <p class="text-xs text-gray-500"><i class="fas fa-map-marker-alt text-gray-400 mr-2"></i>{{ Str::limit($client->address, 60) }}</p>
                    @endif
                    <div class="text-xs text-gray-500 flex flex-col gap-0.5">
                        @php
                            $lastOnline = $client->last_online_at;
                            if (is_numeric($lastOnline)) {
                                $lastOnline = \Carbon\Carbon::createFromTimestamp($lastOnline);
                            }
                        @endphp
                        <p class="flex items-center gap-2">
                            <i class="fas fa-circle text-[6px] text-emerald-400"></i>
                            <span>Online terakhir: {{ $lastOnline ? $lastOnline->diffForHumans() : '—' }}</span>
                        </p>
                        <p class="flex items-center gap-2">
                            <i class="fas fa-network-wired text-gray-400"></i>
                            <span>IP: {{ $client->last_ip_address ?? '—' }}</span>
                        </p>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-3 text-center text-xs">
                    <div class="bg-gray-50 rounded-xl p-2">
                        <p class="text-gray-400">Aktif</p>
                        <p class="text-base font-semibold text-amber-600">{{ $client->active_bookings_count }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-2">
                        <p class="text-gray-400">Total</p>
                        <p class="text-base font-semibold text-gray-800">{{ $client->bookings_count }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-2">
                        <p class="text-gray-400">Status</p>
                        <p class="text-base font-semibold {{ $client->address && $client->phone ? 'text-emerald-600' : 'text-red-500' }}">
                            {{ $client->address && $client->phone ? 'Lengkap' : 'Incomplete' }}
                        </p>
                    </div>
                </div>
            </div>
        @empty
            <div class="md:col-span-2 xl:col-span-3">
                <div class="bg-white rounded-2xl p-10 text-center border border-dashed border-gray-200">
                    <p class="text-gray-500">Belum ada klien yang ditemukan.</p>
                </div>
            </div>
        @endforelse
    </div>

    <div>
        {{ $clients->links() }}
    </div>
</div>
@endsection
