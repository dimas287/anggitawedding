@extends('layouts.guest')
@section('title', 'FAQ – Anggita WO')
@section('meta_description', 'Temukan jawaban atas pertanyaan seputar layanan Anggita Wedding Organizer, proses booking, kebijakan pembayaran, hingga detail paket pernikahan.')

@push('head')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "Kapan waktu terbaik untuk memesan paket wedding?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Idealnya pemesanan dilakukan 6-12 bulan sebelum hari H agar kami dapat memastikan ketersediaan vendor terbaik dan memiliki waktu cukup untuk persiapan."
      }
    },
    {
      "@type": "Question",
      "name": "Apakah bisa custom paket di luar yang tersedia?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Tentu saja! Semua paket kami bersifat fleksibel. Anda dapat berkonsultasi dengan tim planner kami untuk menyesuaikan item, vendor, atau budget sesuai impian Anda."
      }
    },
    {
      "@type": "Question",
      "name": "Bagaimana sistem pembayaran di Anggita Wedding?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Sistem kami sangat aman dan terstruktur. Booking fee (DP 30%) untuk mengunci tanggal, termin kedua 40% (H-30), dan pelunasan 30% (H-7). Kami juga menyediakan opsi pembayaran online yang aman."
      }
    }
  ]
}
</script>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-[#0A0A0A] pt-24 pb-16 transition-colors duration-500">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <span class="text-yellow-600 dark:text-yellow-500 text-sm font-semibold uppercase tracking-widest">Pertanyaan Umum</span>
            <h1 class="font-playfair text-5xl font-bold text-gray-800 dark:text-white mt-2">FAQ</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-4 max-w-xl mx-auto">Temukan jawaban atas pertanyaan yang sering diajukan. Masih ada pertanyaan? Jangan ragu untuk menghubungi kami!</p>
        </div>

        <div class="space-y-4 mb-14" x-data="{ open: null }">
            @php
            $faqs = [
                ['q' => 'Berapa jauh hari sebelumnya saya harus memesan?', 'a' => 'Kami menyarankan Anda memesan minimal 6–12 bulan sebelum hari pernikahan untuk memastikan ketersediaan tanggal dan vendor terbaik. Namun kami juga dapat mengakomodasi pemesanan mendadak tergantung ketersediaan.'],
                ['q' => 'Berapa besar Down Payment (DP) yang diperlukan?', 'a' => 'DP yang diperlukan adalah sebesar 30% dari total harga paket. Setelah DP dibayarkan, tanggal pernikahan Anda resmi ter-booking dan tim kami akan mulai bekerja.'],
                ['q' => 'Apakah harga paket bisa dinegosiasikan?', 'a' => 'Harga paket sudah termasuk semua layanan yang tertera. Namun kami menyediakan layanan kustomisasi paket untuk kebutuhan khusus. Silakan konsultasikan dengan tim kami untuk opsi terbaik.'],
                ['q' => 'Bagaimana jika saya ingin menambah layanan di luar paket?', 'a' => 'Anda dapat menambahkan layanan tambahan (add-on) dengan biaya terpisah. Diskusikan kebutuhan Anda dengan admin melalui fitur chat di dashboard klien kami.'],
                ['q' => 'Apakah ada konsultasi gratis sebelum booking?', 'a' => 'Ya! Kami menyediakan sesi konsultasi gratis tanpa komitmen. Anda bisa memilih konsultasi online (video call) maupun offline di kantor kami. Daftar sekarang melalui halaman Konsultasi.'],
                ['q' => 'Apa yang terjadi jika acara dibatalkan?', 'a' => 'Pembatalan lebih dari 90 hari sebelum acara akan dikenakan biaya admin 10% dari DP. Pembatalan 30–90 hari dikenakan 50% DP. Pembatalan kurang dari 30 hari, DP tidak dapat dikembalikan. Detail lengkap ada di kontrak.'],
                ['q' => 'Bagaimana sistem pembayaran yang tersedia?', 'a' => 'Kami menerima pembayaran via transfer bank (BCA, BNI, BRI, Mandiri), dompet digital (GoPay, OVO, DANA, ShopeePay), QRIS, dan kartu kredit/debit melalui platform Midtrans yang aman.'],
                ['q' => 'Apakah undangan digital termasuk dalam semua paket?', 'a' => 'Ya, undangan digital termasuk dalam semua paket (Silver, Gold, dan Premium). Anda dapat mengkustomisasi template, warna, font, dan konten undangan melalui dashboard klien kami.'],
                ['q' => 'Berapa lama proses pembuatan undangan digital?', 'a' => 'Setelah Anda mengisi semua data di dashboard, undangan digital siap dalam 1–3 hari kerja. Anda juga bisa langsung mengedit sendiri setelah DP dibayarkan.'],
                ['q' => 'Apakah tim hadir selama hari pernikahan?', 'a' => 'Ya! Tim koordinator kami akan hadir dari persiapan hingga akhir acara. Jumlah koordinator disesuaikan dengan paket yang dipilih (1 orang untuk Silver, 2 untuk Gold, 5 untuk Premium).'],
                ['q' => 'Apakah bisa request vendor tertentu?', 'a' => 'Anda diperbolehkan mengajukan request vendor pilihan sendiri. Tim kami akan berkoordinasi dengan vendor tersebut untuk memastikan semua berjalan lancar.'],
                ['q' => 'Bagaimana cara menghubungi admin selama proses persiapan?', 'a' => 'Anda dapat menghubungi tim admin melalui fitur Chat langsung di dashboard klien, WhatsApp, atau email. Tim kami responsif dan siap membantu 7 hari seminggu.'],
            ];
            @endphp

            @foreach($faqs as $i => $faq)
            <div class="bg-white dark:bg-gray-800/50 dark:border dark:border-white/5 rounded-2xl shadow-sm overflow-hidden">
                <button @click="open = open === {{ $i }} ? null : {{ $i }}"
                        class="w-full text-left p-6 flex items-center justify-between gap-4 hover:bg-gray-50 dark:hover:bg-white/5 transition-colors focus:outline-none">
                    <span class="font-semibold text-gray-800 dark:text-gray-200 text-sm leading-relaxed">{{ $faq['q'] }}</span>
                    <div class="w-8 h-8 rounded-full gold-gradient flex-shrink-0 flex items-center justify-center transition-transform"
                         :class="open === {{ $i }} ? 'rotate-45' : ''">
                        <i class="fas fa-plus text-white text-xs"></i>
                    </div>
                </button>
                <div x-show="open === {{ $i }}" x-collapse class="px-6 pb-6">
                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed border-t border-gray-100 dark:border-white/10 pt-4">{{ $faq['a'] }}</p>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Still have questions --}}
        <div class="bg-white dark:bg-gray-800/30 dark:border dark:border-white/5 rounded-3xl shadow-sm p-10 text-center">
            <div class="w-16 h-16 gold-gradient rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-headset text-white text-2xl"></i>
            </div>
            <h2 class="font-playfair text-2xl font-bold text-gray-800 dark:text-white mb-2">Masih Ada Pertanyaan?</h2>
            <p class="text-gray-500 dark:text-gray-400 mb-6 max-w-md mx-auto">Tim kami siap membantu Anda 7 hari seminggu. Hubungi kami melalui berbagai cara di bawah ini.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('consultation.form') }}" class="gold-gradient text-white font-semibold px-6 py-3.5 rounded-xl text-sm hover:shadow-lg transition-all">
                    <i class="fas fa-calendar-check mr-2"></i> Konsultasi Gratis
                </a>
                <a href="https://wa.me/6281231122057" target="_blank" class="bg-green-500 text-white font-semibold px-6 py-3.5 rounded-xl text-sm hover:bg-green-600 transition-all">
                    <i class="fab fa-whatsapp mr-2"></i> WhatsApp Kami
                </a>
                <a href="mailto:anggitaweddingsurabaya@gmail.com" class="border-2 border-gray-300 dark:border-white/10 text-gray-700 dark:text-gray-300 font-semibold px-6 py-3.5 rounded-xl text-sm hover:border-yellow-400 dark:hover:border-yellow-500 hover:text-yellow-600 dark:hover:text-yellow-400 transition-all">
                    <i class="fas fa-envelope mr-2"></i> Email Kami
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
