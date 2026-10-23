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

    $this->rsA = Rs::updateOrCreate(['rs_code' => 'GRD_A'], ['rs_nama' => 'RS Guard A', 'rs_status' => RsStatusEnum::DEDICATED, 'rs_aktif' => 1]);
    $this->rsB = Rs::updateOrCreate(['rs_code' => 'GRD_B'], ['rs_nama' => 'RS Guard B', 'rs_status' => RsStatusEnum::DEDICATED, 'rs_aktif' => 1]);
    $this->ruangan = Ruangan::firstOrCreate(['ruangan_code' => 'GRD'], ['ruangan_nama' => 'Ruang Guard']);
    $this->jenis = JenisLinen::firstOrCreate(['jenis_nama' => 'Seprai Single']);
    $this->bahan = JenisBahan::firstOrCreate(['bahan_nama' => 'Katun']);
    $this->supplier = Supplier::firstOrCreate(['supplier_nama' => 'Supplier Test']);
    foreach ([$this->rsA, $this->rsB] as $rs) {
        DB::table('rs_dan_ruangan')->updateOrInsert(['rs_id' => $rs->rs_id, 'ruangan_id' => $this->ruangan->ruangan_id], []);
    }
    DB::table('warehouse')->updateOrInsert(['warehouse_id' => 1], ['warehouse_nama' => 'Gudang Utama']);
    $this->admin = User::firstOrCreate(['email' => 'guard@bka.test'], ['name' => 'Admin Guard', 'password' => Hash::make('password'), 'role' => 'admin', 'verified_at' => now(), 'email_verified_at' => now()]);
    foreach ([$this->rsA, $this->rsB] as $rs) {
        DB::table('rs_dan_user')->updateOrInsert(['user_id' => $this->admin->id, 'rs_id' => $rs->rs_id], []);
    }
    $this->token = $this->admin->createToken('test-token-'.uniqid())->plainTextToken;

    $this->rfid = 'GUARD_PACK_'.uniqid();
    $now = now();
    DetailLinen::updateOrCreate(['detail_rfid' => $this->rfid], [
        'detail_id_rs' => $this->rsA->rs_id, 'detail_id_ruangan' => $this->ruangan->ruangan_id,
        'detail_id_jenis' => $this->jenis->jenis_id, 'detail_id_bahan' => $this->bahan->bahan_id, 'detail_id_supplier' => $this->supplier->supplier_id,
        'detail_status_cuci' => CuciEnum::CUCI, 'detail_status_kepemilikan' => RsStatusEnum::DEDICATED,
        'detail_status_register' => RegisterEnum::REGISTER, 'detail_status_linen' => LinenStatusEnum::GUDANG,
        'detail_tgl_cek' => now()->format('Y-m-d'), 'detail_updated_at' => $now, 'detail_updated_by' => $this->admin->id,
    ]);
    DB::table('config_linen')->updateOrInsert(['detail_rfid' => $this->rfid, 'rs_id' => $this->rsA->rs_id], []);
    DB::table('outstanding')->updateOrInsert(['outstanding_rfid' => $this->rfid], [
        'outstanding_key' => 'REG-GUARD', 'outstanding_rs_ori' => $this->rsA->rs_id, 'outstanding_rs_scan' => $this->rsA->rs_id,
        'outstanding_id_ruangan' => $this->ruangan->ruangan_id, 'outstanding_status_transaksi' => 'KOTOR', 'outstanding_status_proses' => 'GUDANG',
        'outstanding_status_hilang' => 'NORMAL', 'outstanding_created_at' => $now, 'outstanding_updated_at' => $now,
        'outstanding_created_by' => $this->admin->id, 'outstanding_updated_by' => $this->admin->id, 'outstanding_id_warehouse' => 1,
    ]);
});

