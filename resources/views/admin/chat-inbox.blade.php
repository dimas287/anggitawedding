@extends('layouts.admin')
@section('title', 'Kotak Masuk Chat')
@section('page-title', 'Kotak Masuk Chat')

@section('content')
<div class="space-y-4">
    @forelse($bookings as $booking)
    @php $unread = $booking->chats->where('sender_id','!=',$booking->user_id)->where('is_read',false)->count(); @endphp
    <div class="bg-white rounded-xl shadow-sm p-4 flex items-center justify-between hover:shadow-md transition-all">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full gold-gradient flex items-center justify-center text-white font-bold">
                {{ strtoupper(substr($booking->groom_name, 0, 1)) }}
            </div>
            <div>
                <p class="font-semibold text-gray-800">{{ $booking->groom_name }} & {{ $booking->bride_name }}</p>
                <p class="text-xs text-gray-500">{{ $booking->booking_code }} • {{ $booking->event_date->isoFormat('D MMM Y') }}</p>
                @if($booking->chats->last())
                <p class="text-xs text-gray-400 mt-0.5 truncate max-w-xs">
                    {{ $booking->chats->last()->sender_id === auth()->id() ? 'Anda: ' : '' }}{{ Str::limit($booking->chats->last()->message, 60) }}
                </p>
                @endif
            </div>
        </div>
        <div class="flex items-center gap-3">
            @if($unread > 0)
            <span class="w-6 h-6 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-bold">{{ $unread }}</span>
            @endif
            <span class="text-xs text-gray-400">{{ $booking->chats->last()?->created_at->diffForHumans() ?? '' }}</span>
            <a href="{{ route('admin.chat.index', $booking->id) }}" class="gold-gradient text-white font-semibold px-4 py-2 rounded-xl text-xs hover:shadow-md transition-all">
                Buka Chat
            </a>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-xl shadow-sm p-12 text-center">
        <i class="fas fa-inbox text-5xl text-gray-300 mb-4 block"></i>
        <p class="text-gray-400">Belum ada percakapan</p>
    </div>
    @endforelse
</div>
@endsection
