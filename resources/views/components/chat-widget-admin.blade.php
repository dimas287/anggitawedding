@php
    $chatConversations = \App\Models\Booking::with([
            'user',
            'package',
            'chats' => function ($query) {
                $query->where('is_internal', false)
                    ->orderByDesc('created_at')
                    ->limit(1);
            }
        ])
        ->withCount(['chats as unread_count' => function ($query) {
            $query->where('is_internal', false)
                ->where('receiver_id', auth()->id())
                ->where('is_read', false);
        }])
        ->whereHas('chats', function ($query) {
            $query->where('is_internal', false);
        })
        ->orderByDesc(\DB::raw('(select max(created_at) from chats where chats.booking_id = bookings.id)'))
        ->take(10)
        ->get()
        ->map(function ($booking) {
            $latestChat = $booking->chats->first();
            $lastMessage = $latestChat?->attachment
                ? 'Mengirim lampiran'
                : ($latestChat?->message ?: 'Mulai percakapan');
            $lastTime = $latestChat?->created_at?->format('H:i');
            return [
                'id' => $booking->id,
                'name' => $booking->couple_short_display ?? trim(($booking->groom_name ?? 'Calon') . ' & ' . ($booking->bride_name ?? 'Pasangan')),
                'subtitle' => optional($booking->user)->email ?? 'Klien Anggita',
                'tag' => optional($booking->package)->name ?? 'Undangan Digital',
                'status' => ucfirst(str_replace('_', ' ', $booking->status ?? 'pending')),
                'avatar' => optional($booking->user)->avatar,
                'last_message' => ucfirst($lastMessage),
                'last_time' => $lastTime,
                'unread' => $booking->unread_count ?? 0,
            ];
        })->values();
@endphp

<style>
    .chat-scroll {
        scrollbar-width: thin;
        scrollbar-color: rgba(212,175,55,.8) rgba(32,44,51,.6);
    }
    .chat-scroll::-webkit-scrollbar {
        width: 8px;
    }
    .chat-scroll::-webkit-scrollbar-track {
        background: rgba(32,44,51,.6);
        border-radius: 999px;
    }
    .chat-scroll::-webkit-scrollbar-thumb {
        background: rgba(212,175,55,.85);
        border-radius: 999px;
    }
</style>

