<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Berat Kalkulasi
    |--------------------------------------------------------------------------
    |
    | Jika false, semua total berat yang bersumber dari table jenis_linen
    | (field jenis_berat) beserta hasil perkaliannya disembunyikan dari
    | report kotor (rekap + detail, print + excel).
    | Set via .env: BERAT_KALKULASI=false
    |
    */

    'berat_kalkulasi' => (bool) env('BERAT_KALKULASI', true),

];
