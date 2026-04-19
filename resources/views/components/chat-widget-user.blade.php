@php
    $latestBooking = auth()->user()->bookings()->with('invitation.template')->latest()->first();
    $coupleLabel = $latestBooking
        ? trim(($latestBooking->groom_short_name ?? $latestBooking->groom_name) . ' & ' . ($latestBooking->bride_short_name ?? $latestBooking->bride_name))
        : auth()->user()->name;
    $adminName = \App\Models\User::where('role', 'admin')->value('name') ?? 'Admin Anggita WO';
@endphp

<div x-data="chatWidgetUser({
        bookingId: {{ $latestBooking?->id ?? 'null' }},
        fetchUrl: '{{ $latestBooking ? route('user.chat.messages', $latestBooking) : '' }}',
        sendUrl: '{{ $latestBooking ? route('user.chat.send', $latestBooking) : '' }}',
        typingUrl: '{{ $latestBooking ? route('user.chat.typing', $latestBooking) : '' }}',
        userId: {{ auth()->id() }},
        coupleLabel: @js($coupleLabel),
        adminName: @js($adminName),
        csrf: '{{ csrf_token() }}'
    })"
    class="fixed bottom-6 right-6 z-[9999] space-y-3">
    <div x-show="open" x-cloak x-transition.origin.bottom.right class="w-[330px] max-w-[90vw] rounded-3xl shadow-2xl border border-white/40 overflow-hidden"
         style="background: radial-gradient(circle at 0% 0%, #ffd8b5 0%, #f9bde8 45%, #c5bcff 100%);">
        <div class="px-4 py-3 flex items-center justify-between text-white">
            <div>
                <p class="text-xs uppercase tracking-[0.4em] text-white/70" x-text="adminName">Admin Anggita WO</p>
                <p class="font-semibold leading-tight" x-text="coupleLabel">Nama Klien</p>
                <p class="text-[11px] text-white/70" x-text="presenceText"></p>
            </div>
            <button type="button" @click="open = false" class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center hover:bg-white/30">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="bg-white/95 backdrop-blur px-4 pt-4 pb-3 border-t border-white/40 flex flex-col gap-3">
            <template x-if="!bookingId">
                <div class="h-64 bg-white rounded-2xl flex flex-col items-center justify-center text-gray-400 border border-dashed border-gray-200 px-4 text-center">
                    <i class="fas fa-info-circle text-3xl mb-3"></i>
                    <p class="text-sm font-medium text-gray-500">Anda belum memiliki booking aktif. Buat booking untuk mulai chat.</p>
                </div>
            </template>
            <template x-if="bookingId">
                <div class="h-64 bg-white rounded-2xl border border-gray-100 flex flex-col">
                    <div class="flex-1 overflow-y-auto px-3 py-3 space-y-3" x-ref="scroll">
                        <template x-if="!loading && messages.length === 0">
                            <div class="h-full flex flex-col items-center justify-center text-gray-400 text-sm">
                                <i class="fas fa-comments text-3xl mb-3"></i>
                                <p>Mulai percakapan dengan admin kami</p>
                            </div>
                        </template>
                        <template x-for="msg in messages" :key="msg.id">
                            <div :class="msg.sender_id === userId ? 'text-right' : 'text-left'">
                                <div :class="msg.sender_id === userId ? 'bg-gradient-to-r from-purple-100 to-pink-100 text-gray-800 ml-auto' : 'bg-gray-50 text-gray-700 mr-auto'"
                                     class="inline-flex flex-col max-w-[85%] rounded-2xl px-3 py-2 text-sm border border-white/60 gap-2">
                                    <p class="font-semibold text-[11px] text-gray-500" x-text="msg.sender?.name ?? (msg.sender_id === userId ? 'Anda' : adminName)"></p>

                                    <template x-if="msg.attachment_url">
                                        <div>
                                            <template x-if="msg.attachment_type === 'image'">
                                                <img :src="msg.attachment_url" class="rounded-xl max-h-48 object-cover" />
                                            </template>
                                            <template x-if="msg.attachment_type === 'video'">
                                                <video :src="msg.attachment_url" controls class="rounded-xl max-h-48"></video>
                                            </template>
                                            <template x-if="msg.attachment_type && !['image','video'].includes(msg.attachment_type)">
                                                <a :href="msg.attachment_url" target="_blank" class="text-xs font-semibold text-purple-600 underline flex items-center gap-1">
                                                    <i class="fas fa-paperclip"></i> Lihat Lampiran
                                                </a>
                                            </template>
                                        </div>
                                    </template>

                                    <p class="whitespace-pre-line" x-text="msg.message"></p>
                                    <div class="flex items-center gap-1 text-[10px] text-gray-400 mt-1" :class="msg.sender_id === userId ? 'justify-end' : 'justify-start'">
                                        <span x-text="formatDate(msg.created_at)"></span>
                                        <template x-if="msg.sender_id === userId">
                                            <i class="fas" :class="msg.is_read ? 'fa-check-double text-blue-400' : 'fa-check text-gray-400'"></i>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template x-if="loading">
                            <div class="flex items-center gap-2 text-xs text-gray-400">
                                <span class="animate-spin"><i class="fas fa-spinner"></i></span> Memuat pesan...
                            </div>
                        </template>
                        <template x-if="typing">
                            <div class="flex items-center gap-2 text-xs text-gray-400">
                                <span class="w-2 h-2 rounded-full bg-gray-300 animate-pulse"></span>
                                Admin sedang mengetik...
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            <div>
                <label class="text-xs font-semibold text-gray-500">Ketik pesan</label>
                <input type="file" class="hidden" x-ref="file" accept="image/*,video/*,application/pdf" @change="handleFileChange($event)">
                <div class="relative mt-2">
                    <input type="text" x-model="form.message" @keydown.enter.prevent="sendMessage" @input="handleTyping()"
                           :disabled="!bookingId || sending"
                           class="w-full rounded-2xl border border-gray-200 px-4 py-3 pr-24 text-sm focus:ring-2 focus:ring-purple-200 focus:border-purple-300 disabled:opacity-60"
                           placeholder="Tulis pesan...">
                    <div class="absolute inset-y-0 right-1 flex items-center gap-1">
                        <button type="button" @click="$refs.file.click()" :disabled="!bookingId || sending"
                                class="w-9 h-9 rounded-xl text-purple-500 bg-purple-50 flex items-center justify-center text-base disabled:opacity-50">
                            <i class="fas fa-paperclip"></i>
                        </button>
                        <button type="button" @click="sendMessage" :disabled="!bookingId || sending || (!form.message.trim() && !form.attachmentFile)"
                                class="w-10 h-10 rounded-xl flex items-center justify-center text-white disabled:opacity-40"
                                style="background: linear-gradient(120deg, #ffb074, #f38bdc, #9f8bff);">
                            <span x-show="!sending"><i class="fas fa-paper-plane text-sm"></i></span>
                            <span x-show="sending" class="animate-spin text-sm"><i class="fas fa-spinner"></i></span>
                        </button>
                    </div>
                </div>
                <template x-if="form.attachmentPreview">
                    <div class="mt-3 border border-dashed border-purple-200 rounded-2xl p-2 text-xs flex items-center justify-between">
                        <div class="flex items-center gap-2 text-gray-600">
                            <i class="fas fa-file-upload"></i>
                            <span x-text="form.attachmentName"></span>
                        </div>
                        <button type="button" class="text-purple-500 text-xs" @click="resetAttachment">Hapus</button>
                    </div>
                </template>
                <p class="text-[11px] text-gray-400 text-center mt-2">Pesan dikirim ke admin & mendukung lampiran foto/video/PDF.</p>
            </div>
        </div>
    </div>

    <button type="button" @click="toggle()" class="w-16 h-16 rounded-full shadow-[0_18px_35px_rgba(175,135,255,0.4)] text-white flex flex-col items-center justify-center hover:scale-105 transition-transform focus:outline-none focus:ring-4 focus:ring-purple-200"
            style="background: linear-gradient(135deg, #ffb074, #f38bdc 50%, #9f8bff);">
        <i class="fas text-lg" :class="open ? 'fa-times' : 'fa-comment-dots'"></i>
        <span class="text-[10px] font-semibold tracking-wide mt-0.5">Chat</span>
    </button>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('chatWidgetUser', (config) => ({
                    open: false,
                    loading: false,
                    sending: false,
                    messages: [],
                    form: { message: '', attachmentFile: null, attachmentPreview: null, attachmentName: null },
                    pollTimer: null,
                    typingTimer: null,
                    typing: false,
                    typingStateSent: null,
                    presenceText: '',
                    ...config,
                    toggle() {
                        this.open = !this.open;
                        if (this.open && this.bookingId) {
                            this.fetchMessages();
                        } else if (!this.open && this.pollTimer) {
                            clearTimeout(this.pollTimer);
                        }
                    },
                    formatDate(dt) {
                        if (!dt) return '';
                        return new Date(dt).toLocaleString('id-ID', { hour: '2-digit', minute: '2-digit', day: '2-digit', month: 'short' });
                    },
                    async fetchMessages() {
                        if (!this.fetchUrl) return;
                        this.loading = this.messages.length === 0;
                        try {
                            const res = await fetch(this.fetchUrl, { headers: { 'Accept': 'application/json' } });
                            if (!res.ok) throw new Error('Gagal memuat pesan');
                            const data = await res.json();
                            this.messages = data.messages ?? data;
                            this.typing = data.typing ?? false;
                            if (data.presence) {
                                this.presenceText = data.presence.admin_online
                                    ? 'Online'
                                    : (data.presence.admin_last_online ? `Terakhir aktif ${data.presence.admin_last_online}` : 'Offline');
                            }
                            this.$nextTick(() => this.scrollToBottom());
                        } catch (e) {
                            console.error(e);
                        } finally {
                            this.loading = false;
                            this.schedulePoll();
                        }
                    },
                    schedulePoll() {
                        if (this.pollTimer) {
                            clearTimeout(this.pollTimer);
                        }
                        this.pollTimer = setTimeout(() => {
                            if (this.open) this.fetchMessages();
                        }, 8000);
                    },
                    scrollToBottom() {
                        if (this.$refs.scroll) {
                            this.$refs.scroll.scrollTop = this.$refs.scroll.scrollHeight;
                        }
                    },
                    async sendMessage() {
                        if (!this.sendUrl || this.sending) return;
                        if (!this.form.message.trim() && !this.form.attachmentFile) return;
                        this.sending = true;
                        try {
                            const payload = new FormData();
                            payload.append('message', this.form.message);
                            if (this.form.attachmentFile) {
                                payload.append('attachment', this.form.attachmentFile);
                            }
                            const res = await fetch(this.sendUrl, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': this.csrf,
                                    'Accept': 'application/json'
                                },
                                body: payload
                            });
                            if (!res.ok) throw new Error('Gagal mengirim pesan');
                            this.form.message = '';
                            this.resetAttachment();
                            await this.fetchMessages();
                        } catch (e) {
                            console.error(e);
                            alert('Gagal mengirim pesan. Silakan coba lagi.');
                        } finally {
                            this.sending = false;
                        }
                    },
                    handleFileChange(event) {
                        const file = event.target.files[0];
                        if (!file) return;
                        this.form.attachmentFile = file;
                        this.form.attachmentName = file.name;
                        if (file.type.startsWith('image/')) {
                            const reader = new FileReader();
                            reader.onload = () => {
                                this.form.attachmentPreview = reader.result;
                            };
                            reader.readAsDataURL(file);
                        } else {
                            this.form.attachmentPreview = 'file';
                        }
                    },
                    resetAttachment() {
                        this.form.attachmentFile = null;
                        this.form.attachmentPreview = null;
                        this.form.attachmentName = null;
                        if (this.$refs.file) {
                            this.$refs.file.value = '';
                        }
                    },
                    handleTyping() {
                        if (!this.sendUrl || !this.bookingId) return;
                        this.sendTyping(true);
                        if (this.typingTimer) clearTimeout(this.typingTimer);
                        this.typingTimer = setTimeout(() => this.sendTyping(false), 2000);
                    },
                    async sendTyping(state) {
                        if (!this.typingUrl || this.typingStateSent === state) return;
                        this.typingStateSent = state;
                        try {
                            await fetch(this.typingUrl, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': this.csrf,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({ is_typing: state })
                            });
                        } catch (e) {
                            console.warn('typing failed', e);
                        }
                    }
                }));
            });
        </script>
    @endpush
@endonce