it('packing ulang sebelum delivery ditolak, apalagi ke RS lain', function () {
    $bersihBefore = DB::table('bersih')->where('bersih_rfid', $this->rfid)->count();

    // 1) packing ke RS A — sukses
    $this->withToken($this->token)->postJson('/api/packing', [
        'rfid' => [$this->rfid], 'rs_id' => $this->rsA->rs_id, 'ruangan_id' => $this->ruangan->ruangan_id, 'status_transaksi' => 'KOTOR',
    ])->assertOk()->assertJson(['status' => true]);

    expect(DB::table('outstanding')->where('outstanding_rfid', $this->rfid)->value('outstanding_status_proses'))->toBe('PACKING');
    expect(DB::table('bersih')->where('bersih_rfid', $this->rfid)->count())->toBe($bersihBefore + 1);

    // 2) packing ulang ke RS B — ditolak dengan pesan eksplisit + sebut RS A
    $this->withToken($this->token)->postJson('/api/packing', [
        'rfid' => [$this->rfid], 'rs_id' => $this->rsB->rs_id, 'ruangan_id' => $this->ruangan->ruangan_id, 'status_transaksi' => 'KOTOR',
    ])->assertOk()
        ->assertJson(['status' => false])
        ->assertJsonPath('code', 422);
    $msg = $this->withToken($this->token)->postJson('/api/packing', [
        'rfid' => [$this->rfid], 'rs_id' => $this->rsB->rs_id, 'ruangan_id' => $this->ruangan->ruangan_id, 'status_transaksi' => 'KOTOR',
    ])->json('message');
    expect($msg)->toContain('pernah di-packing')->and($msg)->toContain($this->rfid);

    // 3) packing ulang ke RS yang sama pun tetap ditolak
    $this->withToken($this->token)->postJson('/api/packing', [
        'rfid' => [$this->rfid], 'rs_id' => $this->rsA->rs_id, 'ruangan_id' => $this->ruangan->ruangan_id, 'status_transaksi' => 'KOTOR',
    ])->assertOk()->assertJson(['status' => false]);

    // 4) state tidak berubah: outstanding tetap milik RS A, tidak ada dobel bersih
    expect(DB::table('outstanding')->where('outstanding_rfid', $this->rfid)->value('outstanding_rs_scan'))->toBe($this->rsA->rs_id);
    expect(DB::table('bersih')->where('bersih_rfid', $this->rfid)->count())->toBe($bersihBefore + 1);
});

it('packing ditolak bila bersih belum delivery walau outstanding sudah berubah', function () {
    $bersihBefore = DB::table('bersih')->where('bersih_rfid', $this->rfid)->count();

    // packing ke RS A — sukses
    $this->withToken($this->token)->postJson('/api/packing', [
        'rfid' => [$this->rfid], 'rs_id' => $this->rsA->rs_id, 'ruangan_id' => $this->ruangan->ruangan_id, 'status_transaksi' => 'KOTOR',
    ])->assertOk()->assertJson(['status' => true]);

    // simulasi outstanding tertimpa alur lain (mis. scan QC/grouping balikan
    // ke GUDANG) sementara baris bersih belum di-delivery
    DB::table('outstanding')->where('outstanding_rfid', $this->rfid)->update(['outstanding_status_proses' => 'GUDANG']);
    expect(DB::table('bersih')->where('bersih_rfid', $this->rfid)->whereNull('bersih_delivery')->count())->toBe($bersihBefore + 1);

    // packing ulang ke RS B — tetap ditolak karena cek tabel bersih
    $res = $this->withToken($this->token)->postJson('/api/packing', [
        'rfid' => [$this->rfid], 'rs_id' => $this->rsB->rs_id, 'ruangan_id' => $this->ruangan->ruangan_id, 'status_transaksi' => 'KOTOR',
    ])->assertOk()->assertJson(['status' => false]);
    expect($res->json('message'))->toContain('belum di-delivery')->and($res->json('message'))->toContain($this->rfid);

    // tidak ada baris bersih ganda
    expect(DB::table('bersih')->where('bersih_rfid', $this->rfid)->count())->toBe($bersihBefore + 1);

    // kembalikan ke PACKING lalu delivery normal — siklus selesai
    DB::table('outstanding')->where('outstanding_rfid', $this->rfid)->update(['outstanding_status_proses' => 'PACKING']);
    $this->withToken($this->token)->postJson('/api/delivery', [
        'rs_id' => $this->rsA->rs_id, 'status_transaksi' => 'KOTOR',
    ])->assertOk()->assertJson(['status' => true]);
    expect(DB::table('bersih')->where('bersih_rfid', $this->rfid)->whereNull('bersih_delivery')->count())->toBe(0);

    // siklus berikutnya (kotor lagi → GUDANG) → boleh packing lagi, tidak over-block
    $now = now()->format('Y-m-d H:i:s');
    DetailLinen::where('detail_rfid', $this->rfid)->update(['detail_status_linen' => 'KOTOR']);
    DB::table('outstanding')->updateOrInsert(['outstanding_rfid' => $this->rfid], [
        'outstanding_key' => 'REG-GUARD2', 'outstanding_rs_ori' => $this->rsA->rs_id, 'outstanding_rs_scan' => $this->rsA->rs_id,
        'outstanding_id_ruangan' => $this->ruangan->ruangan_id, 'outstanding_status_transaksi' => 'KOTOR', 'outstanding_status_proses' => 'GUDANG',
        'outstanding_status_hilang' => 'NORMAL', 'outstanding_created_at' => $now, 'outstanding_updated_at' => $now,
        'outstanding_created_by' => $this->admin->id, 'outstanding_updated_by' => $this->admin->id, 'outstanding_id_warehouse' => 1,
    ]);
    $this->withToken($this->token)->postJson('/api/packing', [
        'rfid' => [$this->rfid], 'rs_id' => $this->rsA->rs_id, 'ruangan_id' => $this->ruangan->ruangan_id, 'status_transaksi' => 'KOTOR',
    ])->assertOk()->assertJson(['status' => true]);
});
