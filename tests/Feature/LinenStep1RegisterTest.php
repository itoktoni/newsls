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
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// step1 PERSIST — jangan RefreshDatabase (biar test_bka tidak di-truncate habis test, bisa dicek manual)
// migrate:fresh + seed manual di beforeEach, tidak ada rollback setelah it()
beforeEach(function () {
    expect(DB::connection()->getDatabaseName())->toBe('test_bka');
    Artisan::call('migrate:fresh', ['--database' => 'mariadb', '--force' => true]);
    $this->rs = Rs::create(['rs_nama' => 'RS Step1', 'rs_code' => 'STP1', 'rs_status' => RsStatusEnum::DEDICATED, 'rs_aktif' => 1]);
    $this->ruangan = Ruangan::create(['ruangan_nama' => 'Ruang Dahlia', 'ruangan_code' => 'DHL']);
    $this->jenis = JenisLinen::create(['jenis_nama' => 'Seprai Single']);
    $this->bahan = JenisBahan::create(['bahan_nama' => 'Katun']);
    $this->supplier = Supplier::create(['supplier_nama' => 'Supplier Test']);
    DB::table('rs_dan_ruangan')->insert(['rs_id' => $this->rs->rs_id, 'ruangan_id' => $this->ruangan->ruangan_id]);
    DB::table('rs_dan_jenis')->insert(['rs_id' => $this->rs->rs_id, 'jenis_id' => $this->jenis->jenis_id, 'parstock' => 10]);
    DB::table('warehouse')->updateOrInsert(['warehouse_id' => 1], ['warehouse_nama' => 'Gudang Utama']);
    $this->admin = User::create(['name' => 'Admin Step1', 'email' => 'step1@bka.test', 'password' => Hash::make('password'), 'role' => 'admin', 'verified_at' => now(), 'email_verified_at' => now()]);
    DB::table('rs_dan_user')->insert(['user_id' => $this->admin->id, 'rs_id' => $this->rs->rs_id]);
    $this->token = $this->admin->createToken('test-token')->plainTextToken;
});

/**
 * STEP 1 — REGISTER (api) → cek data linen → cek report → packing & delivery bersih (api) → status BERSIH
 * Sesuai AGENTS web: register via POST /api/register, transaksi viewer read-only, packing/delivery via API
 */
it('step1 register via api, cek data linen, cek report, packing & delivery bersih via api', function () {
    $rfids = ['STEP1_1', 'STEP1_2', 'STEP1_3'];

    // 1) Register via API
    $this->withToken($this->token)->postJson('/api/register', [
        'rfid' => $rfids,
        'jenis_id' => $this->jenis->jenis_id,
        'bahan_id' => $this->bahan->bahan_id,
        'supplier_id' => $this->supplier->supplier_id,
        'status_cuci' => CuciEnum::CUCI,
        'rs_id' => $this->rs->rs_id,
        'ruangan_id' => $this->ruangan->ruangan_id,
        'status_register' => RegisterEnum::REGISTER,
        'status_kepemilikan' => RsStatusEnum::DEDICATED,
        'tgl_cek' => now()->format('Y-m-d'),
    ])->assertOk()->assertJson(['status' => true]);

    // 2) Cek data linen — DetailLinen & config_linen (master)
    foreach ($rfids as $rfid) {
        $d = DetailLinen::where('detail_rfid', $rfid)->firstOrFail();
        expect($d->detail_status_linen)->toBe(LinenStatusEnum::REGISTER);
        expect($d->detail_id_rs)->toBe($this->rs->rs_id);
        expect(DB::table('config_linen')->where('detail_rfid', $rfid)->exists())->toBeTrue();
        expect($d->hasJenis->jenis_nama)->toBe('Seprai Single');
    }
    expect(DetailLinen::whereIn('detail_rfid', $rfids)->count())->toBe(3);

    // 3) Cek report data linen & register (web viewer)
    $this->actingAs($this->admin)->get('/report-data-linen/table?rs_id='.$this->rs->rs_id)->assertOk();
    $this->actingAs($this->admin)->get('/report-register-linen/table?rs_id='.$this->rs->rs_id)->assertOk();
    // report count via DB
    expect(DetailLinen::where('detail_id_rs', $this->rs->rs_id)->count())->toBe(3);

    // 4) Grouping → packing → delivery bersih via API (jadi BERSIH)
    foreach ($rfids as $rfid) {
        $this->withToken($this->token)->getJson("/api/grouping/{$rfid}")->assertOk();
    }

    foreach ($rfids as $rfid) {
        expect(DetailLinen::where('detail_rfid', $rfid)->value('detail_status_linen'))->toBe(LinenStatusEnum::GUDANG);
    }

    $this->withToken($this->token)->postJson('/api/packing', [
        'rfid' => $rfids, 'rs_id' => $this->rs->rs_id, 'ruangan_id' => $this->ruangan->ruangan_id, 'status_transaksi' => 'REGISTER',
    ])->assertOk()->assertJson(['status' => true]);
    expect(DB::table('outstanding')->whereIn('outstanding_rfid', $rfids)->where('outstanding_status_proses', 'PACKING')->count())->toBe(3);
    expect(DB::table('cetak')->where('cetak_type', 1)->count())->toBe(1);
    $packCode = DB::table('cetak')->where('cetak_type', 1)->value('cetak_code');
    expect($packCode)->toBe(strtoupper($packCode)); // barcode capital semua

    $this->withToken($this->token)->postJson('/api/delivery', [
        'rs_id' => $this->rs->rs_id, 'status_transaksi' => 'REGISTER',
    ])->assertOk()->assertJson(['status' => true]);

    // 5) Final status BERSIH, report pengiriman & bersih terisi
    foreach ($rfids as $rfid) {
        expect(DetailLinen::where('detail_rfid', $rfid)->value('detail_status_linen'))->toBe(LinenStatusEnum::BERSIH);
        expect((int) DetailLinen::where('detail_rfid', $rfid)->value('detail_total_bersih'))->toBe(1);
    }
    expect(DB::table('outstanding')->whereIn('outstanding_rfid', $rfids)->count())->toBe(0);
    expect(DB::table('cetak')->where('cetak_type', 2)->count())->toBe(1);
    expect(DB::table('bersih')->whereIn('bersih_rfid', $rfids)->count())->toBe(3);
    expect(DB::table('bersih')->where('bersih_rfid', $rfids[0])->value('bersih_barcode'))->toBe(strtoupper(DB::table('bersih')->where('bersih_rfid', $rfids[0])->value('bersih_barcode')));
    expect(DB::table('cetak')->where('cetak_type', 2)->value('cetak_code'))->toBe(strtoupper(DB::table('cetak')->where('cetak_type', 2)->value('cetak_code')));

    $this->actingAs($this->admin)->get('/report-rekap-bersih/table?rs_id='.$this->rs->rs_id)->assertOk();
    $this->actingAs($this->admin)->get('/report-detail-pengiriman-bersih/table?rs_id='.$this->rs->rs_id)->assertOk();
    $this->actingAs($this->admin)->get('/report-summary-pengiriman-bersih/table?rs_id='.$this->rs->rs_id)->assertOk();
    $this->actingAs($this->admin)->get('/bersih/table?tab=delivery')->assertOk();
});