<div x-data="chatWidgetAdmin({
        conversations: @js($chatConversations),
        baseUrl: '{{ url('/admin/booking') }}',
        csrf: '{{ csrf_token() }}',
        adminName: '{{ auth()->user()->name }}',
        typingUrlBase: '{{ url('/admin/booking') }}'
    })"
    class="fixed bottom-6 right-6 z-[9999] space-y-3">
    <div x-show="open" x-cloak x-transition.origin.bottom.right class="w-full max-w-[420px] md:max-w-[640px] max-h-[85vh] rounded-3xl shadow-[0_24px_65px_rgba(15,23,42,0.25)] border border-gray-200 bg-white flex flex-col relative overflow-hidden"
         style="height: min(85vh, calc(100vh - 48px));">
        <div class="px-5 py-3 flex items-center justify-between text-white" style="background: linear-gradient(120deg,#c18c23,#f1d18a);">
            <div>
                <p class="text-xs tracking-[0.4em] text-white/70 uppercase">Inbox Klien</p>
                <p class="font-semibold text-base" x-text="adminName"></p>
            </div>
            <div class="flex items-center gap-3 text-white/80 text-sm">
                <span x-text="conversations.length + ' chat aktif'"></span>
                <button type="button" @click="close" class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center hover:bg-white/30">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="relative flex-1 min-h-0 flex flex-col md:grid md:grid-cols-5">
            <template x-if="isMobile && showMobileSidebar">
                <div class="absolute inset-0 bg-black/40 z-20 md:hidden" @click="showMobileSidebar = false"></div>
            </template>
            {{-- Left column --}}
            <div class="bg-[#151515] text-white flex flex-col min-h-0 md:relative md:col-span-2"
                 :class="isMobile ? 'absolute inset-y-0 left-0 w-[62vw] max-w-[220px] z-30 rounded-r-3xl shadow-2xl' : ''"
                 x-show="!isMobile || showMobileSidebar"
                 x-transition.opacity x-transition.duration-200>
                <div class="px-4 py-3 space-y-3">
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><i class="fas fa-search text-sm"></i></span>
                        <input type="text" x-model="searchQuery" placeholder="Cari atau mulai chat" class="w-full pl-9 pr-4 py-2.5 rounded-2xl bg-[#1f1f1f] text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#f1d18a]/60">
                    </div>
                    <div class="flex items-center gap-2 text-xs font-semibold">
                        <button type="button" @click="activeFilter = 'all'" :class="filterClass('all')" class="px-3 py-1.5 rounded-full">All</button>
                        <button type="button" @click="activeFilter = 'unread'" :class="filterClass('unread')" class="px-3 py-1.5 rounded-full">Unread</button>
                        <button type="button" @click="activeFilter = 'fav'" :class="filterClass('fav')" class="px-3 py-1.5 rounded-full">Starred</button>
                    </div>
                </div>
                <div class="flex-1 overflow-y-auto divide-y divide-white/5 chat-scroll">
                    <template x-if="filteredConversations().length === 0">
                        <p class="text-center text-xs text-white/50 py-6">Tidak ada chat</p>
                    </template>
                    <template x-for="conversation in filteredConversations()" :key="conversation.id">
                        <button type="button" @click="selectConversation(conversation.id)"
                                class="w-full text-left px-4 py-3 flex gap-3 items-start hover:bg-white/5"
                                :class="selected === conversation.id ? 'bg-white/10' : ''">
                            <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-sm font-semibold overflow-hidden">
                                <template x-if="conversation.avatar">
                                    <img :src="conversation.avatar" alt="avatar" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!conversation.avatar">
                                    <span x-text="conversation.name.substring(0,2)"></span>
                                </template>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="font-semibold text-sm truncate" x-text="conversation.name"></p>
                                    <span class="text-[11px] text-white/60" x-text="conversation.last_time ?? ''"></span>
                                </div>
                                <p class="text-[11px] text-white/60 truncate" x-text="conversation.last_message"></p>
                                <div class="flex items-center justify-between mt-1">
                                    <span class="text-[11px] text-[#f1d18a]" x-text="conversation.tag"></span>
                                    <template x-if="conversation.unread">
                                        <span class="text-[10px] font-bold bg-[#f1d18a] text-[#111b21] px-2 py-0.5 rounded-full" x-text="conversation.unread"></span>
                                    </template>
                                </div>
                            </div>
                        </button>
                    </template>
                </div>
            </div>

            {{-- Right column --}}
            <div class="bg-[#0f0f0f] text-white flex flex-col min-h-0 md:col-span-3">
                <div class="flex-1 flex flex-col items-center justify-center text-white/60" x-show="!selected">
                    <i class="fas fa-comments text-4xl mb-3"></i>
                    <p>Pilih klien untuk mulai percakapan</p>
                </div>
                <div class="flex-1 flex flex-col min-h-0" x-show="selected" x-cloak>
                    <div class="px-5 py-3 bg-[#1f1f1f] flex items-center justify-between border-b border-black/30">
                        <div class="flex items-center gap-3">
                            <button type="button" class="md:hidden text-white/70 text-lg" @click="showMobileSidebar = true">
                                <i class="fas fa-bars"></i>
                            </button>
                            <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-sm overflow-hidden">
                                <template x-if="selectedConversation?.avatar">
                                    <img :src="selectedConversation.avatar" class="w-full h-full object-cover" alt="avatar">
                                </template>
                                <template x-if="!selectedConversation?.avatar">
                                    <span x-text="selectedConversation?.name?.substring(0,2) ?? ''"></span>
                                </template>
                            </div>
                            <div>
                                <p class="font-semibold" x-text="selectedConversation?.name ?? 'Klien'"></p>
                                <p class="text-xs text-white/60" x-text="presenceText"></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 text-white/70 text-lg">
                            <i class="fas fa-search"></i>
                            <i class="far fa-paperclip"></i>
                            <i class="fas fa-ellipsis-v"></i>
                        </div>
                    </div>
                    <div class="flex-1 min-h-0 overflow-y-auto p-5 space-y-3 chat-scroll" x-ref="scroll" style="background:#121212 url('data:image/svg+xml,%3Csvg width=120 height=120 viewBox=0 0 60 60 xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=%23ffffff fill-opacity=0.03%3E%3Cpath d=\'M10 10h8v8h-8zM32 32h8v8h-8zM0 32h8v8H0zM24 0h8v8h-8z\'/%3E%3C/g%3E%3C/svg%3E');">
                        <template x-if="loading">
                            <div class="text-center text-xs text-white/60">
                                <i class="fas fa-spinner fa-spin mr-1"></i>Memuat pesan...
                            </div>
                        </template>
                        <template x-if="!loading && messages.length === 0">
                            <div class="text-center text-xs text-white/60">Belum ada pesan</div>
                        </template>
                        <template x-for="msg in messages" :key="msg.id">
                            <div :class="msg.sender_id === adminId ? 'text-right' : 'text-left'">
                                <div :class="msg.sender_id === adminId ? 'bg-[#b68b2d] text-white ml-auto rounded-3xl rounded-br-none' : 'bg-[#1f1f1f] text-white/85 mr-auto rounded-3xl rounded-bl-none'"
                                     class="inline-flex flex-col max-w-[85%] px-4 py-2 text-sm shadow-lg/30 border border-white/5 gap-1">
                                    <template x-if="msg.attachment_url">
                                        <div class="rounded-xl overflow-hidden">
                                            <template x-if="msg.attachment_type === 'image'">
                                                <img :src="msg.attachment_url" class="rounded-xl max-h-60 object-cover" />
                                            </template>
                                            <template x-if="msg.attachment_type === 'video'">
                                                <video :src="msg.attachment_url" controls class="rounded-xl max-h-60"></video>
                                            </template>
                                            <template x-if="msg.attachment_type && !['image','video'].includes(msg.attachment_type)">
                                                <a :href="msg.attachment_url" target="_blank" class="text-xs font-semibold underline flex items-center gap-1">
                                                    <i class="fas fa-paperclip"></i> Lihat Lampiran
                                                </a>
                                            </template>
                                        </div>
                                    </template>
                                    <p class="whitespace-pre-line" x-text="msg.message"></p>
                                    <div class="flex items-center gap-1 text-[10px] opacity-80" :class="msg.sender_id === adminId ? 'justify-end' : 'justify-start'">
                                        <span x-text="formatDate(msg.created_at)"></span>
                                        <template x-if="msg.sender_id === adminId">
                                            <i class="fas text-[10px]" :class="msg.is_read ? 'fa-check-double text-blue-300' : 'fa-check text-white/70'"></i>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template x-if="typing">
                            <div class="inline-flex items-center gap-2 bg-[#202c33] rounded-full px-3 py-1 text-[11px] text-white/70 shadow">
                                <span class="w-2 h-2 rounded-full bg-white/60 animate-pulse"></span> Klien sedang mengetik...
                            </div>
                        </template>
                    </div>
                    <div class="bg-[#171717] border-t border-black/40 px-5 py-4 space-y-3">
                        <input type="file" class="hidden" x-ref="file" accept="image/*,video/*,application/pdf" @change="handleFileChange($event)">
                        <div class="flex items-center gap-3 text-white/70 text-xl">
                            <button type="button" @click="$refs.file.click()" :disabled="sending" class="hover:text-[#f1d18a]"><i class="far fa-plus-square"></i></button>
                            <button type="button" class="hover:text-[#f1d18a]"><i class="far fa-smile"></i></button>
                            <button type="button" class="hover:text-[#f1d18a]"><i class="fas fa-paperclip"></i></button>
                        </div>
                        <div class="flex items-end gap-3">
                            <textarea rows="1" x-model="form.message" @keydown.enter.prevent="sendMessage" @input="handleTyping()" :disabled="sending"
                                      class="flex-1 bg-[#222] text-white rounded-2xl px-4 py-3 text-sm border border-white/10 focus:ring-2 focus:ring-[#f1d18a]/40 focus:border-transparent placeholder-white/40 min-h-[48px]"
                                      placeholder="Ketik pesan..."></textarea>
                            <button type="button" @click="sendMessage" :disabled="sending || (!form.message.trim() && !form.attachmentFile)"
                                    class="w-12 h-12 rounded-full text-[#0b141a] flex items-center justify-center text-lg shadow-lg disabled:opacity-50"
                                    style="background: linear-gradient(135deg,#c18c23,#f1d18a);">
                                <span x-show="!sending"><i class="fas fa-paper-plane"></i></span>
                                <span x-show="sending" class="animate-spin"><i class="fas fa-spinner"></i></span>
                            </button>
                        </div>
                        <template x-if="form.attachmentName">
                            <div class="flex items-center justify-between text-xs text-white bg-[#222] border border-white/10 rounded-2xl px-3 py-2">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-file"></i>
                                    <span x-text="form.attachmentName"></span>
                                </div>
                                <button type="button" class="text-[#25d366]" @click="resetAttachment">Hapus</button>
                            </div>
                        </template>
                        <label class="inline-flex items-center gap-2 text-[11px] text-white/70">
                            <input type="checkbox" x-model="form.is_internal" class="rounded border-gray-300 text-[#f1d18a] focus:ring-[#f1d18a]/40">
                            Catatan internal (hanya admin)
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <button type="button" @click="toggle" class="relative w-16 h-16 rounded-full gold-gradient shadow-2xl text-white flex flex-col items-center justify-center hover:scale-105 transition-transform focus:outline-none focus:ring-4 focus:ring-yellow-200">
        <i class="fas text-lg" :class="open ? 'fa-times' : 'fa-comments'"></i>
        <span class="text-[10px] font-semibold tracking-wide mt-0.5">Inbox</span>
        <span class="absolute -top-1 -right-1 text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-white text-yellow-700 shadow" x-text="conversations.length"></span>
    </button>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('chatWidgetAdmin', (config) => ({
                    open: false,
                    selected: null,
                    selectedConversation: null,
                    messages: [],
                    loading: false,
                    sending: false,
                    pollTimer: null,
                    presenceText: '',
                    searchQuery: '',
                    activeFilter: 'all',
                    adminId: {{ auth()->id() }},
                    isMobile: window.innerWidth < 768,
                    showMobileSidebar: true,
                    form: { message: '', is_internal: false, attachmentFile: null, attachmentName: null },
                    typing: false,
                    typingTimer: null,
                    typingStateSent: null,
                    ...config,
                    init() {
                        this.handleResize = () => {
                            this.isMobile = window.innerWidth < 768;
                            if (!this.isMobile) {
                                this.showMobileSidebar = true;
                            }
                        };
                        window.addEventListener('resize', this.handleResize);
                        this.handleResize();
                    },
                    toggle() {
                        this.open = !this.open;
                        if (this.open && this.selected) {
                            this.fetchMessages();
                        } else if (!this.open) {
                            this.clearPoll();
                            this.showMobileSidebar = false;
                        }
                        if (this.open && this.isMobile) {
                            this.showMobileSidebar = true;
                        }
                    },
                    close() {
                        this.open = false;
                        this.clearPoll();
                        this.showMobileSidebar = false;
                    },
                    selectConversation(id) {
                        this.selected = id;
                        this.selectedConversation = this.conversations.find((item) => item.id === id) || null;
                        this.presenceText = '';
                        this.fetchMessages();
                    },
                    formatDate(dt) {
                        return dt ? new Date(dt).toLocaleString('id-ID', { hour: '2-digit', minute: '2-digit', day: '2-digit', month: 'short' }) : '';
                    },
                    apiPath(path) {
                        return `${this.baseUrl}/${this.selected}${path}`;
                    },
                    clearPoll() {
                        if (this.pollTimer) {
                            clearTimeout(this.pollTimer);
                            this.pollTimer = null;
                        }
                    },
                    schedulePoll() {
                        this.clearPoll();
                        this.pollTimer = setTimeout(() => {
                            if (this.open && this.selected) this.fetchMessages();
                        }, 6000);
                    },
                    async fetchMessages() {
                        if (!this.selected) return;
                        this.loading = this.messages.length === 0;
                        try {
                            const res = await fetch(this.apiPath('/pesan/messages'), { headers: { 'Accept': 'application/json' } });
                            if (!res.ok) throw new Error('Gagal memuat pesan');
                            const data = await res.json();
                            this.messages = data.messages ?? data;
                            this.typing = data.typing ?? false;
                            if (data.presence) {
                                this.presenceText = data.presence.client_online
                                    ? 'Online'
                                    : (data.presence.client_last_online ? `Terakhir aktif ${data.presence.client_last_online}` : 'Offline');
                            }
                            this.$nextTick(() => {
                                if (this.$refs.scroll) this.$refs.scroll.scrollTop = this.$refs.scroll.scrollHeight;
                            });
                        } catch (e) {
                            console.error(e);
                        } finally {
                            this.loading = false;
                            this.schedulePoll();
                        }
                    },
                    async sendMessage() {
                        if (!this.selected || this.sending) return;
                        if (!this.form.message.trim() && !this.form.attachmentFile) return;
                        this.sending = true;
                        try {
                            const payload = new FormData();
                            payload.append('message', this.form.message);
                            payload.append('is_internal', this.form.is_internal ? 1 : 0);
                            if (this.form.attachmentFile) {
                                payload.append('attachment', this.form.attachmentFile);
                            }
                            const res = await fetch(this.apiPath('/pesan'), {
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
                            await this.sendTyping(false, true);
                            await this.fetchMessages();
                        } catch (e) {
                            console.error(e);
                            alert('Gagal mengirim pesan. Coba lagi.');
                        } finally {
                            this.sending = false;
                        }
                    },
                    handleTyping() {
                        if (!this.selected) return;
                        this.sendTyping(true);
                        if (this.typingTimer) clearTimeout(this.typingTimer);
                        this.typingTimer = setTimeout(() => this.sendTyping(false), 2000);
                    },
                    async sendTyping(state, force = false) {
                        if (!this.selected || (!force && this.typingStateSent === state)) return;
                        this.typingStateSent = state;
                        try {
                            await fetch(this.apiPath('/pesan/typing'), {
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
                    },
                    handleFileChange(event) {
                        const file = event.target.files[0];
                        if (!file) return;
                        this.form.attachmentFile = file;
                        this.form.attachmentName = file.name;
                    },
                    resetAttachment() {
                        this.form.attachmentFile = null;
                        this.form.attachmentName = null;
                        if (this.$refs.file) {
                            this.$refs.file.value = '';
                        }
                    },
                    filteredConversations() {
                        let list = this.conversations;
                        if (this.activeFilter === 'unread') {
                            list = list.filter(item => (item.unread ?? 0) > 0);
                        }
                        if (this.searchQuery.trim()) {
                            const q = this.searchQuery.toLowerCase();
                            list = list.filter(item =>
                                item.name.toLowerCase().includes(q) ||
                                (item.subtitle?.toLowerCase().includes(q))
                            );
                        }
                        return list;
                    },
                    filterClass(type) {
                        return this.activeFilter === type ? 'bg-[#f1d18a] text-[#111b21]' : 'bg-[#1f1f1f] text-white/70 border border-white/10';
                    }
                }));
            });
        </script>
    @endpush
@endonce
