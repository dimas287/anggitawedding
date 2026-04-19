<div x-data="{ open: false }" class="fixed bottom-6 right-6 z-[9999] space-y-3">
    <div x-show="open" x-transition.origin.bottom.right x-cloak class="w-80 max-w-[90vw] bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden">
        <div class="gold-gradient px-4 py-3 text-white flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-widest text-yellow-100">Bantuan</p>
                <p class="font-semibold text-sm">Chat Support</p>
            </div>
            <button type="button" @click="open = false" class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center hover:bg-white/30">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-4 space-y-4">
            <div class="text-xs text-gray-500 leading-relaxed">
                <p class="font-semibold text-gray-800">Tim Support</p>
                <p>Kami siap membantu menanyakan status booking, progres undangan, atau kebutuhan lainnya.</p>
            </div>
            <div class="space-y-3 text-sm">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-yellow-100 text-yellow-700 flex items-center justify-center font-semibold">A</div>
                    <div class="bg-gray-50 border border-gray-100 rounded-2xl px-3 py-2">
                        <p class="font-semibold text-gray-800 text-xs">Admin Anggita</p>
                        <p class="text-gray-600">Halo! Ada yang bisa kami bantu?</p>
                        <p class="text-[11px] text-gray-400 mt-1">Baru saja</p>
                    </div>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-600">Tulis Pesan</label>
                    <textarea rows="3" class="w-full mt-1 border border-gray-200 rounded-2xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400" placeholder="Contoh: Saya ingin konfirmasi jadwal konsultasi..."></textarea>
                </div>
                <button type="button" class="w-full gold-gradient text-white font-semibold py-2.5 rounded-xl text-sm flex items-center justify-center gap-2">
                    <i class="fas fa-paper-plane text-xs"></i> Kirim
                </button>
                <p class="text-[11px] text-center text-gray-400">Chat real-time segera hadir. Untuk sementara, pesan akan diteruskan ke admin.</p>
            </div>
        </div>
    </div>

    <button type="button" @click="open = !open" class="relative w-14 h-14 rounded-full gold-gradient shadow-2xl text-white flex flex-col items-center justify-center hover:scale-105 transition-transform focus:outline-none focus:ring-4 focus:ring-yellow-200">
        <i class="fas text-lg" :class="open ? 'fa-times' : 'fa-comments'"></i>
        <span class="text-[10px] font-semibold tracking-wide mt-0.5">Chat</span>
        <span class="absolute -top-1 -right-1 text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-white text-yellow-700 shadow">1</span>
    </button>
</div>
