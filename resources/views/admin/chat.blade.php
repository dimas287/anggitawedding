@extends('layouts.admin')
@section('title', 'Chat – ' . $booking->groom_name . ' & ' . $booking->bride_name)
@section('page-title', 'Chat: ' . $booking->groom_name . ' & ' . $booking->bride_name)

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden flex flex-col" style="height: 72vh;">
        <div class="gold-gradient p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center text-white font-bold">
                {{ strtoupper(substr($booking->groom_name, 0, 1)) }}
            </div>
            <div>
                <p class="text-white font-semibold text-sm">{{ $booking->groom_name }} & {{ $booking->bride_name }}</p>
                <p class="text-yellow-100 text-xs">{{ $booking->booking_code }} • {{ $booking->event_date->isoFormat('D MMM Y') }}</p>
            </div>
            <a href="{{ route('admin.bookings.show', $booking->id) }}" class="ml-auto text-white/70 hover:text-white text-xs underline">Lihat Booking</a>
        </div>

        <div id="chat-box" class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50">
            @foreach($chats as $chat)
            <div class="flex {{ $chat->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-xs lg:max-w-md">
                    @if($chat->sender_id !== auth()->id())
                    <p class="text-xs text-gray-400 mb-1 ml-1">{{ $chat->sender->name }}</p>
                    @endif
                    <div class="px-4 py-2.5 rounded-2xl text-sm {{ $chat->sender_id === auth()->id() ? 'gold-gradient text-white rounded-tr-sm' : 'bg-white shadow-sm text-gray-700 rounded-tl-sm' }}">
                        {{ $chat->message }}
                        @if($chat->attachment)
                        <a href="{{ route('admin.chat.download', $chat->id) }}" target="_blank" class="block mt-1 underline text-xs opacity-75"><i class="fas fa-paperclip mr-1"></i>Lampiran</a>
                        @endif
                    </div>
                    <p class="text-xs text-gray-400 mt-1 {{ $chat->sender_id === auth()->id() ? 'text-right mr-1' : 'ml-1' }}">
                        {{ $chat->created_at->format('H:i') }} • {{ $chat->is_read ? '✓✓' : '✓' }}
                    </p>
                </div>
            </div>
            @endforeach
            @if($chats->isEmpty())
            <div class="text-center text-gray-400 py-10">
                <i class="fas fa-comments text-4xl mb-3 block"></i>
                <p class="text-sm">Belum ada percakapan</p>
            </div>
            @endif
        </div>

        <div class="p-4 bg-white border-t" x-data="{ message: '', loading: false }">
            <form @submit.prevent="sendMsg()" class="flex gap-2 items-end">
                <textarea x-model="message" @keydown.enter.prevent="sendMsg()" rows="1"
                          placeholder="Ketik pesan..."
                          class="flex-1 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 resize-none max-h-24"></textarea>
                <button type="submit" :disabled="!message.trim() || loading"
                        class="w-10 h-10 gold-gradient text-white rounded-xl flex items-center justify-center flex-shrink-0 hover:shadow-md transition-all disabled:opacity-50">
                    <i :class="loading ? 'fa-spinner fa-spin' : 'fa-paper-plane'" class="fas text-sm"></i>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function sendMsg() {
    const msgEl = document.querySelector('[x-model="message"]');
    if (!msgEl || !msgEl.value.trim()) return;
    const msg = msgEl.value;
    msgEl.value = '';
    fetch('{{ route("admin.chat.send", $booking->id) }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
        body: JSON.stringify({ message: msg })
    }).then(() => location.reload());
}
const box = document.getElementById('chat-box');
if (box) box.scrollTop = box.scrollHeight;
</script>
@endpush
