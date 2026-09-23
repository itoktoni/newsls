<?php

namespace Modules\Cms\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Cms\Models\Content;
use Modules\Cms\Models\Field;
use Modules\Cms\Models\Menu;
use Modules\Cms\Models\Section;
use Modules\Cms\Models\Type;

class CompanyProfileSeeder extends Seeder
{
    public function run(): void
    {
        $type = Type::updateOrCreate(
            ['slug' => 'homepage'],
            [
                'name' => 'Homepage',
                'type' => 'custom',
                'description' => 'Website sayur — hero, tentang, produk (sayur/telur/ikan/ayam/daging/bahan dapur), mitra, CTA.',
                'supports' => ['title', 'slug'],
                'is_active' => true,
            ]
        );

        $fields = $this->seedFields($type);
        $sections = $this->seedSections($type, $fields);
        $this->seedContent($type, $sections);
        $this->seedMenus();
    }

    private function seedFields(Type $type): array
    {
        $defs = [
            ['name' => 'hero', 'label' => 'Hero', 'type' => 'container', 'mode' => 'multiple', 'sort_order' => 10, 'children' => [
                ['name' => 'eyebrow', 'label' => 'Eyebrow', 'type' => 'text', 'sort_order' => 1],
                ['name' => 'title', 'label' => 'Judul', 'type' => 'text', 'sort_order' => 2],
                ['name' => 'description', 'label' => 'Deskripsi', 'type' => 'textarea', 'sort_order' => 3],
                ['name' => 'image', 'label' => 'Gambar', 'type' => 'image', 'sort_order' => 4],
                ['name' => 'cta_text', 'label' => 'Tombol 1 Teks', 'type' => 'text', 'sort_order' => 5],
                ['name' => 'cta_link', 'label' => 'Tombol 1 Link', 'type' => 'url', 'sort_order' => 6],
                ['name' => 'cta2_text', 'label' => 'Tombol 2 Teks', 'type' => 'text', 'sort_order' => 7],
                ['name' => 'cta2_link', 'label' => 'Tombol 2 Link', 'type' => 'url', 'sort_order' => 8],
            ]],
            ['name' => 'about', 'label' => 'Tentang Kami', 'type' => 'container', 'mode' => 'single', 'sort_order' => 20, 'children' => [
                ['name' => 'subtitle', 'label' => 'Subjudul', 'type' => 'text', 'sort_order' => 1],
                ['name' => 'title', 'label' => 'Judul', 'type' => 'text', 'sort_order' => 2],
                ['name' => 'description', 'label' => 'Deskripsi', 'type' => 'textarea', 'sort_order' => 3],
                ['name' => 'image', 'label' => 'Gambar', 'type' => 'image', 'sort_order' => 4],
                ['name' => 'cta_text', 'label' => 'Tombol Teks', 'type' => 'text', 'sort_order' => 5],
                ['name' => 'cta_link', 'label' => 'Tombol Link', 'type' => 'url', 'sort_order' => 6],
            ]],
            ['name' => 'services', 'label' => 'Produk', 'type' => 'container', 'mode' => 'multiple', 'sort_order' => 30, 'children' => [
                ['name' => 'icon', 'label' => 'Icon (Material Symbols)', 'type' => 'text', 'sort_order' => 1],
                ['name' => 'title', 'label' => 'Judul', 'type' => 'text', 'sort_order' => 2],
                ['name' => 'description', 'label' => 'Deskripsi', 'type' => 'textarea', 'sort_order' => 3],
            ]],
            ['name' => 'clients', 'label' => 'Mitra & Pelanggan', 'type' => 'container', 'mode' => 'multiple', 'sort_order' => 40, 'children' => [
                ['name' => 'name', 'label' => 'Nama', 'type' => 'text', 'sort_order' => 1],
                ['name' => 'logo', 'label' => 'Logo', 'type' => 'image', 'sort_order' => 2],
            ]],
            ['name' => 'cta', 'label' => 'CTA Penutup', 'type' => 'container', 'mode' => 'single', 'sort_order' => 50, 'children' => [
                ['name' => 'title', 'label' => 'Judul', 'type' => 'text', 'sort_order' => 1],
                ['name' => 'description', 'label' => 'Deskripsi', 'type' => 'textarea', 'sort_order' => 2],
                ['name' => 'button1_text', 'label' => 'Tombol 1', 'type' => 'text', 'sort_order' => 3],
                ['name' => 'button1_link', 'label' => 'Link 1', 'type' => 'url', 'sort_order' => 4],
                ['name' => 'button2_text', 'label' => 'Tombol 2', 'type' => 'text', 'sort_order' => 5],
                ['name' => 'button2_link', 'label' => 'Link 2', 'type' => 'url', 'sort_order' => 6],
            ]],
        ];

        $byName = [];
        foreach ($defs as $def) {
            $children = $def['children'] ?? [];
            unset($def['children']);
            $def['type_id'] = $type->id;

            $parent = Field::updateOrCreate(['name' => $def['name']], $def);
            $byName[$def['name']] = $parent;

            foreach ($children as $child) {
                Field::updateOrCreate(
                    ['name' => $child['name'], 'parent_id' => $parent->id],
                    array_merge($child, ['parent_id' => $parent->id, 'type_id' => $type->id])
                );
            }
        }

        return $byName;
    }

