<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Printer Type
    |--------------------------------------------------------------------------
    |
    | Jenis printer default: "receipt" ( ESC/POS ) atau "label"
    |
    */

    'default' => env('PRINTER_TYPE', 'receipt'),

    /*
    |--------------------------------------------------------------------------
    | Receipt Settings
    |--------------------------------------------------------------------------
    |
    | Pengaturan untuk printer struk (ESC/POS)
    |
    */

    'receipt' => [
        'paper_width' => env('PRINTER_PAPER_WIDTH', 58), // 58mm atau 80mm
        'encoding' => env('PRINTER_ENCODING', 'CP437'),
        'cut' => env('PRINTER_CUT', true),
        'beep' => env('PRINTER_BEEP', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Label Settings
    |--------------------------------------------------------------------------
    |
    | Pengaturan untuk printer label
    |
    */

    'label' => [
        'width_mm' => env('PRINTER_LABEL_WIDTH', 40),  // mm
        'height_mm' => env('PRINTER_LABEL_HEIGHT', 30),  // mm
        'gap_mm' => env('PRINTER_LABEL_GAP', 2),      // mm
        'copies' => env('PRINTER_LABEL_COPIES', 1),
        'format' => env('PRINTER_LABEL_FORMAT', 'escpos'), // "escpos", "cpcl", "tspl"
    ],

    /*
    |--------------------------------------------------------------------------
    | Auto Connect
    |--------------------------------------------------------------------------
    |
    | Otomatis koneksi ke printer saat app dibuka
    |
    */

    'auto_connect' => env('PRINTER_AUTO_CONNECT', true),

    /*
    |--------------------------------------------------------------------------
    | Printer MAC Address (default)
    |--------------------------------------------------------------------------
    |
    | MAC address printer default, bisa di-override dari storage/app/printer.json
    |
    */

    'default_mac' => env('PRINTER_MAC', ''),

];
