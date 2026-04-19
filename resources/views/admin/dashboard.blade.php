@extends('layouts.admin')
@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @php
        $statCards = [
            ['label'=>'Total Booking','value'=>$stats['total_bookings'],'icon'=>'fa-book','color'=>'blue','sub'=>$stats['active_bookings'].' aktif'],
            ['label'=>'Event Bulan Ini','value'=>$stats['completed_bookings'],'icon'=>'fa-calendar-check','color'=>'green','sub'=>'selesai'],
            ['label'=>'Konsultasi Pending','value'=>$stats['pending_consultations'],'icon'=>'fa-comments','color'=>'yellow','sub'=>'menunggu'],
            ['label'=>'Total Klien','value'=>$stats['total_clients'],'icon'=>'fa-users','color'=>'purple','sub'=>'terdaftar'],
        ];
        @endphp
        @foreach($statCards as $card)
        <div class="bg-white rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ $card['label'] }}</p>
                <div class="w-9 h-9 rounded-lg bg-{{ $card['color'] }}-100 flex items-center justify-center">
                    <i class="fas {{ $card['icon'] }} text-{{ $card['color'] }}-600 text-sm"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-800">{{ $card['value'] }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $card['sub'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Financial Summary --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl p-5 shadow-sm">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Pemasukan Bulan Ini</p>
            <p class="text-2xl font-bold text-green-600">Rp {{ number_format($stats['monthly_income'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Pengeluaran Bulan Ini</p>
            <p class="text-2xl font-bold text-red-500">Rp {{ number_format($stats['monthly_expense'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Profit Bulan Ini</p>
            <p class="text-2xl font-bold {{ $stats['monthly_profit'] >= 0 ? 'text-green-600' : 'text-red-500' }}">
                Rp {{ number_format($stats['monthly_profit'], 0, ',', '.') }}
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Recent Bookings --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-5 border-b flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">Booking Terbaru</h3>
                <a href="{{ route('admin.bookings.index') }}" class="text-xs text-yellow-600 hover:underline">Lihat semua</a>
            </div>
            <div class="divide-y">
                @forelse($recentBookings as $booking)
                <div class="p-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                    <div>
                        <p class="font-medium text-gray-800 text-sm">{{ $booking->couple_short_display }}</p>
                        <p class="text-xs text-gray-500">{{ $booking->booking_code }} • {{ $booking->package->name }} • {{ $booking->event_date->isoFormat('D MMM Y') }}</p>
                    </div>
                    <span class="px-2 py-1 rounded-full text-xs font-semibold flex-shrink-0
                        {{ ['pending'=>'bg-yellow-100 text-yellow-700','dp_paid'=>'bg-blue-100 text-blue-700','in_progress'=>'bg-indigo-100 text-indigo-700','completed'=>'bg-green-100 text-green-700','cancelled'=>'bg-red-100 text-red-600'][$booking->status] ?? 'bg-gray-100 text-gray-600' }}">
                        {{ $booking->status_label }}
                    </span>
                </div>
                @empty
                <div class="p-6 text-center text-gray-400 text-sm">Belum ada booking</div>
                @endforelse
            </div>
        </div>

        {{-- Upcoming Events --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-5 border-b flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">Event Mendatang</h3>
                <a href="{{ route('admin.calendar') }}" class="text-xs text-yellow-600 hover:underline">Kalender</a>
            </div>
            <div class="divide-y">
                @forelse($upcomingEvents as $event)
                <div class="p-4 flex items-center gap-4 hover:bg-gray-50 transition-colors">
                    <div class="w-12 h-12 rounded-xl gold-gradient flex flex-col items-center justify-center text-white flex-shrink-0">
                        <span class="text-xs font-medium">{{ $event->event_date->format('M') }}</span>
                        <span class="text-lg font-bold leading-none">{{ $event->event_date->format('d') }}</span>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-800 text-sm">{{ $event->couple_short_display }}</p>
                        <p class="text-xs text-gray-500">{{ $event->venue }} • {{ $event->package->name }}</p>
                    </div>
                    <a href="{{ route('admin.bookings.show', $event->id) }}" class="text-xs text-yellow-600 hover:underline">Detail</a>
                </div>
                @empty
                <div class="p-6 text-center text-gray-400 text-sm">Tidak ada event mendatang</div>
                @endforelse
            </div>
        </div>

        {{-- Pending Consultations --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden lg:col-span-2">
            <div class="p-5 border-b flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">Konsultasi Menunggu Konfirmasi</h3>
                <a href="{{ route('admin.consultations.index') }}" class="text-xs text-yellow-600 hover:underline">Lihat semua</a>
            </div>
            @forelse($pendingConsultations as $c)
            <div class="p-4 flex items-center justify-between border-b last:border-0 hover:bg-gray-50">
                <div>
                    <p class="font-medium text-gray-800 text-sm">{{ $c->name }}</p>
                    <p class="text-xs text-gray-500">{{ $c->email }} • {{ $c->phone }} • {{ $c->consultation_code }}</p>
                    <p class="text-xs text-blue-600 mt-0.5"><i class="fas fa-calendar mr-1"></i>{{ $c->preferred_date->isoFormat('D MMM Y') }} pukul {{ $c->preferred_time }} • {{ ucfirst($c->consultation_type) }}</p>
                </div>
                <div class="flex gap-2 flex-shrink-0">
                    <form action="{{ route('admin.consultations.status', $c->id) }}" method="POST">
                        @csrf @method('PUT')
                        <input type="hidden" name="status" value="confirmed">
                        <button class="text-xs font-medium px-3 py-1.5 bg-green-50 text-green-700 rounded-lg hover:bg-green-100">Konfirmasi</button>
                    </form>
                    <a href="{{ route('admin.consultations.show', $c->id) }}" class="text-xs font-medium px-3 py-1.5 bg-gray-50 text-gray-600 rounded-lg hover:bg-gray-100">Detail</a>
                </div>
            </div>
            @empty
            <div class="p-6 text-center text-gray-400 text-sm">Tidak ada konsultasi pending</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
