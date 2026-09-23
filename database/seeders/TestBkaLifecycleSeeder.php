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
 * Seeder PERSISTENT untuk test_bka — data TIDAK di-rollback.
 * Pakai ini kalau mau lihat rs/transaksi/outstanding di phpMyAdmin/HeidiSQL setelah run.
 * Beda dengan Pest RefreshDatabase yang sengaja hapus data tiap test method.
 *
 * Jalankan: php artisan db:seed --class=TestBkaLifecycleSeeder
 *   (pastikan .env atau --env=testing menunjuk DB_DATABASE=test_bka, lihat phpunit.xml)
 *
 * Atau: DB_DATABASE=test_bka php artisan db:seed --class=TestBkaLifecycleSeeder
 */
class TestBkaLifecycleSeeder extends Seeder
{
    public function run(): void
    {
        // Paksa koneksi ke test_bka kalau masih di bka (user lupa set env) — auto-switch, jangan hapus bka
        $dbName = DB::connection()->getDatabaseName();
        if ($dbName !== 'test_bka') {
            $this->command->warn("Seeder ini untuk test_bka, sekarang di $dbName — auto-switch ke test_bka.");
            config(['database.connections.mariadb.database' => 'test_bka']);
            DB::purge('mariadb');
            DB::reconnect('mariadb');
            $dbName = DB::connection()->getDatabaseName();
            $this->command->info("Sekarang di DB: $dbName");
            if ($dbName !== 'test_bka') {
                $this->command->error("Gagal switch ke test_bka (masih $dbName). Jalankan manual: \$env:DB_DATABASE='test_bka'; php artisan db:seed --class=TestBkaLifecycleSeeder --force");
                return;
            }
        }

        // Bersihkan hanya test_bka biar lihat data fresh (komentar baris ini kalau mau append)
        DB::table('bersih')->truncate();
        DB::table('cetak')->truncate();
        DB::table('outstanding')->truncate();
        DB::table('transaksi')->truncate();
        DB::table('config_linen')->delete();
        DetailLinen::query()->delete();
        // rs/ruangan/jenis/bahan/supplier/users: truncate + seed ulang
        DB::table('rs_dan_jenis')->delete();
        DB::table('rs_dan_ruangan')->delete();
        DB::table('rs_dan_user')->delete();
        Rs::query()->delete();
        Ruangan::query()->delete();
        JenisLinen::query()->delete();
        JenisBahan::query()->delete();
        Supplier::query()->delete();

        $rs = Rs::create([
            'rs_nama' => 'RS Lifecycle Test',
            'rs_code' => 'LCT',
            'rs_status' => RsStatusEnum::DEDICATED,
            'rs_aktif' => 1,
        ]);

        $ruangan = Ruangan::create(['ruangan_nama' => 'Ruang Dahlia', 'ruangan_code' => 'DHL']);
        $jenis = JenisLinen::create(['jenis_nama' => 'Seprai Single', 'jenis_deskripsi' => 'Seprai single 160x250']);
        $bahan = JenisBahan::create(['bahan_nama' => 'Katun', 'bahan_deskripsi' => 'Katun 100%']);
        $supplier = Supplier::create(['supplier_nama' => 'Supplier Test', 'supplier_email' => 'supplier@test.local']);

        DB::table('rs_dan_ruangan')->insert(['rs_id' => $rs->rs_id, 'ruangan_id' => $ruangan->ruangan_id]);
        DB::table('rs_dan_jenis')->insert(['rs_id' => $rs->rs_id, 'jenis_id' => $jenis->jenis_id, 'parstock' => 10]);
        DB::table('warehouse')->updateOrInsert(['warehouse_id' => 1], ['warehouse_nama' => 'Gudang Utama']);

        $admin = User::firstOrCreate(
            ['email' => 'lifecycle@bka.test'],
            ['name' => 'Admin Lifecycle', 'password' => Hash::make('password'), 'role' => 'admin', 'verified_at' => now(), 'email_verified_at' => now()]
        );
        DB::table('rs_dan_user')->updateOrInsert(['user_id' => $admin->id, 'rs_id' => $rs->rs_id], []);

        // ---- Registrasi TEST1-3 via Action (tanpa HTTP, langsung persist) ----
        $rfids = ['TEST1', 'TEST2', 'TEST3'];
        foreach ($rfids as $rfid) {
            DetailLinen::create([
                'detail_rfid' => $rfid,
                'detail_id_rs' => $rs->rs_id,
                'detail_id_ruangan' => $ruangan->ruangan_id,
                'detail_id_jenis' => $jenis->jenis_id,
                'detail_id_bahan' => $bahan->bahan_id,
                'detail_id_supplier' => $supplier->supplier_id,
                'detail_status_cuci' => CuciEnum::CUCI,
                'detail_status_kepemilikan' => RsStatusEnum::DEDICATED,
                'detail_status_register' => RegisterEnum::REGISTER,
                'detail_status_linen' => LinenStatusEnum::REGISTER,
                'detail_tgl_cek' => now()->format('Y-m-d'),
                'detail_created_by' => $admin->id,
            ]);
            DB::table('config_linen')->insert(['detail_rfid' => $rfid, 'rs_id' => $rs->rs_id]);
        }

        // Ganti chip TEST2 -> TEST2_NEW (REGISTER GANTI_CHIP = KOTOR)
        DetailLinen::create([
            'detail_rfid' => 'TEST2_NEW',
            'detail_id_rs' => $rs->rs_id,
            'detail_id_ruangan' => $ruangan->ruangan_id,
            'detail_id_jenis' => $jenis->jenis_id,
            'detail_id_bahan' => $bahan->bahan_id,
            'detail_id_supplier' => $supplier->supplier_id,
            'detail_status_cuci' => CuciEnum::CUCI,
            'detail_status_kepemilikan' => RsStatusEnum::DEDICATED,
            'detail_status_register' => RegisterEnum::GANTI_CHIP,
            'detail_status_linen' => LinenStatusEnum::KOTOR,
            'detail_tgl_cek' => now()->format('Y-m-d'),
            'detail_created_by' => $admin->id,
        ]);
        DB::table('config_linen')->insert(['detail_rfid' => 'TEST2_NEW', 'rs_id' => $rs->rs_id]);
        DB::table('ganti_chip')->updateOrInsert(
            ['ganti_rfid_lama' => 'TEST2', 'ganti_rfid_baru' => 'TEST2_NEW'],
            ['ganti_tanggal' => now(), 'ganti_by' => $admin->id, 'ganti_keterangan' => 'Chip TEST2 rusak']
        );

        // --- Simulasi siklus grouping -> packing -> delivery -> kotor biar transaksi/outstanding/cetak keisi PERSISTEN ---
        // Grouping TEST1-3: REGISTER -> GUDANG (Outstanding GUDANG)
        $now = now();
        foreach ($rfids as $rfid) {
            DB::table('outstanding')->updateOrInsert(
                ['outstanding_rfid' => $rfid],
                [
                    'outstanding_key' => 'REG-'.now()->format('YmdHis'),
                    'outstanding_rs_ori' => $rs->rs_id,
                    'outstanding_rs_scan' => $rs->rs_id,
                    'outstanding_id_ruangan' => $ruangan->ruangan_id,
                    'outstanding_status_transaksi' => 'REGISTER',
                    'outstanding_status_proses' => 'GUDANG',
                    'outstanding_status_hilang' => 'NORMAL',
                    'outstanding_created_at' => $now,
                    'outstanding_updated_at' => $now,
                    'outstanding_created_by' => $admin->id,
                    'outstanding_updated_by' => $admin->id,
                ]
            );
            DetailLinen::where('detail_rfid', $rfid)->update(['detail_status_linen' => LinenStatusEnum::GUDANG, 'detail_updated_at' => $now, 'detail_updated_by' => $admin->id]);
        }

        // Packing: GUDANG -> PACKING + cetak type 1
        $packCode = generatePackingCode();
        DB::table('outstanding')->whereIn('outstanding_rfid', $rfids)->update(['outstanding_status_proses' => 'PACKING', 'outstanding_updated_at' => $now]);
        DB::table('cetak')->insert([
            'cetak_code' => $packCode,
            'cetak_date' => now()->format('Y-m-d'),
            'cetak_user' => $admin->name,
            'cetak_id_rs' => $rs->rs_id,
            'cetak_id_ruangan' => $ruangan->ruangan_id,
            'cetak_type' => 1,
            'cetak_barcode' => $packCode,
            'cetak_rfids' => json_encode($rfids),
        ]);

        // Delivery: PACKING -> BERSIH, hapus outstanding, cetak type 2, transaksi tidak ada BERSIH (legacy)
        DB::table('outstanding')->whereIn('outstanding_rfid', $rfids)->delete();
        foreach ($rfids as $rfid) {
            DetailLinen::where('detail_rfid', $rfid)->update([
                'detail_status_linen' => LinenStatusEnum::BERSIH,
                'detail_report' => now()->format('Y-m-d'),
                'detail_total_bersih' => DB::raw('COALESCE(detail_total_bersih,0)+1'),
                'detail_updated_at' => $now,
                'detail_updated_by' => $admin->id,
            ]);
        }
        $deliveryCode = generateDeliveryCode($rs->rs_code, 'BSH');
        DB::table('cetak')->insert([
            'cetak_code' => $deliveryCode,
            'cetak_date' => now()->format('Y-m-d'),
            'cetak_user' => $admin->name,
            'cetak_id_rs' => $rs->rs_id,
            'cetak_type' => 2,
            'cetak_delivery' => $deliveryCode,
            'cetak_rfids' => json_encode($rfids),
        ]);
        // Legacy bersih — report lama baca dari sini juga (BuildsDeliveryReport). Isi biar report bener.
        foreach ($rfids as $rfid) {
            DB::table('bersih')->insert([
                'bersih_rfid' => $rfid,
                'bersih_status' => 'BERSIH',
                'bersih_id_rs' => $rs->rs_id,
                'bersih_id_ruangan' => $ruangan->ruangan_id,
                'bersih_barcode' => $packCode,
                'bersih_delivery' => $deliveryCode,
                'bersih_created_at' => $now,
                'bersih_updated_at' => $now,
                'bersih_created_by' => $admin->id,
                'bersih_updated_by' => $admin->id,
                'bersih_report' => now()->format('Y-m-d'),
            ]);
        }

        // Kotor lagi: BERSIH -> KOTOR, insert transaksi KOTOR + outstanding SCAN
        $kotorKey = strtoupper('KTR-'.now()->format('YmdHis').'-'.uniqid());
        foreach ($rfids as $rfid) {
            DB::table('transaksi')->insert([
                'transaksi_key' => $kotorKey,
                'transaksi_rfid' => $rfid,
                'transaksi_rs_ori' => $rs->rs_id,
                'transaksi_rs_scan' => $rs->rs_id,
                'transaksi_beda_rs' => 'TIDAK',
                'transaksi_id_ruangan' => $ruangan->ruangan_id,
                'transaksi_status' => 'KOTOR',
                'transaksi_created_at' => $now,
                'transaksi_updated_at' => $now,
                'transaksi_created_by' => $admin->id,
                'transaksi_updated_by' => $admin->id,
            ]);
            DB::table('outstanding')->insert([
                'outstanding_rfid' => $rfid,
                'outstanding_key' => $kotorKey,
                'outstanding_rs_ori' => $rs->rs_id,
                'outstanding_rs_scan' => $rs->rs_id,
                'outstanding_id_ruangan' => $ruangan->ruangan_id,
                'outstanding_status_transaksi' => 'KOTOR',
                'outstanding_status_proses' => 'SCAN',
                'outstanding_status_hilang' => 'NORMAL',
                'outstanding_created_at' => $now,
                'outstanding_updated_at' => $now,
                'outstanding_created_by' => $admin->id,
                'outstanding_updated_by' => $admin->id,
            ]);
            DetailLinen::where('detail_rfid', $rfid)->update(['detail_status_linen' => LinenStatusEnum::KOTOR]);
        }

        $this->command->info("Seed test_bka selesai di DB $dbName:");
        $this->command->info("  rs: ".Rs::count()." ruangan: ".Ruangan::count()." detail_linen: ".DetailLinen::count()." transaksi: ".DB::table('transaksi')->count()." outstanding: ".DB::table('outstanding')->count()." cetak: ".DB::table('cetak')->count()." bersih: ".DB::table('bersih')->count());
        $this->command->info("  Cek: SELECT * FROM test_bka.rs; SELECT * FROM test_bka.detail_linen; SELECT * FROM test_bka.transaksi; SELECT * FROM test_bka.outstanding; SELECT * FROM test_bka.cetak;");
        $this->command->info("  Login admin: lifecycle@bka.test / password (id {$admin->id}, rs {$rs->rs_id})");
        $this->command->info("  Catatan: php artisan test pakai RefreshDatabase akan TRUNCATE test_bka lagi — seed lagi setelah test kalau mau lihat.");
    }
}
