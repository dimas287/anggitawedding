@extends('layouts.app')
@section('title', 'Chat Admin')
@section('page-title', 'Chat dengan Admin')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden flex flex-col" style="height: 70vh;">

        {{-- Header --}}
        <div class="gold-gradient p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                <i class="fas fa-headset text-white"></i>
            </div>
            <div>
                <p class="text-white font-semibold text-sm">Admin Anggita WO</p>
                <p class="text-yellow-100 text-xs">{{ $booking->groom_name }} & {{ $booking->bride_name }}</p>
            </div>
        </div>

        {{-- Messages --}}
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
                        <a href="{{ route('user.chat.download', $chat->id) }}" target="_blank" class="block mt-1 underline text-xs opacity-75"><i class="fas fa-paperclip mr-1"></i>Lampiran</a>
                        @endif
                    </div>
                    <p class="text-xs text-gray-400 mt-1 {{ $chat->sender_id === auth()->id() ? 'text-right mr-1' : 'ml-1' }}">
                        {{ $chat->created_at->diffForHumans() }}
                    </p>
                </div>
            </div>
            @endforeach
            @if($chats->isEmpty())
            <div class="text-center text-gray-400 py-10">
                <i class="fas fa-comments text-4xl mb-3 block"></i>
                <p class="text-sm">Mulai percakapan dengan admin kami</p>
            </div>
            @endif
        </div>

        {{-- Input --}}
        <div class="p-4 bg-white border-t" x-data="chatSender()">
            <form @submit.prevent="send()" class="flex gap-2 items-end">
                <textarea x-model="message" @keydown.enter.prevent="send()" rows="1"
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
function chatSender() {
    return {
        message: '', loading: false,
        async send() {
            if (!this.message.trim()) return;
            this.loading = true;
            const msg = this.message;
            this.message = '';
            await fetch('{{ route("user.chat.send", $booking->id) }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                body: JSON.stringify({ message: msg })
            });
            this.loading = false;
            location.reload();
        }
    };
}
// Auto scroll to bottom
const box = document.getElementById('chat-box');
if(box) box.scrollTop = box.scrollHeight;
</script>
@endpush