    private function seedSections(Type $type, array $fields): array
    {
        $order = [
            'hero' => 10,
            'about' => 20,
            'services' => 30,
            'clients' => 40,
            'cta' => 50,
        ];

        $sections = [];
        foreach ($order as $name => $sort) {
            $field = $fields[$name] ?? null;
            if (! $field) continue;

            $sections[$name] = Section::updateOrCreate(
                ['name' => $name],
                [
                    'description' => $name === 'services' ? 'Produk — sayur, telur, ikan, ayam, daging, bahan dapur' : ucfirst($name) . ' — website sayur',
                    'icon' => match ($name) { 'hero' => 'home', 'about' => 'info', 'services' => 'grocery', 'clients' => 'handshake', 'cta' => 'shopping_cart', default => 'layers' },
                    'content_type_id' => $type->id,
                    'field_ids' => [$field->id],
                    'sort_order' => $sort,
                    'is_active' => true,
                ]
            );
        }

        return $sections;
    }

    private function seedContent(Type $type, array $sections): void
    {
        $meta = [
            'hero' => [
                [
                    'eyebrow' => 'Sayur & Bahan Dapur Segar',
                    'title' => 'Dari Pasar ke Dapur Anda — Segar Setiap Hari',
                    'description' => 'Sayur-mayur, telur, ikan, ayam, daging & bahan dapur pilihan. Panen pagi, sortir higienis, antar harian ke rumah, warung, dan dapur usaha Anda.',
                    'image' => '',
                    'cta_text' => 'Pesan Sekarang',
                    'cta_link' => '/contact',
                    'cta2_text' => 'Lihat Produk',
                    'cta2_link' => '/#produk',
                ],
            ],
            'about' => [
                'subtitle' => 'Tentang Kami',
                'title' => 'Mitra petani, peternak & nelayan — untuk dapur Anda',
                'description' => "Kami menghubungkan kebun, kandang, dan laut langsung ke meja Anda. Kurasi harian, harga pasar yang jujur, dan pengiriman tepat waktu.\n\nCocok untuk rumah tangga, warung, katering, dan restoran — pesan eceran atau langganan harian/mingguan. Kelola semua konten homepage ini dari CMS tanpa ubah kode.",
                'image' => '',
                'cta_text' => 'Hubungi Kami',
                'cta_link' => '/contact',
            ],
            'services' => [
                ['icon' => 'psychiatry', 'title' => 'Sayur Mayur', 'description' => 'Kangkung, bayam, sawi, wortel, kentang, tomat, cabai, bawang — petik pagi, segar sampai sore.'],
                ['icon' => 'egg', 'title' => 'Telur', 'description' => 'Telur ayam ras & kampung, bebek — sortir bersih, butir utuh, siap masak.'],
                ['icon' => 'set_meal', 'title' => 'Ikan Segar', 'description' => 'Ikan laut & tawar harian — kembung, nila, lele, bandeng. Dingin rantai terjaga.'],
                ['icon' => 'cruelty_free', 'title' => 'Ayam', 'description' => 'Ayam potong segar & ayam kampung — potong higienis, bisa fillet/utuh.'],
                ['icon' => 'kebab_dining', 'title' => 'Daging', 'description' => 'Daging sapi & kambing pilihan — potong sesuai kebutuhan, kemas vakum tersedia.'],
                ['icon' => 'grocery', 'title' => 'Bahan Dapur', 'description' => 'Bumbu, minyak, tepung, santan, tahu-tempe & kebutuhan dapur harian lainnya.'],
            ],
            'clients' => [
                ['name' => 'Warung & Rumah Makan', 'logo' => ''],
                ['name' => 'Katering & Dapur Usaha', 'logo' => ''],
                ['name' => 'Pasar & Toko Mitra', 'logo' => ''],
                ['name' => 'Langganan Rumah Tangga', 'logo' => ''],
            ],
            'cta' => [
                'title' => 'Butuh pasokan harian?',
                'description' => 'Pesan sebelum jam 17.00 — kami antar besok pagi. Eceran & langganan tersedia.',
                'button1_text' => 'Pesan via WhatsApp',
                'button1_link' => '/contact',
                'button2_text' => 'Lihat Produk',
                'button2_link' => '/#produk',
            ],
        ];

        $content = Content::updateOrCreate(
            ['slug' => 'homepage'],
            [
                'title' => 'Homepage',
                'content' => null,
                'excerpt' => 'Website sayur & bahan dapur — homepage',
                'status' => 'published',
                'published_at' => now(),
                'content_type_id' => $type->id,
                'meta' => $meta,
                'active_sections' => array_values(array_map(fn ($s) => $s->id, $sections)),
            ]
        );

        if (empty($content->slug)) {
            $content->slug = Str::slug($content->title) . '-' . $content->id;
            $content->saveQuietly();
        }
    }

