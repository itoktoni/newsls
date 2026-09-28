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
    $this->rs = Rs::create(['rs_nama' => 'RS Grouping Noscan', 'rs_code' => 'GRP'.$uniq, 'rs_status' => RsStatusEnum::DEDICATED, 'rs_aktif' => 1]);
    $this->ruangan = Ruangan::create(['ruangan_nama' => 'Ruang Grouping', 'ruangan_code' => 'GRR'.$uniq]);
    $this->jenis = JenisLinen::create(['jenis_nama' => 'Seprai Grouping '.$uniq]);
    $this->bahan = JenisBahan::create(['bahan_nama' => 'Katun Grouping '.$uniq]);
    $this->supplier = Supplier::create(['supplier_nama' => 'Supplier Grouping '.$uniq]);
    DB::table('rs_dan_ruangan')->insert(['rs_id' => $this->rs->rs_id, 'ruangan_id' => $this->ruangan->ruangan_id]);
    DB::table('rs_dan_jenis')->insert(['rs_id' => $this->rs->rs_id, 'jenis_id' => $this->jenis->jenis_id, 'parstock' => 10]);
    DB::table('warehouse')->updateOrInsert(['warehouse_id' => 1], ['warehouse_nama' => 'Gudang Utama']);
    $this->admin = User::create(['name' => 'Admin Grouping', 'email' => 'grouping-'.$uniq.'@bka.test', 'password' => Hash::make('password'), 'role' => 'admin', 'verified_at' => now(), 'email_verified_at' => now()]);
    DB::table('rs_dan_user')->insert(['user_id' => $this->admin->id, 'rs_id' => $this->rs->rs_id]);
    $this->token = $this->admin->createToken('test-token')->plainTextToken;

    // Linen BERSIH yang baru dikirim (report 2 hari lalu), TANPA jejak
    // transaksi/outstanding — simulasi "tidak tertembak di RS".
    $this->rfid = 'GRPNS-'.$uniq;
    DetailLinen::create([
        'detail_rfid' => $this->rfid, 'detail_id_rs' => $this->rs->rs_id, 'detail_id_ruangan' => $this->ruangan->ruangan_id,
        'detail_id_jenis' => $this->jenis->jenis_id, 'detail_id_bahan' => $this->bahan->bahan_id, 'detail_id_supplier' => $this->supplier->supplier_id,
        'detail_status_cuci' => CuciEnum::CUCI, 'detail_status_kepemilikan' => RsStatusEnum::DEDICATED,
        'detail_status_register' => RegisterEnum::REGISTER, 'detail_status_linen' => LinenStatusEnum::BERSIH,
        'detail_report' => now()->subDays(2)->format('Y-m-d'),
        'detail_total_bersih' => 1, 'detail_created_by' => $this->admin->id,
    ]);
    DB::table('config_linen')->insert(['detail_rfid' => $this->rfid, 'rs_id' => $this->rs->rs_id]);
});

/**
 * Grouping = linen kotor tiba di gudang: tanpa scan kotor sebelumnya,
 * grouping wajib membuat transaksi KOTOR (key GRP) + outstanding.
 */
it('grouping tanpa scan kotor membuat transaksi KOTOR key GRP dan outstanding GUDANG', function () {
    expect(DB::table('transaksi')->where('transaksi_rfid', $this->rfid)->count())->toBe(0);
    expect(DB::table('outstanding')->where('outstanding_rfid', $this->rfid)->count())->toBe(0);

    $this->withToken($this->token)->getJson("/api/grouping/{$this->rfid}")
        ->assertOk()
        ->assertJson(['rfid' => $this->rfid, 'status_linen' => 'KOTOR']);

    $trx = DB::table('transaksi')->where('transaksi_rfid', $this->rfid)->first();
    expect($trx)->not->toBeNull();
    expect($trx->transaksi_status)->toBe('KOTOR');
    expect(str_starts_with($trx->transaksi_key, 'GRP'))->toBeTrue();
    expect($trx->transaksi_grouping)->toBe('YA');
    expect($trx->transaksi_grouping_date)->toBe(today()->format('Y-m-d'));

    $out = DB::table('outstanding')->where('outstanding_rfid', $this->rfid)->first();
    expect($out)->not->toBeNull();
    expect($out->outstanding_status_transaksi)->toBe('KOTOR');
    expect($out->outstanding_status_proses)->toBe('GUDANG');
    expect((int) $out->outstanding_id_warehouse)->toBe(1);

    expect(DetailLinen::where('detail_rfid', $this->rfid)->value('detail_status_linen'))->toBe(LinenStatusEnum::GUDANG);

    // Grouping kedua di hari yang sama: idempoten, tidak nambah baris
    $this->withToken($this->token)->getJson("/api/grouping/{$this->rfid}")->assertOk();
    expect(DB::table('transaksi')->where('transaksi_rfid', $this->rfid)->count())->toBe(1);
    expect(DB::table('outstanding')->where('outstanding_rfid', $this->rfid)->count())->toBe(1);
});
