<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Package;
use App\Models\InvitationTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::firstOrCreate(['email' => 'admin@anggitawo.com'], [
            'name' => 'Admin Anggita WO',
            'email' => 'admin@anggitawo.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Demo client user
        User::firstOrCreate(['email' => 'demo@client.com'], [
            'name' => 'Demo Client',
            'email' => 'demo@client.com',
            'password' => Hash::make('demo123'),
            'role' => 'client',
            'is_active' => true,
            'phone' => '081234567890',
            'email_verified_at' => now(),
        ]);

        // Packages
        $packages = [
            [
                'name' => 'Rumahan Intimate',
                'slug' => 'rumahan-intimate',
                'tier' => 'silver',
                'category' => 'rumahan',
                'price' => 15000000,
                'description' => 'Paket rumahan untuk 150-200 tamu dengan dekor cantik dan tim inti Anggita WO.',
                'features' => ['Dekorasi Pelaminan Minimalis', 'Makeup Pengantin', 'Dokumentasi Foto (4 jam)', 'MC Profesional', 'Koordinator Hari-H', 'Undangan Digital', 'Sound system basic'],
                'max_guests' => 200,
                'duration' => '6 jam',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Rumahan Premium',
                'slug' => 'rumahan-premium',
                'tier' => 'gold',
                'category' => 'rumahan',
                'price' => 26000000,
                'description' => 'Lengkap untuk resepsi rumahan mewah dengan tenda, lighting, dan hiburan.',
                'features' => ['Dekorasi Full Area', 'Makeup + Hairdo 2x', 'Foto & Video (8 jam)', 'Live Acoustic', 'MC Profesional', 'Tim Koordinator 3 orang', 'Undangan Digital Premium', 'Souvenir 150 pcs'],
                'max_guests' => 300,
                'duration' => '10 jam',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Gedung Elegant',
                'slug' => 'gedung-elegant',
                'tier' => 'gold',
                'category' => 'gedung',
                'price' => 38000000,
                'description' => 'Paket lengkap untuk ballroom dengan dekor elegan dan entertainment full.',
                'features' => ['Dekorasi Ballroom', 'Lighting Profesional', 'Foto & Video Sinematik', 'MC + Host', 'Band Live', 'Tim Koordinator 4 orang', 'Undangan Digital & Cetak', 'Souvenir 300 pcs'],
                'max_guests' => 600,
                'duration' => 'Full Day',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Gedung Signature',
                'slug' => 'gedung-signature',
                'tier' => 'premium',
                'category' => 'gedung',
                'price' => 62000000,
                'description' => 'Signature package untuk pernikahan megah di gedung atau hotel bintang lima.',
                'features' => ['Dekorasi Premium Full Venue', 'Makeup Artis', 'Foto & Video Full Day', 'Prewedding Indoor & Outdoor', 'MC + Entertainer', 'Live Band + DJ', 'Tim Koordinator 6 orang', 'Catering 600 pax', 'Souvenir 600 pcs', 'Wedding Car'],
                'max_guests' => 1000,
                'duration' => 'Full Day',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Rias Wisuda & Pagar Ayu',
                'slug' => 'rias-wisuda',
                'tier' => 'silver',
                'category' => 'rias',
                'price' => 3500000,
                'description' => 'Layanan makeup wisuda, bridesmaid, atau pagar ayu dengan look natural glowing.',
                'features' => ['Makeup + Hairdo 1 orang', 'Retouch ringan', 'Aksesori basic', 'Konsultasi gaya'],
                'max_guests' => null,
                'duration' => '3 jam',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Rias Akad & Resepsi',
                'slug' => 'rias-akad',
                'tier' => 'gold',
                'category' => 'rias',
                'price' => 9500000,
                'description' => 'Paket rias lengkap untuk akad dan resepsi termasuk keluarga inti.',
                'features' => ['Makeup pengantin akad + resepsi', 'Hairdo & Aksesori Lengkap', 'Makeup orang tua (2 orang)', 'Touch up selama acara', 'Tes makeup sebelum hari-H'],
                'max_guests' => null,
                'duration' => 'Full Day',
                'sort_order' => 6,
                'is_active' => true,
            ],
        ];

        foreach ($packages as $pkg) {
            Package::firstOrCreate(['slug' => $pkg['slug']], $pkg);
        }

        // Invitation Templates
        $templates = [
            ['name' => 'Klasik Elegan', 'slug' => 'klasik-elegan', 'theme' => 'classic', 'primary_color' => '#D4AF37', 'secondary_color' => '#FFFFFF', 'font_family' => 'Playfair Display', 'sort_order' => 1, 'is_active' => true],
            ['name' => 'Modern Minimalis', 'slug' => 'modern-minimalis', 'theme' => 'minimalist', 'primary_color' => '#1A1A2E', 'secondary_color' => '#F5F5F5', 'font_family' => 'Poppins', 'sort_order' => 2, 'is_active' => true],
            ['name' => 'Bunga Romantis', 'slug' => 'bunga-romantis', 'theme' => 'floral', 'primary_color' => '#E91E8C', 'secondary_color' => '#FFF0F6', 'font_family' => 'Great Vibes', 'sort_order' => 3, 'is_active' => true],
            ['name' => 'Royal Gold', 'slug' => 'royal-gold', 'theme' => 'royal', 'primary_color' => '#8B0000', 'secondary_color' => '#FFF8DC', 'font_family' => 'Cormorant Garamond', 'sort_order' => 4, 'is_active' => true],
            ['name' => 'Garden Party', 'slug' => 'garden-party', 'theme' => 'garden', 'primary_color' => '#2D5A27', 'secondary_color' => '#F0FFF0', 'font_family' => 'Josefin Sans', 'sort_order' => 5, 'is_active' => true],
            ['name' => 'Bohemian Rustic', 'slug' => 'bohemian-rustic', 'theme' => 'bohemian', 'primary_color' => '#8B4513', 'secondary_color' => '#FFF8EE', 'font_family' => 'Dancing Script', 'sort_order' => 6, 'is_active' => true],
        ];

        foreach ($templates as $tpl) {
            InvitationTemplate::firstOrCreate(['slug' => $tpl['slug']], $tpl);
        }
    }
}
