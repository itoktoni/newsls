<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Menu Configuration
    |--------------------------------------------------------------------------
    |
    | Define menu items for desktop sidebar, mobile drawer, and bottom nav.
    | Each item: route (string), icon (string), label (string)
    | Sections: label (string), items (array)
    | Bottom nav: only 5 items max, uses short label
    |
    */

    'sidebar' => [
        [
            'label' => null,
            'items' => [
                ['route' => 'dashboard', 'icon' => 'home', 'label' => 'Dashboard'],
            ],
        ],
        [
            'label' => 'Pengguna & Audit',
            'items' => [
                ['route' => 'user.getTable', 'icon' => 'manage_accounts', 'label' => 'Users', 'match' => ['user.*']],
                ['route' => 'mobile-menu.getTable', 'icon' => 'phone_android', 'label' => 'Menu Mobile', 'match' => ['mobile-menu.*']],
                ['route' => 'activity-log.getTable', 'icon' => 'history', 'label' => 'Activity Log', 'match' => ['activity-log.*']],
            ],
        ],
        [
            'label' => 'Master Data',
            'items' => [
                ['route' => 'jenis-linen.getTable', 'icon' => 'laundry', 'label' => 'Jenis Linen', 'match' => ['jenis-linen.*']],
                ['route' => 'ruangan.getTable', 'icon' => 'meeting_room', 'label' => 'Ruangan', 'match' => ['ruangan.*']],
                ['route' => 'group-rs.getTable', 'icon' => 'corporate_fare', 'label' => 'Group RS', 'match' => ['group-rs.*']],
                ['route' => 'rs.getTable', 'icon' => 'local_hospital', 'label' => 'Rumah Sakit', 'match' => ['rs.*']],
                // ['route' => 'kategori.getTable', 'icon' => 'category', 'label' => 'Kategori', 'match' => ['kategori.*']],
                // ['route' => 'jenis-bahan.getTable', 'icon' => 'texture', 'label' => 'Bahan', 'match' => ['jenis-bahan.*']],
                // ['route' => 'supplier.getTable', 'icon' => 'local_shipping', 'label' => 'Supplier', 'match' => ['supplier.*']],
            ],
        ],
        [
            'label' => 'Manajemen Linen',
            'items' => [
                ['route' => 'config-linen.getTable', 'icon' => 'settings_input_component', 'label' => 'Config Linen', 'match' => ['config-linen.*']],
                ['route' => 'detail-linen.getTable', 'icon' => 'qr_code_2', 'label' => 'Data Linen', 'match' => ['detail-linen.*']],
                ['route' => 'transaksi.getTable', 'icon' => 'receipt_long', 'label' => 'Transaksi', 'match' => ['transaksi.*']],
                ['route' => 'bersih.getTable', 'icon' => 'inventory_2', 'label' => 'Bersih', 'match' => ['bersih.*']],
                ['route' => 'opname.getTable', 'icon' => 'fact_check', 'label' => 'Opname', 'match' => ['opname.*']],
                ['route' => 'warehouse.getTable', 'icon' => 'warehouse', 'label' => 'Warehouse', 'match' => ['warehouse.*']],
            ],
        ],
        [
            'label' => 'Report Opname',
            'items' => [
                ['route' => 'report-rekap-opname.getTable', 'icon' => 'fact_check', 'label' => 'Rekap Opname', 'match' => ['report-rekap-opname.*']],
                ['route' => 'report-opname-detail.getTable', 'icon' => 'list_alt', 'label' => 'Detail Opname', 'match' => ['report-opname-detail.*']],
                ['route' => 'report-opname-summary.getTable', 'icon' => 'summarize', 'label' => 'Summary Opname', 'match' => ['report-opname-summary.*']],
                ['route' => 'report-opname-hilang.getTable', 'icon' => 'search_off', 'label' => 'Hilang Opname', 'match' => ['report-opname-hilang.*']],
                ['route' => 'report-opname-hilang-warehouse.getTable', 'icon' => 'warehouse', 'label' => 'Hilang Warehouse', 'match' => ['report-opname-hilang-warehouse.*']],
                ['route' => 'report-opname-mutasi.getTable', 'icon' => 'swap_horiz', 'label' => 'Opname Mutasi', 'match' => ['report-opname-mutasi.*']],
            ],
        ],
        [
            'label' => 'Report Rekap',
            'items' => [
                ['route' => 'report-rekap-kotor.getTable', 'icon' => 'summarize', 'label' => 'Rekap Kotor', 'match' => ['report-rekap-kotor.*']],
                ['route' => 'report-rekap-bersih.getTable', 'icon' => 'summarize', 'label' => 'Rekap Bersih', 'match' => ['report-rekap-bersih.*']],
                ['route' => 'report-rekap-retur.getTable', 'icon' => 'summarize', 'label' => 'Rekap Retur', 'match' => ['report-rekap-retur.*']],
                ['route' => 'report-rekap-rewash.getTable', 'icon' => 'summarize', 'label' => 'Rekap Rewash', 'match' => ['report-rekap-rewash.*']],
                ['route' => 'report-kotor-vs-bersih.getTable', 'icon' => 'compare_arrows', 'label' => 'Kotor vs Bersih', 'match' => ['report-kotor-vs-bersih.*']],
                ['route' => 'report-in-vs-out.getTable', 'icon' => 'compare_arrows', 'label' => 'In vs Out', 'match' => ['report-in-vs-out.*']],
            ],
        ],
        [
            'label' => 'Report Detail',
            'items' => [
                ['route' => 'report-detail-kotor.getTable', 'icon' => 'receipt_long', 'label' => 'Detail Kotor', 'match' => ['report-detail-kotor.*']],
                ['route' => 'report-detail-retur.getTable', 'icon' => 'receipt_long', 'label' => 'Detail Retur', 'match' => ['report-detail-retur.*']],
                ['route' => 'report-detail-rewash.getTable', 'icon' => 'receipt_long', 'label' => 'Detail Rewash', 'match' => ['report-detail-rewash.*']],
            ],
        ],
        [
            'label' => 'Report Pengiriman',
            'items' => [
                ['route' => 'report-detail-pengiriman-bersih.getTable', 'icon' => 'local_shipping', 'label' => 'Kirim Bersih', 'match' => ['report-detail-pengiriman-bersih.*']],
                ['route' => 'report-detail-pengiriman-retur.getTable', 'icon' => 'local_shipping', 'label' => 'Kirim Retur', 'match' => ['report-detail-pengiriman-retur.*']],
                ['route' => 'report-detail-pengiriman-rewash.getTable', 'icon' => 'local_shipping', 'label' => 'Kirim Rewash', 'match' => ['report-detail-pengiriman-rewash.*']],
                ['route' => 'report-detail-pengiriman-linen-baru.getTable', 'icon' => 'local_shipping', 'label' => 'Kirim Baru', 'match' => ['report-detail-pengiriman-linen-baru.*']],
            ],
        ],
        [
            'label' => 'Report Linen',
            'items' => [
                ['route' => 'report-data-linen.getTable', 'icon' => 'print', 'label' => 'Data Linen', 'match' => ['report-data-linen.*']],
                ['route' => 'report-register-linen.getTable', 'icon' => 'app_registration', 'label' => 'Register Linen', 'match' => ['report-register-linen.*']],
                ['route' => 'report-hilang-linen.getTable', 'icon' => 'search_off', 'label' => 'Linen Hilang', 'match' => ['report-hilang-linen.*']],
                ['route' => 'report-penggantian-linen.getTable', 'icon' => 'autorenew', 'label' => 'Ganti Chip', 'match' => ['report-penggantian-linen.*']],
                ['route' => 'report-pending-linen.getTable', 'icon' => 'hourglass_empty', 'label' => 'Pending Linen', 'match' => ['report-pending-linen.*']],
                ['route' => 'report-detail-pending-linen.getTable', 'icon' => 'hourglass_empty', 'label' => 'Detail Pending', 'match' => ['report-detail-pending-linen.*']],
            ],
        ],
        [
            'label' => 'Report Summary',
            'items' => [
                ['route' => 'report-summary-pengiriman-bersih.getTable', 'icon' => 'summarize', 'label' => 'Summary Bersih', 'match' => ['report-summary-pengiriman-bersih.*']],
                ['route' => 'report-summary-pengiriman-retur.getTable', 'icon' => 'summarize', 'label' => 'Summary Retur', 'match' => ['report-summary-pengiriman-retur.*']],
                ['route' => 'report-summary-pengiriman-rewash.getTable', 'icon' => 'summarize', 'label' => 'Summary Rewash', 'match' => ['report-summary-pengiriman-rewash.*']],
                ['route' => 'report-summary-pengiriman-linen-baru.getTable', 'icon' => 'summarize', 'label' => 'Summary Baru', 'match' => ['report-summary-pengiriman-linen-baru.*']],
                ['route' => 'report-invoice.getTable', 'icon' => 'receipt', 'label' => 'Invoice', 'match' => ['report-invoice.*']],
            ],
        ],

        [
            'label' => 'CMS',
            'items' => [
                ['route' => 'cms-type.getTable', 'icon' => 'category', 'label' => 'Types', 'match' => ['cms-type.*']],
                ['route' => 'field.getTable', 'icon' => 'input', 'label' => 'Fields', 'match' => ['field.*']],
                ['route' => 'section.getTable', 'icon' => 'view_agenda', 'label' => 'Sections', 'match' => ['section.*']],
                ['route' => 'content.getTable', 'icon' => 'article', 'label' => 'Content', 'match' => ['content.*']],
                ['route' => 'category.getTable', 'icon' => 'sell', 'label' => 'Categories', 'match' => ['category.*']],
                ['route' => 'tag.getTable', 'icon' => 'label', 'label' => 'Tags', 'match' => ['tag.*']],
                ['route' => 'menu.getTable', 'icon' => 'menu', 'label' => 'Menus', 'match' => ['menu.*']],
            ],
        ],
        [
            'label' => 'Settings',
            'items' => [
                ['route' => 'settings.website', 'icon' => 'language', 'label' => 'Website'],
                ['route' => 'settings.env', 'icon' => 'settings', 'label' => 'Environment'],
                ['route' => 'native-bridge-test', 'icon' => 'phone_android', 'label' => 'NativeBridge Test'],
            ],
        ],
    ],

    'bottom_nav' => [

        ['route' => 'dashboard', 'icon' => 'home', 'label' => 'Left'],
        ['route' => 'dashboard', 'icon' => 'home', 'label' => 'Kiri'],
        ['route' => 'dashboard', 'icon' => 'home', 'label' => 'Home'],
        ['route' => 'dashboard', 'icon' => 'home', 'label' => 'Kanan'],
        ['route' => 'dashboard', 'icon' => 'home', 'label' => 'Right'],

    ],

];
