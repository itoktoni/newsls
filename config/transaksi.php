<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Bypass linen FREE (detail_linen.detail_id_rs = 1)
    |--------------------------------------------------------------------------
    |
    | Bila true, RFID terdaftar dengan detail_id_rs = 1 yang di-scan RS lain
    | via kotor/retur/rewash (+ alias transaksi/*) dianggap benar:
    | status_sync = 1, rs_ori diisi rs_scan, dan detail_id_rs diupdate
    | ke rs_scan. Default mati (perilaku lama).
    |
    | Dibaca via config() agar tetap jalan saat config di-cache
    | (env() langsung di controller = null bila config:cache aktif).
    |
    */

    'bypass_free_transaction' => filter_var(
        env('BYPASS_FREE_TRANSACTION', false),
        FILTER_VALIDATE_BOOLEAN
    ),

];
