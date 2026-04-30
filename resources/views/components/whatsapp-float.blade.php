@php
    $whatsappLink = $footerInfo['phone_link'] 
                   ?? $footerInfo['socials']['whatsapp'] 
                   ?? 'https://wa.me/6281231122057';
    
    // Fallback jika hanya berisi '#'
    if ($whatsappLink === '#') {
        $whatsappLink = 'https://wa.me/6281231122057';
    }

    $whatsappNumber = Str::after($whatsappLink, 'wa.me/');
    $whatsappNumber = str_replace(['+', ' '], '', $whatsappNumber);
@endphp

<div id="floating-whatsapp-container" class="fixed bottom-8 right-8 z-[9999] group flex flex-col items-end gap-3 pointer-events-none transition-opacity duration-300">
    {{-- Message Tooltip --}}
    <div class="bg-white px-4 py-2 rounded-2xl shadow-xl border border-gray-100 transform translate-y-2 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300 pointer-events-auto">
        <p class="text-xs font-semibold text-gray-800 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
            Tanya Wedding Planner Kami?
        </p>
    </div>

    {{-- Main Button --}}
    <a href="{{ $whatsappLink }}" 
       target="_blank" 
       rel="noopener noreferrer"
       aria-label="Hubungi Anggita Wedding via WhatsApp"
       class="w-16 h-16 rounded-full bg-[#25D366] text-white shadow-[0_10px_30px_rgba(37,211,102,0.4)] flex items-center justify-center text-3xl hover:scale-110 hover:shadow-[0_15px_40px_rgba(37,211,102,0.6)] active:scale-95 transition-all duration-300 pointer-events-auto relative overflow-hidden">
        {{-- Shine effect --}}
        <div class="absolute inset-x-0 top-0 h-1/2 bg-gradient-to-b from-white/20 to-transparent"></div>
        <i class="fab fa-whatsapp relative z-10" aria-hidden="true"></i>
    </a>
</div>

<style>
    @keyframes bounce-subtle {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }
    .group:hover a {
        animation: none;
    }
    /* Bounce on initial idle */
    @media (prefers-reduced-motion: no-preference) {
        a {
            animation: bounce-subtle 4s ease-in-out infinite;
        }
    }
</style>
