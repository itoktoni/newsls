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

it('grouping linen beredar: transaksi KOTOR tercatat dan KPI dashboard kotor ikut naik', function () {
    expect(DB::connection()->getDatabaseName())->toBe('test_bka');

    $rs = Rs::updateOrCreate(['rs_code' => 'GKD'], ['rs_nama' => 'RS GKD', 'rs_status' => RsStatusEnum::DEDICATED, 'rs_aktif' => 1]);
    $ruangan = Ruangan::firstOrCreate(['ruangan_code' => 'GKD'], ['ruangan_nama' => 'Ruang GKD']);
    $jenis = JenisLinen::firstOrCreate(['jenis_nama' => 'Seprai GKD']);
    $bahan = JenisBahan::firstOrCreate(['bahan_nama' => 'Katun GKD']);
    $supplier = Supplier::firstOrCreate(['supplier_nama' => 'Supplier GKD']);
    DB::table('warehouse')->updateOrInsert(['warehouse_id' => 1], ['warehouse_nama' => 'Gudang Utama']);
    $admin = User::firstOrCreate(['email' => 'gkd@bka.test'], ['name' => 'Admin GKD', 'password' => Hash::make('password'), 'role' => 'admin', 'verified_at' => now(), 'email_verified_at' => now()]);
    $token = $admin->createToken('test-token-gkd')->plainTextToken;

    $rfid = 'GKD-'.strtoupper(uniqid());
    DetailLinen::updateOrCreate(['detail_rfid' => $rfid], [
        'detail_id_rs' => $rs->rs_id, 'detail_id_ruangan' => $ruangan->ruangan_id,
        'detail_id_jenis' => $jenis->jenis_id, 'detail_id_bahan' => $bahan->bahan_id,
        'detail_id_supplier' => $supplier->supplier_id,
        'detail_status_cuci' => CuciEnum::CUCI, 'detail_status_kepemilikan' => RsStatusEnum::DEDICATED,
        'detail_status_register' => RegisterEnum::REGISTER, 'detail_status_linen' => LinenStatusEnum::BERSIH,
        'detail_updated_at' => now()->subDays(2),
    ]);
    DB::table('outstanding')->where('outstanding_rfid', $rfid)->delete();
    DB::table('transaksi')->where('transaksi_rfid', $rfid)->whereDate('transaksi_created_at', today())->delete();

    // Grouping QC → lahir baris transaksi KOTOR key GRP + detail jadi GUDANG.
    $this->withToken($token)->getJson("/api/grouping/{$rfid}")->assertOk();
    expect(DB::table('transaksi')->where('transaksi_rfid', $rfid)->where('transaksi_status', 'KOTOR')->count())->toBeGreaterThanOrEqual(1)
        ->and(DetailLinen::where('detail_rfid', $rfid)->value('detail_status_linen'))->toBe(LinenStatusEnum::GUDANG);

    // KPI kotor dashboard admin harus menghitungnya (tidak 0).
    $res = $this->actingAs($admin)->get('/dashboard/admin?rs_id='.$rs->rs_id)->assertOk();
    expect($res->viewData('kpi')['kotor'] ?? 0)->toBeGreaterThanOrEqual(1);

    // Bersih-bersih.
    DB::table('outstanding')->where('outstanding_rfid', $rfid)->delete();
    DB::table('transaksi')->where('transaksi_rfid', $rfid)->delete();
    DetailLinen::where('detail_rfid', $rfid)->delete();
});
