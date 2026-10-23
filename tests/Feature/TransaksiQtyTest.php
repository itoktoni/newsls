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

    $this->rs = Rs::updateOrCreate(['rs_code' => 'QTY_RS'], ['rs_nama' => 'RS Qty', 'rs_status' => RsStatusEnum::DEDICATED, 'rs_aktif' => 1]);
    $this->ruangan = Ruangan::firstOrCreate(['ruangan_code' => 'QTY'], ['ruangan_nama' => 'Ruang Qty']);
    $this->jenis = JenisLinen::firstOrCreate(['jenis_nama' => 'Seprai Single']);
    $this->bahan = JenisBahan::firstOrCreate(['bahan_nama' => 'Katun']);
    $this->supplier = Supplier::firstOrCreate(['supplier_nama' => 'Supplier Test']);
    DB::table('rs_dan_ruangan')->updateOrInsert(['rs_id' => $this->rs->rs_id, 'ruangan_id' => $this->ruangan->ruangan_id], []);
    DB::table('warehouse')->updateOrInsert(['warehouse_id' => 1], ['warehouse_nama' => 'Gudang Utama']);
    $this->admin = User::firstOrCreate(['email' => 'qty@bka.test'], ['name' => 'Admin Qty', 'password' => Hash::make('password'), 'role' => 'admin', 'verified_at' => now(), 'email_verified_at' => now()]);
    DB::table('rs_dan_user')->updateOrInsert(['user_id' => $this->admin->id, 'rs_id' => $this->rs->rs_id], []);
    $this->token = $this->admin->createToken('test-token-'.uniqid())->plainTextToken;

    // KOTOR butuh linen BERSIH + lewat TRANSACTION_HOURS_ALLOWED (default 15 jam)
    $old = now()->subDays(2)->format('Y-m-d H:i:s');
    $this->rfids = ['QTY_'.uniqid().'_1', 'QTY_'.uniqid().'_2'];
    foreach ($this->rfids as $rfid) {
        DetailLinen::updateOrCreate(['detail_rfid' => $rfid], [
            'detail_id_rs' => $this->rs->rs_id, 'detail_id_ruangan' => $this->ruangan->ruangan_id,
            'detail_id_jenis' => $this->jenis->jenis_id, 'detail_id_bahan' => $this->bahan->bahan_id, 'detail_id_supplier' => $this->supplier->supplier_id,
            'detail_status_cuci' => CuciEnum::CUCI, 'detail_status_kepemilikan' => RsStatusEnum::DEDICATED,
            'detail_status_register' => RegisterEnum::REGISTER, 'detail_status_linen' => LinenStatusEnum::BERSIH,
            'detail_tgl_cek' => now()->format('Y-m-d'), 'detail_updated_at' => $old, 'detail_updated_by' => $this->admin->id,
        ]);
        DB::table('config_linen')->updateOrInsert(['detail_rfid' => $rfid, 'rs_id' => $this->rs->rs_id], []);
        // detail_updated_at tidak fillable — set via query builder agar lolos aturan 15 jam KOTOR
        DB::table('detail_linen')->where('detail_rfid', $rfid)->update(['detail_updated_at' => $old]);
        DB::table('outstanding')->where('outstanding_rfid', $rfid)->delete();
    }
});

it('kotor tanpa qty tetap jalan dan response ada qty sejajar data', function () {
    $res = $this->withToken($this->token)->postJson('/api/kotor', [
        'rfid' => $this->rfids, 'rs_id' => $this->rs->rs_id, 'key' => 'QTY-'.uniqid(),
    ])->assertOk()->assertJson(['status' => true]);

    expect($res->json('qty'))->toBe(2);
    expect($res->json('data'))->toHaveCount(2);
});

it('kotor dengan qty sesuai jumlah rfid lolos dan qty ikut di response', function () {
    $res = $this->withToken($this->token)->postJson('/api/kotor', [
        'rfid' => $this->rfids, 'rs_id' => $this->rs->rs_id, 'key' => 'QTY-'.uniqid(), 'qty' => 2,
    ])->assertOk()->assertJson(['status' => true]);

    expect($res->json('qty'))->toBe(2);
});

it('qty beda dari jumlah rfid tetap diterima dan di-echo apa adanya', function () {
    $res = $this->withToken($this->token)->postJson('/api/kotor', [
        'rfid' => $this->rfids, 'rs_id' => $this->rs->rs_id, 'key' => 'QTY-'.uniqid(), 'qty' => 20,
    ])->assertOk()->assertJson(['status' => true]);

    expect($res->json('qty'))->toBe(20);
    expect($res->json('data'))->toHaveCount(2);

    // transaksi tetap tersimpan normal
    foreach ($this->rfids as $rfid) {
        expect(DB::table('transaksi')->where('transaksi_rfid', $rfid)->whereDate('transaksi_created_at', today())->count())->toBe(1);
    }
});

it('qty desimal juga diterima dan di-echo apa adanya', function () {
    $res = $this->withToken($this->token)->postJson('/api/retur', [
        'rfid' => [$this->rfids[0]], 'rs_id' => $this->rs->rs_id, 'key' => 'QTY-'.uniqid(), 'qty' => 2.5,
    ])->assertOk()->assertJson(['status' => true]);

    expect($res->json('qty'))->toBe(2.5);
});

it('retur dan rewash juga terima qty opsional', function () {
    $this->withToken($this->token)->postJson('/api/retur', [
        'rfid' => [$this->rfids[0]], 'rs_id' => $this->rs->rs_id, 'key' => 'QTY-'.uniqid(), 'qty' => 1,
    ])->assertOk()->assertJson(['status' => true])->assertJsonPath('qty', 1);

    $this->withToken($this->token)->postJson('/api/rewash', [
        'rfid' => [$this->rfids[1]], 'rs_id' => $this->rs->rs_id, 'key' => 'QTY-'.uniqid(),
    ])->assertOk()->assertJson(['status' => true])->assertJsonPath('qty', 1);
});
