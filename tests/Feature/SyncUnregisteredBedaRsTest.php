<?php

// Bukti perilaku sync kotor/retur/rewash untuk 2 kasus pertanyaan:
// 1) RFID BELUM terdaftar (tanpa baris detail_linen) → tetap masuk tabel
//    transaksi + outstanding, ditandai BELUM_REGISTER.
// 2) RFID milik RS LAIN (beda RS) → tetap masuk transaksi + outstanding,
//    ditandai beda_rs = YA dengan rs_ori = pemilik, rs_scan = RS pemindai.

use App\Enums\LinenStatusEnum;
use App\Enums\RegisterEnum;
use App\Enums\RsStatusEnum;
use App\Models\DetailLinen;
use App\Models\JenisBahan;
use App\Models\JenisLinen;
use App\Models\Outstanding;
use App\Models\Rs;
use App\Models\Ruangan;
use App\Models\Supplier;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    expect(DB::connection()->getDatabaseName())->toBe('test_bka');

    $uniq = strtoupper(uniqid());

    // RS A = pemilik linen, RS B = tempat scan (beda RS).
    $this->rsA = Rs::create(['rs_nama' => 'RS Own '.$uniq, 'rs_code' => 'OW'.$uniq, 'rs_status' => RsStatusEnum::DEDICATED, 'rs_aktif' => 1]);
    $this->rsB = Rs::create(['rs_nama' => 'RS Scan '.$uniq, 'rs_code' => 'SC'.$uniq, 'rs_status' => RsStatusEnum::DEDICATED, 'rs_aktif' => 1]);
    $this->ruangan = Ruangan::create(['ruangan_nama' => 'Ruang Sync '.$uniq, 'ruangan_code' => 'SR'.$uniq]);
    $this->jenis = JenisLinen::create(['jenis_nama' => 'Jenis Sync '.$uniq]);
    $this->bahan = JenisBahan::create(['bahan_nama' => 'Bahan Sync '.$uniq]);
    $this->supplier = Supplier::create(['supplier_nama' => 'Supplier Sync '.$uniq]);
    DB::table('rs_dan_ruangan')->insert(['rs_id' => $this->rsA->rs_id, 'ruangan_id' => $this->ruangan->ruangan_id]);

    $this->admin = User::create([
        'name' => 'Admin Sync', 'email' => 'sync-'.$uniq.'@bka.test',
        'password' => Hash::make('password'), 'role' => 'admin',
        'verified_at' => now(), 'email_verified_at' => now(),
    ]);
    DB::table('rs_dan_user')->insert(['user_id' => $this->admin->id, 'rs_id' => $this->rsA->rs_id]);
    DB::table('rs_dan_user')->insert(['user_id' => $this->admin->id, 'rs_id' => $this->rsB->rs_id]);
    $this->token = $this->admin->createToken('test-token')->plainTextToken;

    // RFID terdaftar milik RS A, masih BERSIH + sudah lewat masa jeda kotor.
    $this->rfidOwned = 'OWN-'.$uniq;
    DetailLinen::create([
        'detail_rfid' => $this->rfidOwned, 'detail_id_rs' => $this->rsA->rs_id, 'detail_id_ruangan' => $this->ruangan->ruangan_id,
        'detail_id_jenis' => $this->jenis->jenis_id, 'detail_id_bahan' => $this->bahan->bahan_id, 'detail_id_supplier' => $this->supplier->supplier_id,
        'detail_status_cuci' => 'CUCI', 'detail_status_kepemilikan' => RsStatusEnum::DEDICATED,
        'detail_status_register' => RegisterEnum::REGISTER, 'detail_status_linen' => LinenStatusEnum::BERSIH,
        'detail_tgl_cek' => now()->format('Y-m-d'),
    ]);
    DetailLinen::where('detail_rfid', $this->rfidOwned)->update(['detail_updated_at' => now()->subDays(2)]);

    // RFID yang sama sekali belum terdaftar.
    $this->rfidUnreg = 'UNREG-'.$uniq;
});

it('rfid belum terdaftar tetap masuk transaksi + outstanding (BELUM_REGISTER)', function () {
    $key = 'K-'.strtoupper(uniqid());
    $res = $this->withToken($this->token)->postJson('/api/kotor', [
        'rfid' => [$this->rfidUnreg], 'rs_id' => $this->rsB->rs_id, 'key' => $key,
    ])->assertOk();

    // Response: RFID asing = status_sync 2 (kontrak backup).
    expect($res->json('data.0.status_sync'))->toBe(2);

    // Tabel transaksi: baris TETAP dibuat.
    $trx = Transaksi::where('transaksi_rfid', $this->rfidUnreg)->first();
    expect($trx)->not->toBeNull()
        ->and($trx->transaksi_beda_rs)->toBe('BELUM_REGISTER')
        ->and($trx->transaksi_rs_ori)->toBeNull()
        ->and($trx->transaksi_rs_scan)->toBe($this->rsB->rs_id)
        ->and($trx->transaksi_status)->toBe('KOTOR')
        ->and($trx->transaksi_key)->toBe($key);

    // Tabel outstanding: baris TETAP dibuat.
    $out = Outstanding::where('outstanding_rfid', $this->rfidUnreg)->first();
    expect($out)->not->toBeNull()
        ->and($out->outstanding_status_beda_rs)->toBe('YA')
        ->and($out->outstanding_rs_ori)->toBeNull()
        ->and($out->outstanding_rs_scan)->toBe($this->rsB->rs_id)
        ->and($out->outstanding_status_transaksi)->toBe('KOTOR')
        ->and($out->outstanding_status_proses)->toBe('SCAN');

    // Dan tidak membuat detail_linen (belum terdaftar tetap belum terdaftar).
    expect(DetailLinen::where('detail_rfid', $this->rfidUnreg)->exists())->toBeFalse();
});

it('rfid milik rs lain tetap masuk transaksi + outstanding (beda_rs = YA)', function () {
    $key = 'K-'.strtoupper(uniqid());
    $res = $this->withToken($this->token)->postJson('/api/kotor', [
        'rfid' => [$this->rfidOwned], 'rs_id' => $this->rsB->rs_id, 'key' => $key, // scan di RS B, milik RS A
    ])->assertOk();

    expect($res->json('data.0.status_sync'))->toBe(1);

    // Tabel transaksi: dibuat dengan jejak kepemilikan.
    $trx = Transaksi::where('transaksi_rfid', $this->rfidOwned)->first();
    expect($trx)->not->toBeNull()
        ->and($trx->transaksi_beda_rs)->toBe('YA')
        ->and($trx->transaksi_rs_ori)->toBe($this->rsA->rs_id)
        ->and($trx->transaksi_rs_scan)->toBe($this->rsB->rs_id)
        ->and($trx->transaksi_status)->toBe('KOTOR');

    // Tabel outstanding: dibuat dengan jejak kepemilikan.
    $out = Outstanding::where('outstanding_rfid', $this->rfidOwned)->first();
    expect($out)->not->toBeNull()
        ->and($out->outstanding_status_beda_rs)->toBe('YA')
        ->and($out->outstanding_rs_ori)->toBe($this->rsA->rs_id)
        ->and($out->outstanding_rs_scan)->toBe($this->rsB->rs_id)
        ->and($out->outstanding_status_transaksi)->toBe('KOTOR')
        ->and($out->outstanding_status_proses)->toBe('SCAN');

    // Linen ikut ditandai KOTOR seperti scan biasa.
    expect(DetailLinen::where('detail_rfid', $this->rfidOwned)->value('detail_status_linen'))->toBe('KOTOR');
});
