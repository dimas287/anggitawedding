<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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

        foreach ($faqs as $i => $faq) {
            \App\Models\Faq::create([
                'question' => $faq['q'],
                'answer' => $faq['a'],
                'sort_order' => $i,
                'is_active' => true,
            ]);
        }
    }
}
