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
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Data demo alur pending — untuk LIHAT report di browser (DB dev bka).
 *
 * Skenario RS DEMO ("RS Demo Pending"):
 *  - Duk: kotor 10 (2 hari lalu) → terkirim 5 (RFID beda) → terbayar 3 hari
 *    ini = sisa 2. Terlihat di Pending per Jenis + Pelunasan Pending.
 *  - 1 RFID SCAN 25 jam tak bergerak (dedicated) → jadi pending via
 *    check:pending-dedicated. Terlihat di Pending Dedicated.
 *  - 1 RFID GUDANG 40 hari tak bergerak → terlihat di Pending Outstanding
 *    mode stagnan 1 bulan.
 *  - 1 RFID BERSIH 45 hari tak bergerak → terlihat di Stagnan di RS.
 *  - 1 baris pending yang sudah lunas (contoh jejak bayar).
 *
 * Idempoten (aman di-run ulang, prefix DEMO-). Jalankan:
 *   php artisan db:seed --class=PendingDemoSeeder
 */
class PendingDemoSeeder extends Seeder
{
    public function run(): void
    {
        $rs = Rs::firstOrCreate(['rs_code' => 'DEMO'], [
            'rs_nama' => 'RS Demo Pending', 'rs_status' => RsStatusEnum::DEDICATED, 'rs_aktif' => 1,
        ]);
        $ruangan = Ruangan::firstOrCreate(['ruangan_code' => 'DEMORUANG'], ['ruangan_nama' => 'Ruang Demo']);
        $duk = JenisLinen::firstOrCreate(['jenis_nama' => 'Duk Demo']);
        $bahan = JenisBahan::firstOrCreate(['bahan_nama' => 'Katun Demo']);
        $supplier = Supplier::firstOrCreate(['supplier_nama' => 'Supplier Demo']);
        DB::table('rs_dan_ruangan')->updateOrInsert(['rs_id' => $rs->rs_id, 'ruangan_id' => $ruangan->ruangan_id], []);
        DB::table('rs_dan_jenis')->updateOrInsert(['rs_id' => $rs->rs_id, 'jenis_id' => $duk->jenis_id], ['parstock' => 100]);
        DB::table('warehouse')->updateOrInsert(['warehouse_id' => 1], ['warehouse_nama' => 'Gudang Utama']);
        $admin = User::firstOrCreate(['email' => 'demo@bka.test'], [
            'name' => 'Demo Pending', 'password' => Hash::make('password'),
            'role' => 'admin', 'verified_at' => now(), 'email_verified_at' => now(),
        ]);
        DB::table('rs_dan_user')->updateOrInsert(['user_id' => $admin->id, 'rs_id' => $rs->rs_id], []);

        $mk = fn () => [$rs, $ruangan, $duk, $bahan, $supplier, $admin];

        // --- 1) Kotor 10 Duk, 2 hari lalu ---
        $t2 = now()->subDays(2)->format('Y-m-d H:i:s');
        for ($i = 1; $i <= 10; $i++) {
            $this->detail("DEMO-KOTOR-{$i}", $mk, LinenStatusEnum::KOTOR);
            $this->transaksi("DEMO-KOTOR-{$i}", $mk, 'KOTOR', $t2);
        }

        // --- 2) Terkirim 5 (RFID beda), 2 hari lalu ---
        for ($i = 1; $i <= 5; $i++) {
            $this->detail("DEMO-KIRIM1-{$i}", $mk, LinenStatusEnum::BERSIH);
            $this->kirim("DEMO-KIRIM1-{$i}", $mk, 'BSH-DEMO-1', $t2);
        }

        // --- 3) Terbayar 3 lagi hari ini ---
        $now = now()->format('Y-m-d H:i:s');
        for ($i = 1; $i <= 3; $i++) {
            $this->detail("DEMO-KIRIM2-{$i}", $mk, LinenStatusEnum::BERSIH);
            $this->kirim("DEMO-KIRIM2-{$i}", $mk, 'BSH-DEMO-2', $now);
        }

        // --- 4) SCAN 25 jam tak bergerak → pending dedicated ---
        $this->detail('DEMO-SCAN-MACET', $mk, LinenStatusEnum::KOTOR);
        $this->transaksi('DEMO-SCAN-MACET', $mk, 'KOTOR', now()->subHours(26)->format('Y-m-d H:i:s'));
        $this->outstanding('DEMO-SCAN-MACET', $mk, 'KOTOR', 'SCAN', 25);

        // --- 5) GUDANG 40 hari tak bergerak → stagnan outstanding ---
        $this->detail('DEMO-GUDANG-TUA', $mk, LinenStatusEnum::GUDANG);
        $this->outstanding('DEMO-GUDANG-TUA', $mk, 'KOTOR', 'GUDANG', 24 * 40);

        // --- 6) BERSIH 45 hari tak bergerak → stagnan RS ---
        $this->detail('DEMO-RS-TUA', $mk, LinenStatusEnum::BERSIH);
        DetailLinen::where('detail_rfid', 'DEMO-RS-TUA')->update([
            'detail_updated_at' => now()->subDays(45)->format('Y-m-d H:i:s'),
        ]);

        // --- 7) Contoh pending yang sudah lunas ---
        $this->detail('DEMO-LUNAS', $mk, LinenStatusEnum::BERSIH);
        DB::table('pending')->updateOrInsert(['pending_rfid' => 'DEMO-LUNAS'], [
            'pending_key' => 'KTR-DEMO-LUNAS',
            'pending_id_rs' => $rs->rs_id, 'pending_id_ruangan' => $ruangan->ruangan_id,
            'pending_id_jenis' => $duk->jenis_id,
            'pending_created_at' => now()->subDays(5)->format('Y-m-d H:i:s'),
            'pending_updated_at' => $now,
            'pending_kotor_at' => now()->subDays(5)->format('Y-m-d H:i:s'),
            'pending_bersih_at' => now()->subDays(1)->format('Y-m-d H:i:s'),
            'pending_created_by' => $admin->id, 'pending_updated_by' => $admin->id,
            'pending_kotor_by' => $admin->id, 'pending_bersih_by' => $admin->id,
            'pending_delivery' => 'BSH-DEMO-LUNAS',
            'pending_status' => 'PENDING', 'pending_transaksi' => 'KOTOR', 'pending_proses' => 'SCAN',
        ]);

        // --- 8) Jalankan scheduler pending supaya DEMO-SCAN-MACET masuk tabel ---
        Artisan::call('check:pending-dedicated');

        $this->command->info('Demo pending siap di RS Demo Pending. Lihat: Pending per Jenis, Pelunasan Pending, Pending Dedicated, Pending Outstanding (stagnan 1 bulan), Stagnan di RS.');
    }

