@extends('layouts.guest')
@section('title', 'Syarat & Ketentuan – Anggita Wedding Organizer')
@section('meta_description', 'Syarat dan Ketentuan layanan Anggita Wedding Organizer. Pelajari aturan main, kebijakan pembatalan, refund, dan tanggung jawab layanan kami.')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-[#0A0A0A] pt-24 pb-16 transition-colors duration-500">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <span class="text-yellow-600 dark:text-yellow-500 text-sm font-semibold uppercase tracking-widest">Legal & Agreement</span>
            <h1 class="font-playfair text-5xl font-bold text-gray-800 dark:text-white mt-2">Syarat & Ketentuan</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-4 max-w-xl mx-auto">Terakhir diperbarui: {{ date('d F Y') }}</p>
        </div>

        <div class="bg-white dark:bg-gray-800/50 dark:border dark:border-white/5 rounded-3xl shadow-sm p-8 md:p-12 prose dark:prose-invert max-w-none">
            <p class="text-gray-600 dark:text-gray-300 leading-relaxed mb-8">
                Selamat datang di <strong>Anggita Wedding Organizer</strong>. Dengan menggunakan layanan kami, Anda dianggap telah membaca, memahami, dan menyetujui seluruh Syarat & Ketentuan yang berlaku di bawah ini. Dokumen ini merupakan perjanjian sah antara Anda (Klien) dan Anggita Wedding Organizer.
            </p>

            <h2 class="text-2xl font-playfair font-bold text-gray-800 dark:text-white mt-10 mb-4">1. Ruang Lingkup Layanan</h2>
            <p class="text-gray-600 dark:text-gray-400 leading-relaxed mb-4">
                Anggita Wedding Organizer menyediakan jasa perencanaan, koordinasi, dan pelaksanaan acara pernikahan sesuai dengan paket yang dipilih oleh Klien. Layanan kami mencakup:
            </p>
            <ul class="list-disc pl-6 space-y-2 text-gray-600 dark:text-gray-400 mb-6">
                <li>Konsultasi konsep dan perencanaan anggaran.</li>
                <li>Koordinasi dengan vendor pihak ketiga (katering, dekorasi, dokumentasi, dll).</li>
                <li>Manajemen operasional pada hari pelaksanaan (Day of Coordinator).</li>
                <li>Penyediaan platform undangan digital (jika termasuk dalam paket).</li>
            </ul>

            <h2 class="text-2xl font-playfair font-bold text-gray-800 dark:text-white mt-10 mb-4">2. Pemesanan dan Pembayaran</h2>
            <p class="text-gray-600 dark:text-gray-400 leading-relaxed mb-4">
                Proses pemesanan dianggap sah apabila:
            </p>
            <ul class="list-disc pl-6 space-y-2 text-gray-600 dark:text-gray-400 mb-6">
                <li>Klien telah melakukan reservasi melalui situs web atau secara langsung.</li>
                <li>Pembayaran Down Payment (DP) sebesar persentase yang ditentukan telah diterima oleh kami.</li>
                <li>Pelunasan wajib dilakukan paling lambat 14 hari sebelum hari pelaksanaan acara.</li>
                <li>Keterlambatan pelunasan dapat menyebabkan penangguhan layanan tanpa pengembalian dana DP.</li>
            </ul>

            <h2 class="text-2xl font-playfair font-bold text-gray-800 dark:text-white mt-10 mb-4">3. Kebijakan Pembatalan dan Perubahan Jadwal</h2>
            <p class="text-gray-600 dark:text-gray-400 leading-relaxed mb-4">
                Kami memahami bahwa rencana dapat berubah, namun kami memiliki kebijakan sebagai berikut:
            </p>
            <ul class="list-disc pl-6 space-y-2 text-gray-600 dark:text-gray-400 mb-6">
                <li><strong>Pembatalan oleh Klien:</strong> DP yang telah dibayarkan bersifat non-refundable (tidak dapat dikembalikan) karena alasan apapun, mengingat kami telah melakukan pemblokiran tanggal dan koordinasi awal.</li>
                <li><strong>Perubahan Jadwal (Reschedule):</strong> Diperbolehkan maksimal 1 kali dengan pemberitahuan minimal 3 bulan sebelum tanggal awal, bergantung pada ketersediaan tim kami di tanggal baru.</li>
                <li><strong>Biaya Tambahan:</strong> Perubahan jadwal mungkin akan dikenakan biaya administrasi atau penyesuaian harga jika ada kenaikan harga vendor di periode baru.</li>
            </ul>

            <h2 class="text-2xl font-playfair font-bold text-gray-800 dark:text-white mt-10 mb-4">4. Keadaan Darurat (Force Majeure)</h2>
            <p class="text-gray-600 dark:text-gray-400 leading-relaxed mb-6">
                Dalam hal terjadi peristiwa di luar kendali kedua belah pihak (seperti bencana alam, wabah penyakit, kebijakan pemerintah/PPKM, atau kerusuhan) yang menyebabkan acara tidak dapat dilaksanakan, Anggita Wedding Organizer akan bekerja sama dengan Klien untuk penjadwalan ulang tanpa penalti tambahan. Namun, biaya yang sudah dibayarkan ke vendor pihak ketiga akan mengikuti kebijakan masing-masing vendor tersebut.
            </p>

            <h2 class="text-2xl font-playfair font-bold text-gray-800 dark:text-white mt-10 mb-4">5. Tanggung Jawab dan Batasan</h2>
            <p class="text-gray-600 dark:text-gray-400 leading-relaxed mb-6">
                Anggita WO bertanggung jawab atas koordinasi layanan sesuai kontrak. Kami tidak bertanggung jawab atas kegagalan teknis dari vendor pihak ketiga (misal: rasa katering, keterlambatan vendor dekorasi di luar kendali koordinasi), namun kami berkomitmen untuk melakukan mediasi dan solusi terbaik bagi Klien.
            </p>

            <h2 class="text-2xl font-playfair font-bold text-gray-800 dark:text-white mt-10 mb-4">6. Hak Cipta dan Dokumentasi</h2>
            <p class="text-gray-600 dark:text-gray-400 leading-relaxed mb-6">
                Anggita Wedding Organizer berhak menggunakan dokumentasi (foto/video) hasil acara untuk kepentingan promosi di media sosial atau portofolio situs web kami, kecuali jika ada permintaan tertulis dari Klien untuk tidak mempublikasikannya.
            </p>

            <h2 class="text-2xl font-playfair font-bold text-gray-800 dark:text-white mt-10 mb-4">7. Penyelesaian Perselisihan</h2>
            <p class="text-gray-600 dark:text-gray-400 leading-relaxed mb-6">
                Segala perselisihan yang timbul dari perjanjian ini akan diselesaikan secara musyawarah untuk mufakat terlebih dahulu. Jika tidak tercapai kesepakatan, maka akan diselesaikan melalui jalur hukum yang berlaku di wilayah hukum Kota Surabaya.
            </p>
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
