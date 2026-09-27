<?php

// Peta otorisasi modul → role yang BOLEH (allowlist).
//
// - Key = prefix nama route SEBELUM titik (mis. 'user' untuk user.getTable,
//   'users' untuk users.postCreate). Route tanpa titik = nama penuhnya.
// - Modul yang TIDAK terdaftar = bebas (perilaku lama: ikut logika per-method
//   di BasePolicy, yang praktisnya allow).
// - 'user'  = web /user/* (Route::auto name 'user.*').
// - 'users' = api /api/users/* (name 'users.*') — tidak dipakai desktop/mobile,
//   hanya admin tooling bertoken. Lihat komentar di routes/api.php.
//
// Nilai role mengacu ke App\Enums\RoleEnum (ditulis string agar aman di-cache
// via php artisan config:cache — konstanta Enum tidak bisa di-var_export).
return [
    'user' => ['admin', 'developer'],
    'users' => ['admin', 'developer'],
];
