<?php

namespace Database\Seeders;

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
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Seed 3 flow berbeda di test_bka — buat cek report & outstanding:
 *  - KOTOR  : TEST_KOTOR_1..3  → detail KOTOR + outstanding SCAN/KOTOR + transaksi KOTOR (stok laundry)
 *  - BERSIH : TEST_BERSIH_1..3 → detail BERSIH + bersih/cetak delivery (sudah dicuci, stok 0)
 *  - PENDING: TEST_PENDING_1..3→ detail KOTOR + outstanding PENDING/HILANG=PENDING (linen hilang pending)
 *
 * Jalankan: php artisan db:seed --class=TestBkaFlowsSeeder --force  (auto-switch ke test_bka)
 * Lihat: SELECT detail_rfid, detail_status_linen FROM test_bka.detail_linen;
 *        SELECT outstanding_rfid, outstanding_status_transaksi, outstanding_status_proses, outstanding_status_hilang FROM test_bka.outstanding;
 *        SELECT transaksi_rfid, transaksi_status FROM test_bka.transaksi;
 *        SELECT bersih_rfid, bersih_status FROM test_bka.bersih;
 */
class TestBkaFlowsSeeder extends Seeder
{
    public function run(): void
    {
        $dbName = DB::connection()->getDatabaseName();
        if ($dbName !== 'test_bka') {
            config(['database.connections.mariadb.database' => 'test_bka']);
            DB::purge('mariadb');
            DB::reconnect('mariadb');
            $dbName = DB::connection()->getDatabaseName();
        }

        // clean test_bka only
        DB::table('bersih')->truncate();
        DB::table('cetak')->truncate();
        DB::table('outstanding')->truncate();
        DB::table('transaksi')->truncate();
        DB::table('config_linen')->delete();
        DetailLinen::query()->delete();
        DB::table('rs_dan_jenis')->delete();
        DB::table('rs_dan_ruangan')->delete();
        DB::table('rs_dan_user')->delete();
        Rs::query()->delete();
        Ruangan::query()->delete();
        JenisLinen::query()->delete();
        JenisBahan::query()->delete();
        Supplier::query()->delete();

        $rs = Rs::create(['rs_nama' => 'RS Flows Test', 'rs_code' => 'FLW', 'rs_status' => RsStatusEnum::DEDICATED, 'rs_aktif' => 1]);
        $ruangan = Ruangan::create(['ruangan_nama' => 'Ruang Dahlia', 'ruangan_code' => 'DHL']);
        $jenis = JenisLinen::create(['jenis_nama' => 'Seprai Single']);
        $bahan = JenisBahan::create(['bahan_nama' => 'Katun']);
        $supplier = Supplier::create(['supplier_nama' => 'Supplier Test']);
        DB::table('rs_dan_ruangan')->insert(['rs_id' => $rs->rs_id, 'ruangan_id' => $ruangan->ruangan_id]);
        DB::table('rs_dan_jenis')->insert(['rs_id' => $rs->rs_id, 'jenis_id' => $jenis->jenis_id, 'parstock' => 10]);
        DB::table('warehouse')->updateOrInsert(['warehouse_id' => 1], ['warehouse_nama' => 'Gudang Utama']);
        $admin = User::firstOrCreate(['email' => 'lifecycle@bka.test'], ['name' => 'Admin Lifecycle', 'password' => Hash::make('password'), 'role' => 'admin', 'verified_at' => now(), 'email_verified_at' => now()]);
        DB::table('rs_dan_user')->updateOrInsert(['user_id' => $admin->id, 'rs_id' => $rs->rs_id], []);

        $now = now();

        // ---------- FLOW KOTOR ----------
        $kotorRfids = ['TEST_KOTOR_1', 'TEST_KOTOR_2', 'TEST_KOTOR_3'];
        $kotorKey = strtoupper('KTR-FLOW-'.uniqid());
        foreach ($kotorRfids as $rfid) {
            DetailLinen::create([
                'detail_rfid' => $rfid, 'detail_id_rs' => $rs->rs_id, 'detail_id_ruangan' => $ruangan->ruangan_id,
                'detail_id_jenis' => $jenis->jenis_id, 'detail_id_bahan' => $bahan->bahan_id, 'detail_id_supplier' => $supplier->supplier_id,
                'detail_status_cuci' => CuciEnum::CUCI, 'detail_status_kepemilikan' => RsStatusEnum::DEDICATED,
                'detail_status_register' => RegisterEnum::REGISTER, 'detail_status_linen' => LinenStatusEnum::KOTOR,
                'detail_tgl_cek' => now()->format('Y-m-d'), 'detail_created_by' => $admin->id,
            ]);
            DB::table('config_linen')->insert(['detail_rfid' => $rfid, 'rs_id' => $rs->rs_id]);
            DB::table('transaksi')->insert([
                'transaksi_key' => $kotorKey, 'transaksi_rfid' => $rfid, 'transaksi_rs_ori' => $rs->rs_id, 'transaksi_rs_scan' => $rs->rs_id,
                'transaksi_beda_rs' => 'TIDAK', 'transaksi_id_ruangan' => $ruangan->ruangan_id, 'transaksi_status' => 'KOTOR',
                'transaksi_created_at' => $now, 'transaksi_updated_at' => $now, 'transaksi_created_by' => $admin->id, 'transaksi_updated_by' => $admin->id,
            ]);
            DB::table('outstanding')->insert([
                'outstanding_rfid' => $rfid, 'outstanding_key' => $kotorKey, 'outstanding_rs_ori' => $rs->rs_id, 'outstanding_rs_scan' => $rs->rs_id,
                'outstanding_id_ruangan' => $ruangan->ruangan_id, 'outstanding_status_transaksi' => 'KOTOR', 'outstanding_status_proses' => 'SCAN',
                'outstanding_status_hilang' => 'NORMAL', 'outstanding_created_at' => $now, 'outstanding_updated_at' => $now,
                'outstanding_created_by' => $admin->id, 'outstanding_updated_by' => $admin->id,
            ]);
        }

        // ---------- FLOW BERSIH ----------
        $bersihRfids = ['TEST_BERSIH_1', 'TEST_BERSIH_2', 'TEST_BERSIH_3'];
        $packCode = generatePackingCode();
        $deliveryCode = generateDeliveryCode($rs->rs_code, 'BSH');
        foreach ($bersihRfids as $rfid) {
            DetailLinen::create([
                'detail_rfid' => $rfid, 'detail_id_rs' => $rs->rs_id, 'detail_id_ruangan' => $ruangan->ruangan_id,
                'detail_id_jenis' => $jenis->jenis_id, 'detail_id_bahan' => $bahan->bahan_id, 'detail_id_supplier' => $supplier->supplier_id,
                'detail_status_cuci' => CuciEnum::CUCI, 'detail_status_kepemilikan' => RsStatusEnum::DEDICATED,
                'detail_status_register' => RegisterEnum::REGISTER, 'detail_status_linen' => LinenStatusEnum::BERSIH,
                'detail_tgl_cek' => now()->format('Y-m-d'), 'detail_report' => now()->format('Y-m-d'),
                'detail_total_bersih' => 1, 'detail_created_by' => $admin->id, 'detail_updated_by' => $admin->id,
            ]);
            DB::table('config_linen')->insert(['detail_rfid' => $rfid, 'rs_id' => $rs->rs_id]);
            DB::table('bersih')->insert([
                'bersih_rfid' => $rfid, 'bersih_status' => 'BERSIH', 'bersih_id_rs' => $rs->rs_id, 'bersih_id_ruangan' => $ruangan->ruangan_id,
                'bersih_barcode' => $packCode, 'bersih_delivery' => $deliveryCode, 'bersih_created_at' => $now, 'bersih_updated_at' => $now,
                'bersih_created_by' => $admin->id, 'bersih_updated_by' => $admin->id, 'bersih_report' => now()->format('Y-m-d'),
            ]);
        }
        DB::table('cetak')->insert([
            'cetak_code' => $packCode, 'cetak_date' => now()->format('Y-m-d'), 'cetak_user' => $admin->name,
            'cetak_id_rs' => $rs->rs_id, 'cetak_id_ruangan' => $ruangan->ruangan_id, 'cetak_type' => 1, 'cetak_barcode' => $packCode, 'cetak_rfids' => json_encode($bersihRfids),
        ]);
        DB::table('cetak')->insert([
            'cetak_code' => $deliveryCode, 'cetak_date' => now()->format('Y-m-d'), 'cetak_user' => $admin->name,
            'cetak_id_rs' => $rs->rs_id, 'cetak_type' => 2, 'cetak_delivery' => $deliveryCode, 'cetak_rfids' => json_encode($bersihRfids),
        ]);
        // transaksi history KOTOR untuk yang bersih juga (pernah kotor sebelum dicuci)
        $histKey = strtoupper('KTR-BERSIH-'.uniqid());
        foreach ($bersihRfids as $rfid) {
            DB::table('transaksi')->insert([
                'transaksi_key' => $histKey, 'transaksi_rfid' => $rfid, 'transaksi_rs_ori' => $rs->rs_id, 'transaksi_rs_scan' => $rs->rs_id,
                'transaksi_beda_rs' => 'TIDAK', 'transaksi_id_ruangan' => $ruangan->ruangan_id, 'transaksi_status' => 'KOTOR',
                'transaksi_created_at' => $now->subDay(), 'transaksi_updated_at' => $now->subDay(), 'transaksi_created_by' => $admin->id, 'transaksi_updated_by' => $admin->id,
            ]);
        }

        // ---------- FLOW PENDING ----------
        $pendingRfids = ['TEST_PENDING_1', 'TEST_PENDING_2', 'TEST_PENDING_3'];
        $pendingKey = strtoupper('KTR-PENDING-'.uniqid());
        foreach ($pendingRfids as $rfid) {
            DetailLinen::create([
                'detail_rfid' => $rfid, 'detail_id_rs' => $rs->rs_id, 'detail_id_ruangan' => $ruangan->ruangan_id,
                'detail_id_jenis' => $jenis->jenis_id, 'detail_id_bahan' => $bahan->bahan_id, 'detail_id_supplier' => $supplier->supplier_id,
                'detail_status_cuci' => CuciEnum::CUCI, 'detail_status_kepemilikan' => RsStatusEnum::DEDICATED,
                'detail_status_register' => RegisterEnum::REGISTER, 'detail_status_linen' => LinenStatusEnum::KOTOR,
                'detail_tgl_cek' => now()->format('Y-m-d'), 'detail_created_by' => $admin->id,
            ]);
            DB::table('config_linen')->insert(['detail_rfid' => $rfid, 'rs_id' => $rs->rs_id]);
            DB::table('transaksi')->insert([
                'transaksi_key' => $pendingKey, 'transaksi_rfid' => $rfid, 'transaksi_rs_ori' => $rs->rs_id, 'transaksi_rs_scan' => $rs->rs_id,
                'transaksi_beda_rs' => 'TIDAK', 'transaksi_id_ruangan' => $ruangan->ruangan_id, 'transaksi_status' => 'KOTOR',
                'transaksi_created_at' => $now, 'transaksi_updated_at' => $now, 'transaksi_created_by' => $admin->id, 'transaksi_updated_by' => $admin->id,
            ]);
            DB::table('outstanding')->insert([
                'outstanding_rfid' => $rfid, 'outstanding_key' => $pendingKey, 'outstanding_rs_ori' => $rs->rs_id, 'outstanding_rs_scan' => $rs->rs_id,
                'outstanding_id_ruangan' => $ruangan->ruangan_id, 'outstanding_status_transaksi' => 'KOTOR', 'outstanding_status_proses' => 'PENDING',
                'outstanding_status_hilang' => 'PENDING', 'outstanding_created_at' => $now, 'outstanding_updated_at' => $now,
                'outstanding_created_by' => $admin->id, 'outstanding_updated_by' => $admin->id,
                'outstanding_pending_created_at' => $now, 'outstanding_pending_updated_at' => $now,
            ]);
        }

        $this->command->info("Seed flows selesai di $dbName (rs {$rs->rs_id}):");
        $this->command->info("  KOTOR: ".count($kotorRfids)." (outstanding SCAN/NORMAL, transaksi KOTOR)");
        $this->command->info("  BERSIH: ".count($bersihRfids)." (detail BERSIH, bersih+cetak, outstanding 0)");
        $this->command->info("  PENDING: ".count($pendingRfids)." (outstanding PENDING/PENDING)");
        $this->command->info("  Total detail: ".DetailLinen::count()." transaksi: ".DB::table('transaksi')->count()." outstanding: ".DB::table('outstanding')->count()." bersih: ".DB::table('bersih')->count()." cetak: ".DB::table('cetak')->count());
    }
}