    private function seedMenus(): void
    {
        Menu::updateOrCreate(
            ['slug' => 'main-menu'],
            [
                'name' => 'Main Navigation',
                'location' => 'main',
                'is_active' => true,
                'sort_order' => 0,
                'items' => [
                    ['label' => 'Beranda', 'url' => '/', 'sort_order' => 0],
                    ['label' => 'Produk', 'url' => '/#produk', 'sort_order' => 1],
                    ['label' => 'Tentang', 'url' => '/#tentang', 'sort_order' => 2],
                    ['label' => 'Kontak', 'url' => '/contact', 'sort_order' => 3],
                ],
            ]
        );

        Menu::updateOrCreate(
            ['slug' => 'footer-menu'],
            [
                'name' => 'Footer',
                'location' => 'footer',
                'is_active' => true,
                'sort_order' => 0,
                'items' => [
                    ['label' => 'Tautan', 'sort_order' => 0, 'children' => [
                        ['label' => 'Produk', 'url' => '/#produk', 'sort_order' => 0],
                        ['label' => 'Tentang', 'url' => '/#tentang', 'sort_order' => 1],
                        ['label' => 'Kontak', 'url' => '/contact', 'sort_order' => 2],
                    ]],
                    ['label' => 'Produk', 'sort_order' => 1, 'children' => [
                        ['label' => 'Sayur Mayur', 'url' => '/#produk', 'sort_order' => 0],
                        ['label' => 'Telur', 'url' => '/#produk', 'sort_order' => 1],
                        ['label' => 'Ikan', 'url' => '/#produk', 'sort_order' => 2],
                        ['label' => 'Ayam', 'url' => '/#produk', 'sort_order' => 3],
                        ['label' => 'Daging', 'url' => '/#produk', 'sort_order' => 4],
                        ['label' => 'Bahan Dapur', 'url' => '/#produk', 'sort_order' => 5],
                    ]],
                ],
            ]
        );
        Menu::whereIn('slug', ['main-navigation', 'footer-company'])->delete();
    }
}
