<?php

// Regresi kontrak backup/andalan: respons kotor/retur/rewash wajib memuat
// rincian per-RFID (key, status_sync 1/0/2, status_transaksi,
// status_process, rfid, tanggal_update) — bukan cuma agregat.

use App\Enums\LinenStatusEnum;
use App\Enums\RegisterEnum;
use App\Enums\RsStatusEnum;
use App\Models\DetailLinen;
use App\Models\JenisBahan;
use App\Models\JenisLinen;
use App\Models\Rs;
use App\Models\Ruangan;
use App\Models\Supplier;
use App\Models\TransaksiMaster;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    expect(DB::connection()->getDatabaseName())->toBe('test_bka');

    $uniq = strtoupper(uniqid());
    $this->rs = Rs::create(['rs_nama' => 'RS Sync', 'rs_code' => 'SYN'.$uniq, 'rs_status' => RsStatusEnum::DEDICATED, 'rs_aktif' => 1]);
    $this->ruangan = Ruangan::create(['ruangan_nama' => 'Ruang Sync', 'ruangan_code' => 'SYN'.$uniq]);
    $this->jenis = JenisLinen::create(['jenis_nama' => 'Jenis Sync '.$uniq]);
    $this->bahan = JenisBahan::create(['bahan_nama' => 'Bahan Sync '.$uniq]);
    $this->supplier = Supplier::create(['supplier_nama' => 'Supplier Sync '.$uniq]);
    DB::table('rs_dan_ruangan')->insert(['rs_id' => $this->rs->rs_id, 'ruangan_id' => $this->ruangan->ruangan_id]);
    $this->admin = User::create(['name' => 'Admin Sync', 'email' => 'sync-'.$uniq.'@bka.test', 'password' => Hash::make('password'), 'role' => 'admin', 'verified_at' => now(), 'email_verified_at' => now()]);
    DB::table('rs_dan_user')->insert(['user_id' => $this->admin->id, 'rs_id' => $this->rs->rs_id]);
    $this->token = $this->admin->createToken('test-token')->plainTextToken;

    $this->rfidA = 'SYNC-A-'.$uniq;
    DetailLinen::create([
        'detail_rfid' => $this->rfidA, 'detail_id_rs' => $this->rs->rs_id, 'detail_id_ruangan' => $this->ruangan->ruangan_id,
        'detail_id_jenis' => $this->jenis->jenis_id, 'detail_id_bahan' => $this->bahan->bahan_id, 'detail_id_supplier' => $this->supplier->supplier_id,
        'detail_status_cuci' => 'CUCI', 'detail_status_kepemilikan' => RsStatusEnum::DEDICATED,
        'detail_status_register' => RegisterEnum::REGISTER, 'detail_status_linen' => LinenStatusEnum::BERSIH,
        'detail_tgl_cek' => now()->format('Y-m-d'),
    ]);
    // updated_at via query (fillable create mengabaikannya) — kotor butuh
    // selisih >= TRANSACTION_HOURS_ALLOWED dari update terakhir.
    DetailLinen::where('detail_rfid', $this->rfidA)->update(['detail_updated_at' => now()->subDays(2)]);
    $this->rfidUnknown = 'SYNC-X-'.$uniq;
});

it('kotor mengembalikan items per-RFID ala backup (sync=1, unknown=2)', function () {
    $res = $this->withToken($this->token)->postJson('/api/kotor', [
        'rfid' => [$this->rfidA, $this->rfidUnknown], 'rs_id' => $this->rs->rs_id, 'key' => 'SYNC-'.strtoupper(uniqid()), 'qty' => 10,
    ])->assertOk()->assertJson(['status' => true, 'code' => 200, 'name' => 'Create', 'message' => 'Data berhasil di buat']);

    // qty sejajar data: echo input apa adanya.
    expect($res->json('qty'))->toBe(10);

    $items = collect($res->json('data'))->keyBy('rfid')->all();
    expect($items[$this->rfidA]['status_sync'])->toBe(1)
        ->and($items[$this->rfidA]['status_transaksi'])->toBe('KOTOR')
        ->and($items[$this->rfidA]['status_process'])->toBe('SCAN')
        ->and($items[$this->rfidA]['key'])->not->toBeEmpty()
        ->and($items[$this->rfidUnknown]['status_sync'])->toBe(2)
        ->and($items[$this->rfidUnknown]['status_transaksi'])->toBe('UNKNOWN');

    // Agregat lama tidak ada — jumlah dari items (unknown dihitung sync=2 ala backup).
    expect(count($res->json('data')))->toBe(2)
        ->and(collect($res->json('data'))->where('status_sync', 1)->count())->toBe(1);
});

it('scan ulang hari yang sama menandai sync=0 dengan status outstanding', function () {
    $key = 'SYNC-'.strtoupper(uniqid());
    $this->withToken($this->token)->postJson('/api/kotor', [
        'rfid' => [$this->rfidA], 'rs_id' => $this->rs->rs_id, 'key' => $key,
    ])->assertOk();

    $res = $this->withToken($this->token)->postJson('/api/kotor', [
        'rfid' => [$this->rfidA], 'rs_id' => $this->rs->rs_id, 'key' => 'SYNC2-'.strtoupper(uniqid()),
    ])->assertOk()->assertJson(['status' => true]);

    $items = $res->json('data');
    expect(count($items))->toBe(1)
        ->and($items[0]['status_sync'])->toBe(0)
        ->and($items[0]['status_transaksi'])->toBe('KOTOR')
        ->and($items[0]['rfid'])->toBe($this->rfidA);
});

it('qty koma desimal diterima dan di-echo apa adanya', function () {
    $key = 'SYNCQ-'.strtoupper(uniqid());
    $res = $this->withToken($this->token)->postJson('/api/kotor', [
        'rfid' => [$this->rfidA], 'rs_id' => $this->rs->rs_id, 'key' => $key, 'qty' => '7,5',
    ])->assertOk()->assertJson(['status' => true]);

    expect($res->json('qty'))->toBe(7.5);
    expect((float) TransaksiMaster::where('master_kode', $key)->value('master_qty'))->toBe(7.5);
});

it('retur memakai status REJECT di items', function () {
    $res = $this->withToken($this->token)->postJson('/api/retur', [
        'rfid' => [$this->rfidA], 'rs_id' => $this->rs->rs_id, 'key' => 'SYNCR-'.strtoupper(uniqid()),
    ])->assertOk()->assertJson(['status' => true]);

    expect($res->json('data.0.status_transaksi'))->toBe('REJECT')
        ->and($res->json('data.0.status_sync'))->toBe(1)
        ->and(collect($res->json('data'))->where('status_sync', 1)->count())->toBe(1);
});
