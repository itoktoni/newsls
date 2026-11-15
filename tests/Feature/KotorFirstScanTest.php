<?php

use App\Enums\CuciEnum;
use App\Enums\LinenStatusEnum;
use App\Enums\RegisterEnum;
use App\Enums\RsStatusEnum;
use App\Models\DetailLinen;
use Illuminate\Support\Facades\DB;

// Aturan strict andalan (tanpa pengecualian): scan kotor hanya sync bila
// linen BERSIH + sudah lewat TRANSACTION_HOURS_ALLOWED jam. Linen fresh
// REGISTER / baru BERSIH (<15 jam) dilewati diam-diam (status_sync=0,
// tanpa baris transaksi) — dashboard + report kotor memang tidak berubah.
it('scan kotor linen fresh register dilewati mengikuti aturan strict andalan', function () {
    $admin = \App\Models\User::updateOrCreate(['email' => 'kotor-first@bka.test'], [
        'name' => 'Kotor First', 'password' => bcrypt('password'), 'role' => 'admin',
    ]);
    $rs = \App\Models\Rs::create(['rs_nama' => 'RS KF '.uniqid()]);
    $ruangan = \App\Models\Ruangan::create(['ruangan_nama' => 'R KF '.uniqid()]);
    $jenis = \App\Models\JenisLinen::create(['jenis_nama' => 'J KF '.uniqid(), 'jenis_berat' => 0.5]);
    $bahan = \App\Models\JenisBahan::create(['bahan_nama' => 'B KF '.uniqid()]);
    $sup = \App\Models\Supplier::create(['supplier_nama' => 'S KF '.uniqid()]);

    $make = function (string $rfid, string $linenStatus) use ($rs, $ruangan, $jenis, $bahan, $sup) {
        DetailLinen::create([
            'detail_rfid' => $rfid, 'detail_id_rs' => $rs->rs_id,
            'detail_id_ruangan' => $ruangan->ruangan_id, 'detail_id_jenis' => $jenis->jenis_id,
            'detail_id_bahan' => $bahan->bahan_id, 'detail_id_supplier' => $sup->supplier_id,
            'detail_status_cuci' => CuciEnum::CUCI, 'detail_status_kepemilikan' => RsStatusEnum::DEDICATED,
            'detail_status_register' => RegisterEnum::REGISTER, 'detail_status_linen' => $linenStatus,
        ]);
    };

    $token = $admin->createToken('api_token')->plainTextToken;

    // 1) Linen REGISTER fresh → scan kotor DILEWATI (sync 0, tanpa baris transaksi).
    $rfid = 'KF-'.strtoupper(uniqid());
    $make($rfid, LinenStatusEnum::REGISTER);
    $key = 'KF-'.strtoupper(uniqid());
    $res = $this->withToken($token)->postJson('/api/kotor', [
        'rfid' => [$rfid], 'rs_id' => $rs->rs_id, 'key' => $key,
    ])->assertOk();
    expect($res->json('data.0.status_sync'))->toBe(0)
        ->and(DB::table('transaksi')->where('transaksi_key', $key)->count())->toBe(0)
        ->and(DetailLinen::where('detail_rfid', $rfid)->value('detail_status_linen'))->toBe(LinenStatusEnum::REGISTER);

    // 2) Retur/rewash linen REGISTER juga ditolak.
    $rfid2 = 'KF-'.strtoupper(uniqid());
    $make($rfid2, LinenStatusEnum::REGISTER);
    $res2 = $this->withToken($token)->postJson('/api/retur', [
        'rfid' => [$rfid2], 'rs_id' => $rs->rs_id, 'key' => 'KF-'.strtoupper(uniqid()),
    ])->assertOk();
    expect($res2->json('data.0.status_sync'))->toBe(0);
});
