@extends('layouts.guest')
@section('title', 'Kebijakan Cookie – Anggita Wedding Organizer')
@section('meta_description', 'Kebijakan Cookie Anggita Wedding Organizer. Kami menggunakan cookie untuk memberikan pengalaman terbaik, menganalisis lalu lintas situs, dan mempersonalisasi konten.')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-[#0A0A0A] pt-24 pb-16 transition-colors duration-500">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <span class="text-yellow-600 dark:text-yellow-500 text-sm font-semibold uppercase tracking-widest">Privacy & Technology</span>
            <h1 class="font-playfair text-5xl font-bold text-gray-800 dark:text-white mt-2">Kebijakan Cookie</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-4 max-w-xl mx-auto">Terakhir diperbarui: {{ date('d F Y') }}</p>
        </div>

        <div class="bg-white dark:bg-gray-800/50 dark:border dark:border-white/5 rounded-3xl shadow-sm p-8 md:p-12 prose dark:prose-invert max-w-none">
            <p class="text-gray-600 dark:text-gray-300 leading-relaxed mb-8">
                Situs web <strong>Anggita Wedding Organizer</strong> menggunakan cookie dan teknologi pelacakan serupa untuk meningkatkan pengalaman Anda, menganalisis penggunaan situs, dan membantu upaya pemasaran kami. Halaman ini menjelaskan apa itu cookie dan bagaimana kami menggunakannya.
            </p>

            <h2 class="text-2xl font-playfair font-bold text-gray-800 dark:text-white mt-10 mb-4">1. Apa itu Cookie?</h2>
            <p class="text-gray-600 dark:text-gray-400 leading-relaxed mb-6">
                Cookie adalah file teks kecil yang disimpan di perangkat Anda (komputer, tablet, atau smartphone) oleh situs web yang Anda kunjungi. Cookie memungkinkan situs web untuk mengenali perangkat Anda dan mengingat preferensi atau tindakan tertentu dari waktu ke waktu.
            </p>

            <h2 class="text-2xl font-playfair font-bold text-gray-800 dark:text-white mt-10 mb-4">2. Jenis Cookie yang Kami Gunakan</h2>
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-2">Cookie Penting (Essential)</h3>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                        Cookie ini diperlukan agar situs web berfungsi dengan benar. Mereka mencakup, misalnya, cookie yang memungkinkan Anda untuk masuk ke area aman di situs kami (dashboard klien) dan mengingat status autentikasi Anda.
                    </p>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-2">Cookie Fungsional</h3>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                        Digunakan untuk mengenali Anda saat Anda kembali ke situs web kami. Ini memungkinkan kami untuk mempersonalisasi konten kami untuk Anda dan mengingat preferensi Anda (misalnya, pilihan mode gelap atau bahasa).
                    </p>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-2">Cookie Analitik</h3>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                        Kami menggunakan cookie analitik (seperti Google Analytics) untuk memahami bagaimana pengunjung berinteraksi dengan situs kami. Ini membantu kami meningkatkan fungsionalitas situs dengan memastikan pengguna menemukan apa yang mereka cari dengan mudah.
                    </p>
                </div>
            </div>

            <h2 class="text-2xl font-playfair font-bold text-gray-800 dark:text-white mt-10 mb-4">3. Cookie Pihak Ketiga</h2>
            <p class="text-gray-600 dark:text-gray-400 leading-relaxed mb-6">
                Selain cookie kami sendiri, kami juga dapat menggunakan berbagai cookie pihak ketiga untuk melaporkan statistik penggunaan situs web, menyampaikan iklan di dan melalui situs web, dan sebagainya. Contohnya termasuk Facebook Pixel untuk melacak efektivitas iklan media sosial kami.
            </p>

            <h2 class="text-2xl font-playfair font-bold text-gray-800 dark:text-white mt-10 mb-4">4. Mengelola Pilihan Cookie Anda</h2>
            <p class="text-gray-600 dark:text-gray-400 leading-relaxed mb-6">
                Sebagian besar browser web memungkinkan Anda untuk mengontrol cookie melalui pengaturan mereka. Anda dapat mengatur browser Anda untuk menolak cookie, atau untuk memperingatkan Anda ketika cookie sedang dikirim. Namun, perlu dicatat bahwa beberapa bagian dari situs kami mungkin tidak berfungsi dengan benar jika Anda menonaktifkan cookie.
            </p>

            <h2 class="text-2xl font-playfair font-bold text-gray-800 dark:text-white mt-10 mb-4">5. Perubahan pada Kebijakan Cookie</h2>
            <p class="text-gray-600 dark:text-gray-400 leading-relaxed mb-6">
                Kami dapat memperbarui Kebijakan Cookie ini sewaktu-waktu. Setiap perubahan akan dipublikasikan di halaman ini dengan tanggal pembaruan terbaru.
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
