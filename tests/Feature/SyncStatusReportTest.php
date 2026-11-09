<?php

// Uji integrasi penuh sesuai permintaan: sync kotor/retur/rewash via API,
// pastikan outstanding_status + transaksi_status sesuai, lalu pastikan
// report rekap + detail masing-masing menampilkannya.

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
    $this->rs = Rs::create(['rs_nama' => 'RS SyncRep '.$uniq, 'rs_code' => 'SRP'.$uniq, 'rs_status' => RsStatusEnum::DEDICATED, 'rs_aktif' => 1]);
    $this->ruangan = Ruangan::create(['ruangan_nama' => 'Ruang SyncRep '.$uniq, 'ruangan_code' => 'SRP'.$uniq]);
    $this->jenis = JenisLinen::create(['jenis_nama' => 'Jenis SyncRep '.$uniq, 'jenis_berat' => 0.5]);
    $this->bahan = JenisBahan::create(['bahan_nama' => 'Bahan SyncRep '.$uniq]);
    $this->supplier = Supplier::create(['supplier_nama' => 'Supplier SyncRep '.$uniq]);
    DB::table('rs_dan_ruangan')->insert(['rs_id' => $this->rs->rs_id, 'ruangan_id' => $this->ruangan->ruangan_id]);
    $this->admin = User::create(['name' => 'Admin SyncRep', 'email' => 'syncrep-'.$uniq.'@bka.test', 'password' => Hash::make('password'), 'role' => 'admin', 'verified_at' => now(), 'email_verified_at' => now()]);
    $this->token = $this->admin->createToken('test-token')->plainTextToken;

    // Satu RFID BERSIH per tipe scan (dedup harian = 1 transaksi per RFID).
    $this->cases = [
        'kotor' => ['endpoint' => 'kotor', 'trx' => 'KOTOR', 'rfid' => 'SYNK-'.$uniq],
        'retur' => ['endpoint' => 'retur', 'trx' => 'REJECT', 'rfid' => 'SYNR-'.$uniq],
        'rewash' => ['endpoint' => 'rewash', 'trx' => 'REWASH', 'rfid' => 'SYNW-'.$uniq],
    ];
    foreach ($this->cases as $c) {
        DetailLinen::create([
            'detail_rfid' => $c['rfid'], 'detail_id_rs' => $this->rs->rs_id, 'detail_id_ruangan' => $this->ruangan->ruangan_id,
            'detail_id_jenis' => $this->jenis->jenis_id, 'detail_id_bahan' => $this->bahan->bahan_id, 'detail_id_supplier' => $this->supplier->supplier_id,
            'detail_status_cuci' => 'CUCI', 'detail_status_kepemilikan' => RsStatusEnum::DEDICATED,
            'detail_status_register' => RegisterEnum::REGISTER, 'detail_status_linen' => LinenStatusEnum::BERSIH,
            'detail_created_by' => $this->admin->id,
        ]);
        DB::table('config_linen')->insert(['detail_rfid' => $c['rfid'], 'rs_id' => $this->rs->rs_id]);
    }
    // Kotor butuh selisih >= TRANSACTION_HOURS_ALLOWED dari update terakhir.
    DetailLinen::where('detail_rfid', $this->cases['kotor']['rfid'])->update(['detail_updated_at' => now()->subDays(2)]);
});

it('sync kotor/retur/rewash mengisi status transaksi+outstanding yang benar dan muncul di report', function () {
    foreach ($this->cases as $name => $c) {
        $res = $this->withToken($this->token)->postJson('/api/'.$c['endpoint'], [
            'rfid' => [$c['rfid']], 'rs_id' => $this->rs->rs_id, 'key' => 'SYN-'.$name.'-'.strtoupper(uniqid()),
        ])->assertOk()->assertJson(['status' => true]);

        // 1) Respons sync sukses.
        expect($res->json('data.0.status_sync'))->toBe(1)
            ->and($res->json('data.0.status_transaksi'))->toBe($c['trx'])
            ->and($res->json('data.0.status_process'))->toBe('SCAN');

        // 2) Baris transaksi sesuai tipe.
        $trx = Transaksi::where('transaksi_rfid', $c['rfid'])->firstOrFail();
        expect($trx->transaksi_status)->toBe($c['trx'])
            ->and((int) $trx->transaksi_rs_scan)->toBe($this->rs->rs_id);

        // 3) Outstanding sesuai tipe + SCAN, detail jadi KOTOR.
        $out = Outstanding::where('outstanding_rfid', $c['rfid'])->firstOrFail();
        expect($out->outstanding_status_transaksi)->toBe($c['trx'])
            ->and($out->outstanding_status_proses)->toBe('SCAN');
        expect(DetailLinen::where('detail_rfid', $c['rfid'])->value('detail_status_linen'))->toBe(LinenStatusEnum::KOTOR);
    }

    // 4) Report rekap + detail tiap tipe menampilkan datanya.
    $slugs = ['kotor' => 'kotor', 'retur' => 'retur', 'rewash' => 'rewash'];
    foreach ($slugs as $name => $slug) {
        $rfid = $this->cases[$name]['rfid'];

        $this->actingAs($this->admin)
            ->get("report-rekap-{$slug}/print?rs_id=".$this->rs->rs_id)
            ->assertOk()
            ->assertSee($this->jenis->jenis_nama, false);

        $html = $this->actingAs($this->admin)
            ->get("report-detail-{$slug}/print?rs_id=".$this->rs->rs_id)
            ->assertOk()->getContent();
        expect(substr_count($html, $rfid))->toBe(1);
    }
});
