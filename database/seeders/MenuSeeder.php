<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // Main Navigation
        Menu::updateOrCreate(
            ['slug' => 'main-navigation'],
            [
                'name' => 'Main Navigation',
                'location' => 'main',
                'items' => [
                    [
                        'label' => 'Profil',
                        'url' => '#profil',
                        'icon' => null,
                        'target' => '_self',
                        'sort_order' => 0,
                        'children' => [
                            ['label' => 'Sejarah', 'url' => '/sejarah', 'icon' => 'history_edu', 'target' => '_self'],
                            ['label' => 'Visi & Misi', 'url' => '/visi-misi', 'icon' => 'visibility', 'target' => '_self'],
                            ['label' => 'Struktur Organisasi', 'url' => '/struktur-organisasi', 'icon' => 'account_tree', 'target' => '_self'],
                            ['label' => 'Wilayah Kerja', 'url' => '/wilayah-kerja', 'icon' => 'location_on', 'target' => '_self'],
                        ],
                    ],
                    ['label' => 'Layanan', 'url' => '/services', 'icon' => null, 'target' => '_self', 'sort_order' => 1],
                    ['label' => 'Berita', 'url' => '/blog', 'icon' => null, 'target' => '_self', 'sort_order' => 2],
                    ['label' => 'Dokumentasi', 'url' => '/documentation', 'icon' => null, 'target' => '_self', 'sort_order' => 3],
                    ['label' => 'Kontak', 'url' => '/contact', 'icon' => null, 'target' => '_self', 'sort_order' => 4],
                ],
                'is_active' => true,
                'sort_order' => 0,
            ]
        );

        // Footer Navigation
        Menu::updateOrCreate(
            ['slug' => 'footer-nav'],
            [
                'name' => 'Footer Navigation',
                'location' => 'footer',
                'items' => [
                    [
                        'label' => 'Layanan',
                        'url' => '#',
                        'icon' => null,
                        'target' => '_self',
                        'sort_order' => 0,
                        'children' => [
                            ['label' => 'Kalibrasi Alat Kesehatan', 'url' => '/services', 'icon' => null, 'target' => '_self'],
                            ['label' => 'Inspeksi Preventive', 'url' => '/services', 'icon' => null, 'target' => '_self'],
                            ['label' => 'Konsultasi Teknis', 'url' => '/services', 'icon' => null, 'target' => '_self'],
                            ['label' => 'Verifikasi Sertifikat', 'url' => '/services', 'icon' => null, 'target' => '_self'],
                        ],
                    ],
                    [
                        'label' => 'Perusahaan',
                        'url' => '#',
                        'icon' => null,
                        'target' => '_self',
                        'sort_order' => 1,
                        'children' => [
                            ['label' => 'Tentang Kami', 'url' => '/sejarah', 'icon' => null, 'target' => '_self'],
                            ['label' => 'Berita', 'url' => '/blog', 'icon' => null, 'target' => '_self'],
                            ['label' => 'Sertifikat Akreditasi', 'url' => '#', 'icon' => null, 'target' => '_self'],
                            ['label' => 'Hubungi Kami', 'url' => '/contact', 'icon' => null, 'target' => '_self'],
                        ],
                    ],
                ],
                'is_active' => true,
                'sort_order' => 0,
            ]
        );
    }
}
