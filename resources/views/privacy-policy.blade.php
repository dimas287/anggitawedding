@extends('layouts.guest')
@section('title', 'Kebijakan Privasi – Anggita Wedding Organizer')
@section('meta_description', 'Kebijakan Privasi Anggita Wedding Organizer. Kami berkomitmen untuk melindungi data pribadi Anda dan memastikan keamanan informasi selama proses perencanaan pernikahan.')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-[#0A0A0A] pt-24 pb-16 transition-colors duration-500">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <span class="text-yellow-600 dark:text-yellow-500 text-sm font-semibold uppercase tracking-widest">Legal & Privacy</span>
            <h1 class="font-playfair text-5xl font-bold text-gray-800 dark:text-white mt-2">Kebijakan Privasi</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-4 max-w-xl mx-auto">Terakhir diperbarui: {{ date('d F Y') }}</p>
        </div>

        <div class="bg-white dark:bg-gray-800/50 dark:border dark:border-white/5 rounded-3xl shadow-sm p-8 md:p-12 prose dark:prose-invert max-w-none">
            <p class="text-gray-600 dark:text-gray-300 leading-relaxed mb-8">
                Di <strong>Anggita Wedding Organizer</strong>, kami sangat menghargai privasi Anda. Kebijakan Privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, melindungi, dan dalam keadaan tertentu, membagikan informasi pribadi Anda saat Anda menggunakan situs web dan layanan kami.
            </p>

            <h2 class="text-2xl font-playfair font-bold text-gray-800 dark:text-white mt-10 mb-4">1. Informasi yang Kami Kumpulkan</h2>
            <p class="text-gray-600 dark:text-gray-400 leading-relaxed mb-4">
                Kami mengumpulkan informasi yang Anda berikan langsung kepada kami saat:
            </p>
            <ul class="list-disc pl-6 space-y-2 text-gray-600 dark:text-gray-400 mb-6">
                <li>Melakukan pendaftaran akun di dashboard klien kami.</li>
                <li>Melakukan reservasi paket pernikahan atau layanan undangan digital.</li>
                <li>Mengisi formulir konsultasi atau menghubungi kami melalui fitur chat.</li>
                <li>Melakukan pembayaran melalui sistem pembayaran online kami.</li>
            </ul>

            <h2 class="text-2xl font-playfair font-bold text-gray-800 dark:text-white mt-10 mb-4">2. Penggunaan Informasi Anda</h2>
            <p class="text-gray-600 dark:text-gray-400 leading-relaxed mb-4">
                Informasi yang kami kumpulkan digunakan untuk:
            </p>
            <ul class="list-disc pl-6 space-y-2 text-gray-600 dark:text-gray-400 mb-6">
                <li>Memproses reservasi dan mengelola perencanaan pernikahan Anda.</li>
                <li>Berkomunikasi dengan Anda mengenai detail acara, update, dan penagihan.</li>
                <li>Menyediakan layanan undangan digital yang disesuaikan dengan data Anda.</li>
                <li>Meningkatkan kualitas layanan dan pengalaman pengguna di situs web kami.</li>
                <li>Kepatuhan terhadap kewajiban hukum dan pencegahan aktivitas penipuan.</li>
            </ul>

            <h2 class="text-2xl font-playfair font-bold text-gray-800 dark:text-white mt-10 mb-4">3. Keamanan Data</h2>
            <p class="text-gray-600 dark:text-gray-400 leading-relaxed mb-6">
                Kami menerapkan standar keamanan teknis dan organisasional yang ketat untuk melindungi data pribadi Anda dari akses yang tidak sah, kehilangan, atau penyalahgunaan. Pembayaran online diproses melalui gateway pembayaran yang aman (Midtrans) yang telah tersertifikasi standar keamanan internasional.
            </p>

            <h2 class="text-2xl font-playfair font-bold text-gray-800 dark:text-white mt-10 mb-4">4. Berbagi Informasi dengan Pihak Ketiga</h2>
            <p class="text-gray-600 dark:text-gray-400 leading-relaxed mb-6">
                Kami tidak menjual atau menyewakan informasi pribadi Anda kepada pihak ketiga. Kami hanya membagikan informasi Anda kepada vendor mitra (seperti fotografer, katering, atau dekorator) sejauh yang diperlukan untuk pelaksanaan acara pernikahan Anda, dan kepada penyedia layanan pembayaran untuk memproses transaksi.
            </p>

            <h2 class="text-2xl font-playfair font-bold text-gray-800 dark:text-white mt-10 mb-4">5. Hak Anda</h2>
            <p class="text-gray-600 dark:text-gray-400 leading-relaxed mb-6">
                Anda memiliki hak untuk mengakses, memperbarui, atau menghapus informasi pribadi Anda yang tersimpan di sistem kami. Anda dapat mengelola sebagian besar informasi ini melalui dashboard profil klien atau dengan menghubungi tim dukungan kami.
            </p>

            <h2 class="text-2xl font-playfair font-bold text-gray-800 dark:text-white mt-10 mb-4">6. Perubahan pada Kebijakan Ini</h2>
            <p class="text-gray-600 dark:text-gray-400 leading-relaxed mb-6">
                Kami dapat memperbarui Kebijakan Privasi ini dari waktu ke waktu untuk mencerminkan perubahan dalam praktik kami atau hukum yang berlaku. Kami menyarankan Anda untuk meninjau halaman ini secara berkala.
            </p>

            <h2 class="text-2xl font-playfair font-bold text-gray-800 dark:text-white mt-10 mb-4">7. Hubungi Kami</h2>
            <p class="text-gray-600 dark:text-gray-400 leading-relaxed mb-6">
                Jika Anda memiliki pertanyaan mengenai Kebijakan Privasi ini, silakan hubungi kami melalui:
            </p>
            <ul class="list-none space-y-3 text-gray-600 dark:text-gray-400 mb-6">
                <li class="flex items-center gap-3">
                    <i class="fas fa-envelope text-yellow-600"></i>
                    <span>anggitaweddingsurabaya@gmail.com</span>
                </li>
                <li class="flex items-center gap-3">
                    <i class="fab fa-whatsapp text-yellow-600"></i>
                    <span>+62 812-3112-2057</span>
                </li>
                <li class="flex items-center gap-3">
                    <i class="fas fa-map-marker-alt text-yellow-600"></i>
                    <span>Surabaya, Jawa Timur, Indonesia</span>
                </li>
            </ul>
        </div>

        {{-- Back home link --}}
        <div class="mt-12 text-center">
            <a href="{{ route('landing') }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-yellow-600 dark:text-gray-400 dark:hover:text-yellow-500 transition-colors">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali ke Beranda</span>
            </a>
        </div>
    </div>
</div>
@endsection
