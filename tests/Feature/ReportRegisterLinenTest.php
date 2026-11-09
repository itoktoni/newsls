<?php

use App\Enums\CuciEnum;
use App\Enums\LinenStatusEnum;
use App\Enums\RegisterEnum;
use App\Enums\RsStatusEnum;
use App\Models\DetailLinen;
use App\Models\JenisBahan;
use App\Models\JenisLinen;
use App\Models\Rs;
use App\Models\Ruangan;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    expect(DB::connection()->getDatabaseName())->toBe('test_bka');

    $uniq = strtoupper(uniqid());
    $this->rsA = Rs::create(['rs_nama' => 'RS RPT A '.$uniq, 'rs_code' => 'RPTA'.$uniq, 'rs_status' => RsStatusEnum::DEDICATED, 'rs_aktif' => 1]);
    $this->rsB = Rs::create(['rs_nama' => 'RS RPT B '.$uniq, 'rs_code' => 'RPTB'.$uniq, 'rs_status' => RsStatusEnum::DEDICATED, 'rs_aktif' => 1]);
    $this->rsGlobal = Rs::create(['rs_nama' => 'Global BAC '.$uniq, 'rs_code' => 'BAC'.$uniq, 'rs_status' => RsStatusEnum::FREE, 'rs_aktif' => 1]);
    $this->ruangan = Ruangan::create(['ruangan_nama' => 'Ruang RPT '.$uniq, 'ruangan_code' => 'RPT'.$uniq]);
    $this->jenis = JenisLinen::create(['jenis_nama' => 'Seprai RPT '.$uniq]);
    $this->bahan = JenisBahan::create(['bahan_nama' => 'Katun RPT '.$uniq]);
    $this->supplier = Supplier::create(['supplier_nama' => 'Supplier RPT '.$uniq]);
    $this->admin = User::create(['name' => 'Admin RPT', 'email' => 'rpt-'.$uniq.'@bka.test', 'password' => Hash::make('password'), 'role' => 'admin', 'verified_at' => now(), 'email_verified_at' => now()]);

    $base = [
        'detail_id_jenis' => $this->jenis->jenis_id, 'detail_id_bahan' => $this->bahan->bahan_id,
        'detail_id_supplier' => $this->supplier->supplier_id, 'detail_status_cuci' => CuciEnum::CUCI,
        'detail_status_register' => RegisterEnum::REGISTER, 'detail_status_linen' => LinenStatusEnum::REGISTER,
        'detail_created_by' => $this->admin->id,
    ];

    // FREE (linen global): holder Global, nol baris config.
    $this->freeRfid = 'RPTFREE-'.$uniq;
    DetailLinen::create($base + ['detail_rfid' => $this->freeRfid, 'detail_id_rs' => $this->rsGlobal->rs_id, 'detail_id_ruangan' => $this->ruangan->ruangan_id, 'detail_status_kepemilikan' => RsStatusEnum::FREE]);

    // GROUP milik A + B (linen global): holder Global, 2 baris config.
    $this->groupRfid = 'RPTGRP-'.$uniq;
    DetailLinen::create($base + ['detail_rfid' => $this->groupRfid, 'detail_id_rs' => $this->rsGlobal->rs_id, 'detail_id_ruangan' => $this->ruangan->ruangan_id, 'detail_status_kepemilikan' => RsStatusEnum::GROUP]);
    DB::table('config_linen')->insert([
        ['detail_rfid' => $this->groupRfid, 'rs_id' => $this->rsA->rs_id],
        ['detail_rfid' => $this->groupRfid, 'rs_id' => $this->rsB->rs_id],
    ]);

    // DEDICATED milik + dipegang A.
    $this->dedRfid = 'RPTDED-'.$uniq;
    DetailLinen::create($base + ['detail_rfid' => $this->dedRfid, 'detail_id_rs' => $this->rsA->rs_id, 'detail_id_ruangan' => $this->ruangan->ruangan_id, 'detail_status_kepemilikan' => RsStatusEnum::DEDICATED]);
    DB::table('config_linen')->insert(['detail_rfid' => $this->dedRfid, 'rs_id' => $this->rsA->rs_id]);
});

function rptPrintHtml($test, array $params = []): string
{
    $res = $test->actingAs($test->admin)->get('report-register-linen/print?'.http_build_query($params));
    $res->assertOk();

    return $res->getContent();
}

/**
 * Tanpa filter: tiap RFID tepat 1 baris (linen GROUP tidak ganda),
 * kolom RS = daftar pemilik ("A, B") atau pemegang untuk FREE.
 */
it('report register tanpa filter menampilkan tiap RFID tepat satu baris', function () {
    $html = rptPrintHtml($this);

    expect(substr_count($html, $this->freeRfid))->toBe(1);
    expect(substr_count($html, $this->groupRfid))->toBe(1);
    expect(substr_count($html, $this->dedRfid))->toBe(1);

    // GROUP: satu baris berisi kedua pemilik, bukan dua baris per RS.
    expect($html)->toContain($this->rsA->rs_nama.', '.$this->rsB->rs_nama);
});

/**
 * Filter RS = A: GROUP ikut (satu baris, daftar pemilik), FREE global
 * tidak ikut, dan tidak ada baris pemilik tunggal B yang nyasar.
 */
it('report register filter RS hanya menampilkan baris milik atau dipegang RS itu', function () {
    $html = rptPrintHtml($this, ['rs_id' => $this->rsA->rs_id]);

    expect(substr_count($html, $this->groupRfid))->toBe(1);
    expect(substr_count($html, $this->dedRfid))->toBe(1);
    expect($html)->not->toContain($this->freeRfid);

    // Baris GROUP menampilkan daftar pemilik, bukan RS tunggal B.
    expect($html)->toContain($this->rsA->rs_nama.', '.$this->rsB->rs_nama);
    expect(substr_count($html, $this->rsB->rs_nama))->toBe(1);
});

/**
 * Filter RS = Global: GROUP tetap satu baris (daftar pemilik "A, B"),
 * FREE tampil sebagai pemegang Global — bukan baris RS tunggal lain.
 */
it('report register filter RS global tidak menampilkan RS lain sebagai pemilik tunggal', function () {
    $html = rptPrintHtml($this, ['rs_id' => $this->rsGlobal->rs_id]);

    expect(substr_count($html, $this->freeRfid))->toBe(1);
    expect(substr_count($html, $this->groupRfid))->toBe(1);
    expect(substr_count($html, $this->dedRfid))->toBe(0);

    expect($html)->toContain($this->rsGlobal->rs_nama);
    expect($html)->toContain($this->rsA->rs_nama.', '.$this->rsB->rs_nama);
});
