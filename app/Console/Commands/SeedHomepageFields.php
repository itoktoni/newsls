<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SeedHomepageFields extends Command
{
    protected $signature = 'seed:homepage-fields';

    protected $description = 'Seed homepage fields, sections, and content from template';

    public function handle()
    {
        $this->seedFields();
        $this->seedSections();
        $this->seedContent();
        $this->info('Homepage fields seeded successfully.');
    }

    private function seedFields()
    {
        $dir = base_path('content/fields');
        // Clear old files
        foreach (glob("$dir/*.json") as $f) {
            unlink($f);
        }

        $now = now()->toDateTimeString();
        $fields = $this->getFieldDefinitions();

        foreach ($fields as $field) {
            $data = [
                'id' => $field['id'],
                'name' => $field['name'],
                'label' => $field['label'] ?? ucfirst(str_replace('_', ' ', $field['name'])),
                'type' => $field['type'],
                'config' => $field['config'] ?? null,
                'rules' => null,
                'is_required' => $field['is_required'] ?? false,
                'default_value' => null,
                'sort_order' => $field['sort_order'] ?? 0,
                'parent_id' => $field['parent_id'] ?? null,
                'mode' => $field['mode'] ?? null,
                'min' => $field['min'] ?? null,
                'max' => $field['max'] ?? null,
                'collapsed' => null,
                'sortable' => null,
                'cloneable' => null,
                'layouts' => null,
                'type_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            file_put_contents("$dir/{$field['id']}.json", json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }

        $this->info('Created '.count($fields).' field files.');
    }

    private function getFieldDefinitions(): array
    {
        return [
            // === ROOT FIELDS (section references) ===
            ['id' => 1, 'name' => 'hero', 'label' => 'Hero Slider', 'type' => 'container', 'mode' => 'multiple', 'sort_order' => 0],
            ['id' => 2, 'name' => 'certifications', 'label' => 'Certifications', 'type' => 'container', 'mode' => 'multiple', 'sort_order' => 1],
            ['id' => 3, 'name' => 'verification', 'label' => 'Verification', 'type' => 'container', 'sort_order' => 2],
            ['id' => 4, 'name' => 'services', 'label' => 'Services', 'type' => 'container', 'mode' => 'multiple', 'sort_order' => 3],
            ['id' => 5, 'name' => 'competency', 'label' => 'Competency', 'type' => 'container', 'mode' => 'multiple', 'sort_order' => 4],
            ['id' => 6, 'name' => 'news', 'label' => 'News', 'type' => 'container', 'sort_order' => 5],
            ['id' => 7, 'name' => 'cta', 'label' => 'CTA', 'type' => 'container', 'sort_order' => 6],
            ['id' => 8, 'name' => 'clients', 'label' => 'Clients', 'type' => 'container', 'mode' => 'multiple', 'sort_order' => 7],

            // === HERO CHILDREN ===
            ['id' => 9, 'name' => 'image', 'label' => 'Image', 'type' => 'image', 'parent_id' => 1, 'sort_order' => 0],
            ['id' => 10, 'name' => 'subtitle', 'label' => 'Subtitle', 'type' => 'text', 'parent_id' => 1, 'sort_order' => 1],
            ['id' => 11, 'name' => 'title', 'label' => 'Title', 'type' => 'text', 'parent_id' => 1, 'sort_order' => 2],
            ['id' => 12, 'name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'parent_id' => 1, 'sort_order' => 3],
            ['id' => 13, 'name' => 'button1_text', 'label' => 'Button 1 Text', 'type' => 'text', 'parent_id' => 1, 'sort_order' => 4],
            ['id' => 14, 'name' => 'button1_link', 'label' => 'Button 1 Link', 'type' => 'text', 'parent_id' => 1, 'sort_order' => 5],
            ['id' => 15, 'name' => 'button2_text', 'label' => 'Button 2 Text', 'type' => 'text', 'parent_id' => 1, 'sort_order' => 6],
            ['id' => 16, 'name' => 'button2_link', 'label' => 'Button 2 Link', 'type' => 'text', 'parent_id' => 1, 'sort_order' => 7],

            // === CERTIFICATIONS CHILDREN ===
            ['id' => 17, 'name' => 'image', 'label' => 'Image', 'type' => 'image', 'parent_id' => 2, 'sort_order' => 0],
            ['id' => 18, 'name' => 'title', 'label' => 'Title', 'type' => 'text', 'parent_id' => 2, 'sort_order' => 1],
            ['id' => 19, 'name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'parent_id' => 2, 'sort_order' => 2],
            ['id' => 20, 'name' => 'link_text', 'label' => 'Link Text', 'type' => 'text', 'parent_id' => 2, 'sort_order' => 3],

            // === VERIFICATION FIELDS (no container) ===
            ['id' => 21, 'name' => 'subtitle', 'label' => 'Subtitle', 'type' => 'text', 'parent_id' => 3, 'sort_order' => 0],
            ['id' => 22, 'name' => 'title', 'label' => 'Title', 'type' => 'text', 'parent_id' => 3, 'sort_order' => 1],
            ['id' => 23, 'name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'parent_id' => 3, 'sort_order' => 2],
            ['id' => 24, 'name' => 'input_placeholder', 'label' => 'Input Placeholder', 'type' => 'text', 'parent_id' => 3, 'sort_order' => 3],
            ['id' => 25, 'name' => 'button_text', 'label' => 'Button Text', 'type' => 'text', 'parent_id' => 3, 'sort_order' => 4],

            // === SERVICES CHILDREN ===
            ['id' => 26, 'name' => 'icon', 'label' => 'Icon', 'type' => 'text', 'parent_id' => 4, 'sort_order' => 0],
            ['id' => 27, 'name' => 'title', 'label' => 'Title', 'type' => 'text', 'parent_id' => 4, 'sort_order' => 1],
            ['id' => 28, 'name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'parent_id' => 4, 'sort_order' => 2],
            ['id' => 29, 'name' => 'features', 'label' => 'Features', 'type' => 'container', 'mode' => 'multiple', 'parent_id' => 4, 'sort_order' => 3],
            ['id' => 30, 'name' => 'text', 'label' => 'Feature Text', 'type' => 'text', 'parent_id' => 29, 'sort_order' => 0],

            // === COMPETENCY CHILDREN ===
            ['id' => 31, 'name' => 'image', 'label' => 'Image', 'type' => 'image', 'parent_id' => 5, 'sort_order' => 0],
            ['id' => 32, 'name' => 'icon', 'label' => 'Icon', 'type' => 'text', 'parent_id' => 5, 'sort_order' => 1],
            ['id' => 33, 'name' => 'title', 'label' => 'Title', 'type' => 'text', 'parent_id' => 5, 'sort_order' => 2],
            ['id' => 34, 'name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'parent_id' => 5, 'sort_order' => 3],

            // === NEWS MAIN ARTICLE CHILDREN ===
            ['id' => 35, 'name' => 'main_article', 'label' => 'Main Article', 'type' => 'container', 'mode' => 'single', 'parent_id' => 6, 'sort_order' => 0],
            ['id' => 36, 'name' => 'image', 'label' => 'Image', 'type' => 'image', 'parent_id' => 35, 'sort_order' => 0],
            ['id' => 37, 'name' => 'category', 'label' => 'Category', 'type' => 'text', 'parent_id' => 35, 'sort_order' => 1],
            ['id' => 38, 'name' => 'year', 'label' => 'Year', 'type' => 'text', 'parent_id' => 35, 'sort_order' => 2],
            ['id' => 39, 'name' => 'title', 'label' => 'Title', 'type' => 'text', 'parent_id' => 35, 'sort_order' => 3],
            ['id' => 40, 'name' => 'link_text', 'label' => 'Link Text', 'type' => 'text', 'parent_id' => 35, 'sort_order' => 4],

            // === NEWS SIDE ARTICLES CHILDREN ===
            ['id' => 41, 'name' => 'side_articles', 'label' => 'Side Articles', 'type' => 'container', 'mode' => 'multiple', 'parent_id' => 6, 'sort_order' => 1],
            ['id' => 42, 'name' => 'bg_color', 'label' => 'Background Color', 'type' => 'color', 'parent_id' => 41, 'sort_order' => 0],
            ['id' => 43, 'name' => 'icon', 'label' => 'Icon', 'type' => 'text', 'parent_id' => 41, 'sort_order' => 1],
            ['id' => 44, 'name' => 'title', 'label' => 'Title', 'type' => 'text', 'parent_id' => 41, 'sort_order' => 2],
            ['id' => 45, 'name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'parent_id' => 41, 'sort_order' => 3],
            ['id' => 46, 'name' => 'link_text', 'label' => 'Link Text', 'type' => 'text', 'parent_id' => 41, 'sort_order' => 4],

            // === CTA FIELDS (no container) ===
            ['id' => 47, 'name' => 'title', 'label' => 'Title', 'type' => 'text', 'parent_id' => 7, 'sort_order' => 0],
            ['id' => 48, 'name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'parent_id' => 7, 'sort_order' => 1],
            ['id' => 49, 'name' => 'button1_text', 'label' => 'Button 1 Text', 'type' => 'text', 'parent_id' => 7, 'sort_order' => 2],
            ['id' => 50, 'name' => 'button1_link', 'label' => 'Button 1 Link', 'type' => 'text', 'parent_id' => 7, 'sort_order' => 3],
            ['id' => 51, 'name' => 'button2_text', 'label' => 'Button 2 Text', 'type' => 'text', 'parent_id' => 7, 'sort_order' => 4],
            ['id' => 52, 'name' => 'button2_link', 'label' => 'Button 2 Link', 'type' => 'text', 'parent_id' => 7, 'sort_order' => 5],
            ['id' => 53, 'name' => 'image', 'label' => 'Image', 'type' => 'image', 'parent_id' => 7, 'sort_order' => 6],

            // === CLIENTS CHILDREN ===
            ['id' => 54, 'name' => 'icon', 'label' => 'Icon', 'type' => 'text', 'parent_id' => 8, 'sort_order' => 0],
            ['id' => 55, 'name' => 'name', 'label' => 'Name', 'type' => 'text', 'parent_id' => 8, 'sort_order' => 1],
        ];
    }

    private function seedSections()
    {
        $dir = base_path('content/sections');
        foreach (glob("$dir/*.json") as $f) {
            unlink($f);
        }

        $now = now()->toDateTimeString();
        $sections = [
            ['id' => 1, 'name' => 'hero', 'field_ids' => ['1'], 'sort_order' => 0],
            ['id' => 2, 'name' => 'certifications', 'field_ids' => ['2'], 'sort_order' => 1],
            ['id' => 3, 'name' => 'verification', 'field_ids' => ['3'], 'sort_order' => 2],
            ['id' => 4, 'name' => 'services', 'field_ids' => ['4'], 'sort_order' => 3],
            ['id' => 5, 'name' => 'competency', 'field_ids' => ['5'], 'sort_order' => 4],
            ['id' => 6, 'name' => 'news', 'field_ids' => ['6'], 'sort_order' => 5],
            ['id' => 7, 'name' => 'cta', 'field_ids' => ['7'], 'sort_order' => 6],
            ['id' => 8, 'name' => 'clients', 'field_ids' => ['8'], 'sort_order' => 7],
        ];

        foreach ($sections as $section) {
            $data = [
                'id' => $section['id'],
                'name' => $section['name'],
                'content_type_id' => '1',
                'field_ids' => $section['field_ids'],
                'sort_order' => (string) $section['sort_order'],
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
            file_put_contents("$dir/{$section['id']}.json", json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }

        $this->info('Created '.count($sections).' section files.');
    }

    private function seedContent()
    {
        $dir = base_path('content/contents');
        $now = now()->toDateTimeString();

        $data = [
            'id' => 1,
            'content_type_id' => '1',
            'title' => 'homepage',
            'content' => '<p>Homepage content</p>',
            'excerpt' => 'ECM Homepage',
            'status' => 'published',
            'menu_order' => '0',
            'meta' => $this->getContentMeta(),
            'created_at' => $now,
            'updated_at' => $now,
        ];

        file_put_contents("$dir/1.json", json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->info('Content file updated.');
    }

    private function getContentMeta(): array
    {
        return [
            'hero' => [
                [
                    'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuAf-aS9V131adgmumVvTDQz-d_eJhhSPp7f73U69OguQA45zdhMhEYowf0HZr5X4XzvVICkyk1Pp5KisTlfFUP84ybja4ftXS99i_kluPea2e18hNENoubFMkb9muSJGKfJ2MWaG0eQrx5HCEk4TDa5aBEG_Z5z24orCECqt_v7hrx3Z3CtUDGgDt7IQt1UmOUFfr0e1SN_0DT-yi51sxdxftPBc-tlzoEA7H9vTTNJnYtPUgZyyNGMNzWHUp5ITOrpQ4ArUi-NIny7',
                    'subtitle' => 'Institusi IPFK Terakreditasi',
                    'title' => 'Ketelitian dalam Setiap Pengukuran',
                    'description' => 'Menjamin ketelitian dan keamanan melalui kalibrasi dan verifikasi teknis alat kesehatan sesuai standar nasional dan internasional.',
                    'button1_text' => 'Order Sekarang',
                    'button1_link' => '#order',
                    'button2_text' => 'Layanan Kami',
                    'button2_link' => '#services',
                ],
                [
                    'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCiT2ICMT31yHvNTlw3DoKD-EtcQ8QSParinKwYZsDYy7LJyDVzi0SQbN-KuVBYsHG4RRuo68xFNAv-WMZM6mO33uvNUwOPMx_P3Wb9r8__3dXheQyf4626kp12aCig05lNhjbfJR97GoBSojdEpysX03xzIgBcDv7jyPuN_Fx_93m98JjfMU1zrXb2PhsftTvPbTOBj5q3WcJA2oiPAkXDwUCyEvl5UBjNdOGNIQOdqtd3tE-5LMoqameG9Xr2YzI4AdUulHQqYDj1',
                    'subtitle' => 'Inspeksi Preventive',
                    'title' => 'Pencegahan Sejak Dini',
                    'description' => 'Pemeriksaan berkala untuk memastikan alat kesehatan berfungsi optimal dan memenuhi standar keselamatan yang berlaku.',
                    'button1_text' => 'Jadwalkan Inspeksi',
                    'button1_link' => '#contact',
                    'button2_text' => 'Pelajari Lebih Lanjut',
                    'button2_link' => '#services',
                ],
                [
                    'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDfBzaN7GVPg5R1-N15vIiN7ALsjLuEiE0ciwhNuFrUwfLQQqznUs4D_vCdCJTL9FVqewA1EJG1k0gorTBJLLtdmxDlRkkurIgnIX9b4WqdsXFXvOsYbTYs_xHfQr9_rPet5toxmK-SV_AVjzJSBm-bVxypkZ8pDuIov7B-nn600nIkjK8ff_QkpNDV4YcvN_pZRo16yrmd0IcUyyDS64XiskopusEofGaKcle-Ky00zuP5tAwFAPXtfpPaKfc1PfaLmtTNIc58Extu',
                    'subtitle' => 'Verifikasi Sertifikat',
                    'title' => 'Kapan Saja, Di Saja',
                    'description' => 'Pastikan keaslian sertifikat kalibrasi Anda melalui database akreditasi kami yang terintegrasi dan terpercaya.',
                    'button1_text' => 'Verifikasi Sekarang',
                    'button1_link' => '#sertifikat',
                    'button2_text' => 'Lihat Sertifikat',
                    'button2_link' => '#sertifikat',
                ],
            ],
            'certifications' => [
                [
                    'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuAni7KlIuMfqfnEQ_xYG7xGURljqIBBhN1LZSLAetRSJWwY9fTZm2sjPZQcrynMPEjeJhf9PxYdTlcZfLo9BS0W5QTKEh4hprmUOag6MxXdivQL6qpl-ayH0Y_wSQNUizWjUNeR836PhR1DOnv3Ep0UAG61AQ1QA9_h_ZGhkgJ8cwZBjpv_DoeR8S6BzTwE6GxYlPdte8fRkoU6aS7W6F89XcG_4WfUA67cyD4HSp5Wqk8s3xtNDgcqsZzeb3_wIPV7iw3R5vOF5Vq5',
                    'title' => 'KAN LK-XXX-IDN',
                    'description' => 'Akreditasi nasional untuk institusi pengujian fasilitas kesehatan sesuai standar nasional dan internasional.',
                    'link_text' => 'Verify Details',
                ],
                [
                    'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCHXcQ4pYaxFlXnw4afuReAYyYozJTtqq1_xu7PIXXwz3jmwNQb8iFN1kpLHG_1XQL__NYC5-X5TOwNUMARBpCRByGF41PqvqkctH66qWwAqXtle339tKsYTEQIl-1GgN-3ABOFxeS4wQbxcm2mDmewwc6u31v9OLjgpnESB-mTu4kfD6t2MZ7U27kW4vNzsR6bMi0XYY-LJoF9z3pLH5LUd4ml7ya1b-snpleH-4RfKchIaPcnQ6MR5rHrC5AwarmqN0DP3ElTrma6',
                    'title' => 'IPFK Berstandar',
                    'description' => 'Standar kompetensi lembaga pengujian fasilitas kesehatan untuk memastikan ketelitian teknis dan mutu layanan.',
                    'link_text' => 'Verify Details',
                ],
                [
                    'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBpZmIBi2QvqRC46sGLZQP7BvqUcnWA_XbRqAONaeGEjT967DViGQ2s_kA2ZXKn_Bl0x6f9R7aoB0MH1LmaIkPepUH4iMGyFaMOy0DQyh0Tas5zKY3bwRga6GpRcZCryfwP3N993SsgQHc-iiDBP0Sr8jOhBNlVimtzXvZxzRM-cxDeEBSD9gV4hysMhutAZWwlerM01lWFkBgCaCJ4VtojG6eUT9ZMytCPaCocpgs4rQ9hvLhMpnkdjiX7g_M8HOiAYRUQiH-cCmJM',
                    'title' => 'Ketelitian Tinggi',
                    'description' => 'Ketelitian mutlak melalui standar yang tertraceability untuk setiap alat yang dikalibrasi.',
                    'link_text' => 'Verify Details',
                ],
            ],
            'verification' => [
                'subtitle' => 'Otoritas Sertifikat',
                'title' => 'Portal Verifikasi Sertifikat',
                'description' => 'Verifikasi keaslian sertifikat kalibrasi alat kesehatan Anda menggunakan database akreditasi kami yang terintegrasi.',
                'input_placeholder' => 'Masukkan Nomor Sertifikat (contoh: ECM-2024-XXXX)',
                'button_text' => 'Verifikasi Sekarang',
            ],
            'services' => [
                [
                    'icon' => 'monitor_heart',
                    'title' => 'Kalibrasi Alat Kesehatan',
                    'description' => 'Pengukuran dan penyesuaian ketelitian alat kesehatan sesuai standar nasional dan internasional.',
                    'features' => [
                        ['text' => 'Alat Monitoring Vital Signs'],
                        ['text' => 'Alat Lab & Diagnostik'],
                    ],
                ],
                [
                    'icon' => 'radiology',
                    'title' => 'Inspeksi Preventive',
                    'description' => 'Pemeriksaan berkala untuk memastikan alat kesehatan berfungsi optimal dan aman digunakan.',
                    'features' => [
                        ['text' => 'Pemeriksaan Fungsional'],
                        ['text' => 'Uji Keselamatan Alat'],
                    ],
                ],
                [
                    'icon' => 'medical_services',
                    'title' => 'Konsultasi Teknis',
                    'description' => 'Bimbingan ahli untuk pengelolaan dan pemeliharaan alat kesehatan di fasilitas Anda.',
                    'features' => [
                        ['text' => 'Rencana Pemeliharaan'],
                        ['text' => 'Dokumentasi & Sertifikat'],
                    ],
                ],
            ],
            'competency' => [
                [
                    'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCiT2ICMT31yHvNTlw3DoKD-EtcQ8QSParinKwYZsDYy7LJyDVzi0SQbN-KuVBYsHG4RRuo68xFNAv-WMZM6mO33uvNUwOPMx_P3Wb9r8__3dXheQyf4626kp12aCig05lNhjbfJR97GoBSojdEpysX03xzIgBcDv7jyPuN_Fx_93m98JjfMU1zrXb2PhsftTvPbTOBj5q3WcJA2oiPAkXDwUCyEvl5UBjNdOGNIQOdqtd3tE-5LMoqameG9Xr2YzI4AdUulHQqYDj1',
                    'icon' => 'medical_services',
                    'title' => 'Peralatan Diagnostik',
                    'description' => 'Kalibrasi menyeluruh untuk radiologi, ultrasonografi, dan sistem monitoring hemodinamik.',
                ],
                [
                    'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuC_MQvVMWqA2WzHZCaT40MEuZHXZF_IUxQ0SIi_gXQ7UzrSiMtC735KEtlU5AS0VNjivAyTU_YxthvLqC83-CazK5HfEWuJk4EW_8WTNXVTxzc07SRUj91e_qSd1rJmw6VGSw4DjEUBZRsZKPk_vT80-jBIG2LCcePYwNm3aN3f6QDRwfdg_Zge3cfHUFGd0UKcXnwemIY4RhQiR3-rJtjdHTzaeFO2wLR7FFMqLBn4eIMbPGjjc9tUHF5LuGnsDJpHOGedPhl2CVoP',
                    'icon' => 'vital_signs',
                    'title' => 'Sistem Life Support',
                    'description' => 'Verifikasi kritis untuk ventilator, mesin anestesi, dan unit life support jantung.',
                ],
                [
                    'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDfBzaN7GVPg5R1-N15vIiN7ALsjLuEiE0ciwhNuFrUwfLQQqznUs4D_vCdCJTL9FVqewA1EJG1k0gorTBJLLtdmxDlRkkurIgnIX9b4WqdsXFXvOsYbTYs_xHfQr9_rPet5toxmK-SV_AVjzJSBm-bVxypkZ8pDuIov7B-nn600nIkjK8ff_QkpNDV4YcvN_pZRo16yrmd0IcUyyDS64XiskopusEofGaKcle-Ky00zuP5tAwFAPXtfpPaKfc1PfaLmtTNIc58Extu',
                    'icon' => 'biotech',
                    'title' => 'Analisis Laboratorium',
                    'description' => 'Traceability metrologis untuk analyzer otomatis, pipet, dan spektrofotometer.',
                ],
                [
                    'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDW9kZzAbRGg-YAIHL2H8MRy2IKQSuT0ffZIMZsC8AHNsI1N5JBSnbGy0U4Xm5KQYZmzwWQQM_lV9zEiqbuuFbZGFlH_0UTEJXvb3ieqnLn4xVAHyceQYfjnJmpMabseKnc3ik7D1JF8HOUzSB1gstQdK820_nh4KVkJxVLyRbyAyG8DD1MN_WFqu_YmJbtCAx0hnyAXmwCr2bE4SsBFc-w8F1KEFJe8vHOnJCTaHeP-kCcmCsLDKcNFH3IC8IQnJ23pabmUN4heBOp',
                    'icon' => 'precision_manufacturing',
                    'title' => 'Instrumen Bedah',
                    'description' => 'Pengujian presisi untuk laser bedah, unit elektrokauter, dan asisten robotik.',
                ],
            ],
            'news' => [
                'main_article' => [
                    'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBd8xKBdkjsLHWibHkNIx5Nn4N8Z-446sgDnskdWs7Rbio7ON1Zjmf1qg0xtT9v5g48WtqcAkljp642B287Sk0xjGOpqa7Zs1rD89wSum2TB0uMrzJKqt_Vd-gVdSSDHa5dLQGz9ecLgIRGTXFRALl0fEUilDx8phdo6z9fC6Z-_w3K1oDO0uFsG8lIdeohBqvI3FzuJyOJqpaFCkOdLibJR0guo1MSJuWIK7OYzoz96j3ra0ydC_cwaJYEWn2HZqJp83MWZnuigdZ1',
                    'category' => 'ARTIKEL',
                    'year' => '2024',
                    'title' => 'Kalibrasi Alat Kesehatan: Panduan Lengkap untuk Fasilitas Kesehatan',
                    'link_text' => 'Baca Selengkapnya',
                ],
                'side_articles' => [
                    [
                        'bg_color' => '#005f3b',
                        'icon' => 'policy',
                        'title' => 'Regulasi Terbaru: Pedoman Kemenkes 2024',
                        'description' => 'Bagaimana mandat baru untuk alat terapi mempengaruhi penyedia layanan kesehatan di Indonesia.',
                        'link_text' => 'Baca Selengkapnya',
                    ],
                    [
                        'bg_color' => '#ffffff',
                        'icon' => 'microwave',
                        'title' => 'Studi Kasus: Efisiensi Kalibrasi di Fasilitas Kesehatan',
                        'description' => 'Analisis mendalam implementasi sistem manajemen kalibrasi terintegrasi kami.',
                        'link_text' => 'Lihat Metrics',
                    ],
                ],
            ],
            'cta' => [
                'title' => 'Tingkatkan Keandalan Fasilitas Anda',
                'description' => 'Bekerja sama dengan ECM untuk audit teknis dan siklus kalibrasi yang menjamin ketersediaan alat dan ketelitian maksimal.',
                'button1_text' => 'Konsultasi Sekarang',
                'button1_link' => '#contact',
                'button2_text' => 'Lihat Katalog Layanan',
                'button2_link' => '#services',
                'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDDfRjdC6ppIdw5jdSCd8Lce1igf3dUFJWbP2IRf59vbWKvWzNFDTtGmFnSkQvOl7ye99nQs09SwAR7H0NIQkdJX3OQhv6ljvXpOovVPwttC6cX36KVgPzSYaMgn43VvPYSXla4rxkMls1NKwyTqDMxVFpIaIhyEYXJqkc292X8bgxQDmBjWZzY4FyC1XN2TeYMAkW0P3AB94-Q2XJEvbMvq2uNE9Y-HnsJohg5DZHJQRTc3rDODA7VcwOMjCsMQLnTaWDyG18BMvHk',
            ],
            'clients' => [
                ['icon' => 'apartment', 'name' => 'National Hospital'],
                ['icon' => 'science', 'name' => 'BioMed Research Lab'],
                ['icon' => 'medical_services', 'name' => "St. Mary's Medical"],
                ['icon' => 'public', 'name' => 'Global Health Institute'],
                ['icon' => 'biotech', 'name' => 'Apex Diagnostics'],
                ['icon' => 'local_hospital', 'name' => 'Mayo Regional Clinic'],
                ['icon' => 'biotech', 'name' => 'Siloam Hospitals'],
                ['icon' => 'science', 'name' => 'Pondok Indah Group'],
                ['icon' => 'apartment', 'name' => 'RS Cipto'],
                ['icon' => 'health_and_safety', 'name' => 'Medistra Hospital'],
            ],
        ];
    }
}