    private function masters(callable $mk): array
    {
        return $mk();
    }

    private function detail(string $rfid, callable $mk, string $linen): void
    {
        [$rs, $ruangan, $duk, $bahan, $supplier, $admin] = $this->masters($mk);

        DetailLinen::firstOrCreate(['detail_rfid' => $rfid], [
            'detail_id_rs' => $rs->rs_id, 'detail_id_ruangan' => $ruangan->ruangan_id,
            'detail_id_jenis' => $duk->jenis_id, 'detail_id_bahan' => $bahan->bahan_id,
            'detail_id_supplier' => $supplier->supplier_id,
            'detail_status_cuci' => CuciEnum::CUCI, 'detail_status_kepemilikan' => RsStatusEnum::DEDICATED,
            'detail_status_register' => RegisterEnum::REGISTER, 'detail_status_linen' => $linen,
            'detail_created_by' => $admin->id,
        ]);
        DB::table('config_linen')->updateOrInsert(['detail_rfid' => $rfid, 'rs_id' => $rs->rs_id], []);
    }

    private function transaksi(string $rfid, callable $mk, string $status, string $at): void
    {
        [$rs, $ruangan, $duk, $bahan, $supplier, $admin] = $this->masters($mk);

        if (DB::table('transaksi')->where('transaksi_rfid', $rfid)->where('transaksi_key', 'KTR-'.$rfid)->exists()) {
            return;
        }
        DB::table('transaksi')->insert([
            'transaksi_key' => 'KTR-'.$rfid, 'transaksi_rfid' => $rfid,
            'transaksi_rs_ori' => $rs->rs_id, 'transaksi_rs_scan' => $rs->rs_id,
            'transaksi_beda_rs' => 'TIDAK', 'transaksi_id_ruangan' => $ruangan->ruangan_id,
            'transaksi_status' => $status,
            'transaksi_created_at' => $at, 'transaksi_created_by' => $admin->id,
            'transaksi_updated_at' => $at, 'transaksi_updated_by' => $admin->id,
        ]);
    }

    private function kirim(string $rfid, callable $mk, string $code, string $at): void
    {
        [$rs, $ruangan, $duk, $bahan, $supplier, $admin] = $this->masters($mk);

        if (DB::table('bersih')->where('bersih_rfid', $rfid)->where('bersih_delivery', $code)->exists()) {
            return;
        }
        DB::table('bersih')->insert([
            'bersih_rfid' => $rfid, 'bersih_status' => 'BERSIH',
            'bersih_id_rs' => $rs->rs_id, 'bersih_id_ruangan' => $ruangan->ruangan_id,
            'bersih_barcode' => 'BC-'.$rfid, 'bersih_delivery' => $code,
            'bersih_created_at' => $at, 'bersih_updated_at' => $at,
            'bersih_created_by' => $admin->id, 'bersih_updated_by' => $admin->id,
            'bersih_report' => substr($at, 0, 10),
        ]);
    }

    private function outstanding(string $rfid, callable $mk, string $transaksi, string $proses, int $hoursAgo): void
    {
        [$rs, $ruangan, $duk, $bahan, $supplier, $admin] = $this->masters($mk);

        DB::table('outstanding')->updateOrInsert(['outstanding_rfid' => $rfid], [
            'outstanding_key' => 'KTR-'.$rfid,
            'outstanding_rs_ori' => $rs->rs_id, 'outstanding_rs_scan' => $rs->rs_id,
            'outstanding_id_ruangan' => $ruangan->ruangan_id,
            'outstanding_status_transaksi' => $transaksi, 'outstanding_status_proses' => $proses,
            'outstanding_status_hilang' => 'NORMAL',
            'outstanding_created_at' => now()->subHours($hoursAgo + 1)->format('Y-m-d H:i:s'),
            'outstanding_updated_at' => now()->subHours($hoursAgo)->format('Y-m-d H:i:s'),
            'outstanding_created_by' => $admin->id, 'outstanding_updated_by' => $admin->id,
            'outstanding_id_warehouse' => 1,
        ]);
    }
}
