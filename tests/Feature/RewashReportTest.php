<?php

// Regresi: scan rewash via POST /api/rewash wajib tercatat di transaksi
// (status REWASH) dan terbaca di web report rekap + detail rewash.

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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    expect(DB::connection()->getDatabaseName())->toBe('test_bka');

    $uniq = strtoupper(uniqid());
    $this->rs = Rs::create(['rs_nama' => 'RS Rewash '.$uniq, 'rs_code' => 'RWS'.$uniq, 'rs_status' => RsStatusEnum::DEDICATED, 'rs_aktif' => 1]);
    $this->ruangan = Ruangan::create(['ruangan_nama' => 'Ruang Rewash '.$uniq, 'ruangan_code' => 'RWS'.$uniq]);
    $this->jenis = JenisLinen::create(['jenis_nama' => 'Jenis Rewash '.$uniq, 'jenis_berat' => 0.4]);
    $this->bahan = JenisBahan::create(['bahan_nama' => 'Bahan Rewash '.$uniq]);
    $this->supplier = Supplier::create(['supplier_nama' => 'Supplier Rewash '.$uniq]);
    DB::table('rs_dan_ruangan')->insert(['rs_id' => $this->rs->rs_id, 'ruangan_id' => $this->ruangan->ruangan_id]);
    $this->admin = User::create(['name' => 'Admin Rewash', 'email' => 'rewash-'.$uniq.'@bka.test', 'password' => Hash::make('password'), 'role' => 'admin', 'verified_at' => now(), 'email_verified_at' => now()]);
    $this->token = $this->admin->createToken('test-token')->plainTextToken;

    $this->rfids = ['RWS-1-'.$uniq, 'RWS-2-'.$uniq];
    foreach ($this->rfids as $rfid) {
        DetailLinen::create([
            'detail_rfid' => $rfid, 'detail_id_rs' => $this->rs->rs_id, 'detail_id_ruangan' => $this->ruangan->ruangan_id,
            'detail_id_jenis' => $this->jenis->jenis_id, 'detail_id_bahan' => $this->bahan->bahan_id, 'detail_id_supplier' => $this->supplier->supplier_id,
            'detail_status_cuci' => 'CUCI', 'detail_status_kepemilikan' => RsStatusEnum::DEDICATED,
            'detail_status_register' => RegisterEnum::REGISTER, 'detail_status_linen' => LinenStatusEnum::BERSIH,
            'detail_created_by' => $this->admin->id,
        ]);
    }
    DB::table('config_linen')->insert(array_map(
        fn ($rfid) => ['detail_rfid' => $rfid, 'rs_id' => $this->rs->rs_id],
        $this->rfids
    ));
});

it('scan rewash via api tercatat dan tampil di report rekap + detail rewash', function () {
    $res = $this->withToken($this->token)->postJson('/api/rewash', [
        'rfid' => $this->rfids, 'rs_id' => $this->rs->rs_id, 'key' => 'RWS-'.strtoupper(uniqid()),
    ])->assertOk()->assertJson(['status' => true]);

    $items = collect($res->json('data'))->keyBy('rfid')->all();
    foreach ($this->rfids as $rfid) {
        expect($items[$rfid]['status_sync'])->toBe(1);
        expect($items[$rfid]['status_transaksi'])->toBe('REWASH');
    }

    expect(DB::table('transaksi')->whereIn('transaksi_rfid', $this->rfids)->where('transaksi_status', 'REWASH')->count())->toBe(2);

    // andalan: linen lolos scan rewash ditandai KOTOR.
    foreach ($this->rfids as $rfid) {
        expect(DetailLinen::where('detail_rfid', $rfid)->value('detail_status_linen'))->toBe('KOTOR');
    }

    // Rekap: matriks berisi jenis linen (1 baris, total 2 pcs).
    $this->actingAs($this->admin)
        ->get('report-rekap-rewash/print?rs_id='.$this->rs->rs_id)
        ->assertOk()
        ->assertSee($this->jenis->jenis_nama, false)
        ->assertSee('>2<', false);

    // Detail: kedua RFID tampil.
    $html = $this->actingAs($this->admin)
        ->get('report-detail-rewash/print?rs_id='.$this->rs->rs_id)
        ->assertOk()->getContent();
    foreach ($this->rfids as $rfid) {
        expect(substr_count($html, $rfid))->toBe(1);
    }
});
