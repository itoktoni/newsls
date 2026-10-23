<?php

// Regresi kasus produksi: RFID ...75 ter-packing 2x (Kuningan + Cakung)
// tanpa delivery, lalu delivery beda RS lolos.
// - Packing ulang RFID berstatus PACKING wajib 422.
// - Delivery dengan rfid[] wajib cocok RS packing-nya, bila tidak 422.

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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    expect(DB::connection()->getDatabaseName())->toBe('test_bka');

    $uniq = strtoupper(uniqid());
    $this->rsA = Rs::create(['rs_nama' => 'RS Guard A', 'rs_code' => 'GRD'.$uniq.'A', 'rs_status' => RsStatusEnum::DEDICATED, 'rs_aktif' => 1]);
    $this->rsB = Rs::create(['rs_nama' => 'RS Guard B', 'rs_code' => 'GRD'.$uniq.'B', 'rs_status' => RsStatusEnum::DEDICATED, 'rs_aktif' => 1]);
    $this->ruangan = Ruangan::create(['ruangan_nama' => 'Ruang Guard', 'ruangan_code' => 'GRD'.$uniq]);
    $this->jenis = JenisLinen::create(['jenis_nama' => 'Jenis Guard '.$uniq]);
    $this->bahan = JenisBahan::create(['bahan_nama' => 'Bahan Guard '.$uniq]);
    $this->supplier = Supplier::create(['supplier_nama' => 'Supplier Guard '.$uniq]);
    foreach ([$this->rsA, $this->rsB] as $rs) {
        DB::table('rs_dan_ruangan')->insert(['rs_id' => $rs->rs_id, 'ruangan_id' => $this->ruangan->ruangan_id]);
        DB::table('rs_dan_jenis')->insert(['rs_id' => $rs->rs_id, 'jenis_id' => $this->jenis->jenis_id, 'parstock' => 10]);
    }
    DB::table('warehouse')->updateOrInsert(['warehouse_id' => 1], ['warehouse_nama' => 'Gudang Utama']);
    $this->admin = User::create(['name' => 'Admin Guard', 'email' => 'guard-'.$uniq.'@bka.test', 'password' => Hash::make('password'), 'role' => 'admin', 'verified_at' => now(), 'email_verified_at' => now()]);
    foreach ([$this->rsA, $this->rsB] as $rs) {
        DB::table('rs_dan_user')->insert(['user_id' => $this->admin->id, 'rs_id' => $rs->rs_id]);
    }
    $this->token = $this->admin->createToken('test-token')->plainTextToken;

    $this->rfid = 'GUARD-'.$uniq;
    DetailLinen::create([
        'detail_rfid' => $this->rfid, 'detail_id_rs' => $this->rsA->rs_id, 'detail_id_ruangan' => $this->ruangan->ruangan_id,
        'detail_id_jenis' => $this->jenis->jenis_id, 'detail_id_bahan' => $this->bahan->bahan_id, 'detail_id_supplier' => $this->supplier->supplier_id,
        'detail_status_cuci' => CuciEnum::CUCI, 'detail_status_kepemilikan' => RsStatusEnum::DEDICATED,
        'detail_status_register' => RegisterEnum::REGISTER, 'detail_status_linen' => LinenStatusEnum::GUDANG,
        'detail_tgl_cek' => now()->format('Y-m-d'),
    ]);
    DB::table('config_linen')->insert(['detail_rfid' => $this->rfid, 'rs_id' => $this->rsA->rs_id]);
    DB::table('outstanding')->insert([
        'outstanding_key' => 'GRP-GUARD', 'outstanding_rfid' => $this->rfid,
        'outstanding_rs_ori' => $this->rsA->rs_id, 'outstanding_rs_scan' => $this->rsA->rs_id,
        'outstanding_id_ruangan' => $this->ruangan->ruangan_id, 'outstanding_id_warehouse' => 1,
        'outstanding_status_transaksi' => 'KOTOR', 'outstanding_status_proses' => 'GUDANG',
        'outstanding_status_hilang' => 'NORMAL', 'outstanding_created_at' => now(), 'outstanding_updated_at' => now(),
        'outstanding_created_by' => $this->admin->id, 'outstanding_updated_by' => $this->admin->id,
    ]);
});

it('menolak packing ulang RFID yang masih berstatus PACKING', function () {
    $this->withToken($this->token)->postJson('/api/packing', [
        'rfid' => [$this->rfid], 'rs_id' => $this->rsA->rs_id, 'ruangan_id' => $this->ruangan->ruangan_id, 'status_transaksi' => 'KOTOR',
    ])->assertOk()->assertJson(['status' => true]);

    $this->withToken($this->token)->postJson('/api/packing', [
        'rfid' => [$this->rfid], 'rs_id' => $this->rsB->rs_id, 'ruangan_id' => $this->ruangan->ruangan_id, 'status_transaksi' => 'KOTOR',
    ])->assertOk()->assertJson(['status' => false]);

    expect(DB::table('bersih')->where('bersih_rfid', $this->rfid)->count())->toBe(1);
});

it('menolak delivery beda RS bila rfid[] dikirim, dan menerima yang sesuai', function () {
    $this->withToken($this->token)->postJson('/api/packing', [
        'rfid' => [$this->rfid], 'rs_id' => $this->rsA->rs_id, 'ruangan_id' => $this->ruangan->ruangan_id, 'status_transaksi' => 'KOTOR',
    ])->assertOk()->assertJson(['status' => true]);

    // Packing RS-A, delivery RS-B + rfid -> 422, outstanding utuh.
    $this->withToken($this->token)->postJson('/api/delivery', [
        'rs_id' => $this->rsB->rs_id, 'status_transaksi' => 'KOTOR', 'rfid' => [$this->rfid],
    ])->assertOk()->assertJson(['status' => false]);
    expect(DB::table('outstanding')->where('outstanding_rfid', $this->rfid)->count())->toBe(1);

    // Delivery RS-A + rfid -> OK, outstanding habis.
    $this->withToken($this->token)->postJson('/api/delivery', [
        'rs_id' => $this->rsA->rs_id, 'status_transaksi' => 'KOTOR', 'rfid' => [$this->rfid],
    ])->assertOk()->assertJson(['status' => true]);
    expect(DB::table('outstanding')->where('outstanding_rfid', $this->rfid)->count())->toBe(0);
});
