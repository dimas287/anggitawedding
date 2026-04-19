@extends('layouts.admin')
@section('title', 'Kalender Event')
@section('page-title', 'Kalender Event')

@section('content')
<div class="space-y-5">
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="p-4 border-b flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">Kalender Booking & Konsultasi</h3>
            <div class="flex gap-3 text-xs">
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full gold-gradient inline-block"></span>Booking</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-blue-400 inline-block"></span>Konsultasi</span>
            </div>
        </div>
        <div class="p-4">
            <div id="calendar" style="min-height: 600px;"></div>
        </div>
    </div>

    {{-- Upcoming Events List --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="p-4 border-b"><h3 class="font-semibold text-gray-800">Event 30 Hari Ke Depan</h3></div>
        <div class="divide-y">
            @forelse($upcomingBookings as $b)
            <div class="p-4 flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl gold-gradient flex flex-col items-center justify-center text-white flex-shrink-0">
                    <span class="text-xs">{{ $b->event_date->format('M') }}</span>
                    <span class="text-xl font-bold leading-tight">{{ $b->event_date->format('d') }}</span>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-gray-800">{{ $b->couple_short_display }}</p>
                    <p class="text-xs text-gray-500">{{ $b->package->name }} • {{ $b->venue }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $b->booking_code }}</p>
                </div>
                <a href="{{ route('admin.bookings.show', $b->id) }}" class="text-xs font-medium px-3 py-1.5 gold-gradient text-white rounded-lg">Detail</a>
            </div>
            @empty
            <div class="p-8 text-center text-gray-400 text-sm">Tidak ada event dalam 30 hari ke depan</div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        plugins: [FullCalendar.dayGridPlugin, FullCalendar.listPlugin],
        initialView: 'dayGridMonth',
        locale: 'id',
        headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,listWeek' },
        events: '{{ route("admin.calendar.events") }}',
        eventClick: function(info) {
            if (info.event.extendedProps.url) window.location.href = info.event.extendedProps.url;
        },
        eventContent: function(arg) {
            return { html: `<div class="px-1 py-0.5 text-xs truncate font-medium">${arg.event.title}</div>` };
        }
    });
    calendar.render();
});
</script>
@endpush
