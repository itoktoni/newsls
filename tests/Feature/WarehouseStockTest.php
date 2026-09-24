<?php

use App\Enums\CuciEnum;
use App\Enums\RsStatusEnum;
use App\Models\DetailLinen;
use App\Models\Rs;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

/**
 * Menu Warehouse = stok linen di gudang, bersumber dari tabel outstanding.
 * Baris outstanding apa pun (SCAN/QC/GUDANG/PACKING) masih ada di gudang;
 * baris baru hilang dari daftar ketika delivery menghapusnya.
 */
beforeEach(function () {
    expect(DB::connection()->getDatabaseName())->toBe('test_bka');

    $this->rs = Rs::create([
        'rs_nama' => 'RS Warehouse',
        'rs_code' => 'WHX',
        'rs_status' => RsStatusEnum::DEDICATED,
        'rs_aktif' => 1,
    ]);

    $this->admin = User::create([
        'name' => 'Admin Warehouse',
        'email' => 'admin-warehouse@bka.test',
        'password' => Hash::make('password'),
        'role' => 'admin',
        'verified_at' => now(),
        'email_verified_at' => now(),
    ]);

    DB::table('rs_dan_user')->insert(['user_id' => $this->admin->id, 'rs_id' => $this->rs->rs_id]);
    DB::table('warehouse')->updateOrInsert(['warehouse_id' => 1], ['warehouse_nama' => 'Gudang Utama']);
});

it('menampilkan baris outstanding apa pun sebagai stok gudang', function () {
    DetailLinen::create([
        'detail_rfid' => 'WHX-SCAN-1',
        'detail_id_rs' => $this->rs->rs_id,
        'detail_status_cuci' => CuciEnum::CUCI,
        'detail_status_kepemilikan' => RsStatusEnum::DEDICATED,
        'detail_status_linen' => 'REGISTER',
    ]);

    DB::table('outstanding')->insert([
        'outstanding_rfid' => 'WHX-SCAN-1',
        'outstanding_key' => 'WHX-KEY-1',
        'outstanding_rs_scan' => $this->rs->rs_id,
        'outstanding_status_transaksi' => 'KOTOR',
        'outstanding_status_proses' => 'SCAN',
        'outstanding_status_hilang' => 'NORMAL',
        'outstanding_created_at' => now(),
        'outstanding_updated_at' => now(),
    ]);

    $html = $this->actingAs($this->admin)->get('/warehouse/table')->assertOk()->getContent();

    expect($html)->toContain('WHX-SCAN-1');
});

it('tetap menampilkan baris outstanding yang belum ter-set gudang', function () {
    DetailLinen::create([
        'detail_rfid' => 'WHX-NULL-1',
        'detail_id_rs' => $this->rs->rs_id,
        'detail_status_cuci' => CuciEnum::CUCI,
        'detail_status_kepemilikan' => RsStatusEnum::DEDICATED,
        'detail_status_linen' => 'REGISTER',
    ]);

    DB::table('outstanding')->insert([
        'outstanding_rfid' => 'WHX-NULL-1',
        'outstanding_key' => 'WHX-KEY-2',
        'outstanding_rs_scan' => $this->rs->rs_id,
        'outstanding_id_warehouse' => null,
        'outstanding_status_transaksi' => 'KOTOR',
        'outstanding_status_proses' => 'SCAN',
        'outstanding_status_hilang' => 'NORMAL',
        'outstanding_created_at' => now(),
        'outstanding_updated_at' => now(),
    ]);

    $html = $this->actingAs($this->admin)->get('/warehouse/table')->assertOk()->getContent();

    expect($html)->toContain('WHX-NULL-1');
});
