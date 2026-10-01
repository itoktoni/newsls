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
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Riwayat transaksi demo 1 Agustus → hari ini untuk RS DEMO.
 *
 * Pola per hari per jenis: kotor 5–15 potong (RFID baru tiap potong),
 * dibayar ~80% selang 2 hari dengan RFID BERBEDA, Minggu libur kirim
 * (backlog menumpuk, realistis). Sisa ~20% jadi hutang terlihat di
 * Pending per Jenis + Pelunasan Pending (aging per lot tanggal).
 *
 * Deterministik (seed 42) + idempoten: bila data DEMOH- sudah ada, dilewati.
 *
 * Jalankan: php artisan db:seed --class=PendingHistorySeeder
 */
class PendingHistorySeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('transaksi')->where('transaksi_key', 'like', 'KTR-DEMOH-%')->exists()) {
            $this->command->info('Riwayat DEMOH- sudah ada — dilewati.');

            return;
        }

        $rs = Rs::firstOrCreate(['rs_code' => 'DEMO'], [
            'rs_nama' => 'RS Demo Pending', 'rs_status' => RsStatusEnum::DEDICATED, 'rs_aktif' => 1,
        ]);
        $ruangan = Ruangan::firstOrCreate(['ruangan_code' => 'DEMORUANG'], ['ruangan_nama' => 'Ruang Demo']);
        $bahan = JenisBahan::firstOrCreate(['bahan_nama' => 'Katun Demo']);
        $supplier = Supplier::firstOrCreate(['supplier_nama' => 'Supplier Demo']);
        $admin = User::firstOrCreate(['email' => 'demo@bka.test'], [
            'name' => 'Demo Pending', 'password' => bcrypt('password'),
            'role' => 'admin', 'verified_at' => now(), 'email_verified_at' => now(),
        ]);

        $jenisList = [];
        foreach (['Duk Demo', 'Seprai Demo', 'Handuk Demo'] as $nama) {
            $j = JenisLinen::firstOrCreate(['jenis_nama' => $nama]);
            $jenisList[] = $j;
            DB::table('rs_dan_jenis')->updateOrInsert(
                ['rs_id' => $rs->rs_id, 'jenis_id' => $j->jenis_id], ['parstock' => 100]
            );
        }

        mt_srand(42);
        $start = Carbon::create(2026, 8, 1)->startOfDay();
        $today = now()->startOfDay();

        // Antrian lot menunggu dibayar: [jenisIdx => [[tanggal, sisa]...]]
        $backlog = [[], [], []];
        $nTrx = 0;
        $nKirim = 0;

        for ($day = $start->copy(); $day->lte($today); $day->addDay()) {
            $date = $day->format('Y-m-d');
            $ymd = $day->format('Ymd');
            $isSunday = $day->isSunday();

            foreach ($jenisList as $ji => $j) {
                // --- KOTOR hari ini ---
                $count = mt_rand(5, 15);
                for ($i = 1; $i <= $count; $i++) {
                    $rfid = "DEMOH-K-{$ymd}-{$ji}-{$i}";
                    $at = $day->copy()->setTime(mt_rand(6, 17), mt_rand(0, 59))->format('Y-m-d H:i:s');
                    $this->detail($rfid, $rs, $ruangan, $j, $bahan, $supplier, $admin, LinenStatusEnum::KOTOR);
                    $this->kotor($rfid, $rs, $ruangan, $admin, $at);
                    $nTrx++;
                }
                $backlog[$ji][] = ['tanggal' => $date, 'sisa' => $count];

                // --- BAYAR: lot yang berumur >= 2 hari, ~80%, Minggu libur ---
                if ($isSunday) {
                    continue;
                }
                $payAt = $day->copy()->setTime(mt_rand(9, 15), mt_rand(0, 59))->format('Y-m-d H:i:s');
                $code = 'BSH-H-'.$ymd;
                foreach ($backlog[$ji] as $k => $lot) {
                    if ($lot['sisa'] <= 0 || Carbon::parse($lot['tanggal'])->diffInDays($day) < 2) {
                        continue;
                    }
                    $pay = (int) floor($lot['sisa'] * 0.8);
                    for ($i = 1; $i <= $pay; $i++) {
                        $rfid = "DEMOH-B-{$ymd}-{$ji}-{$k}-{$i}";
                        $this->detail($rfid, $rs, $ruangan, $j, $bahan, $supplier, $admin, LinenStatusEnum::BERSIH);
                        $this->kirim($rfid, $rs, $ruangan, $admin, $code, $payAt);
                        $nKirim++;
                    }
                    $backlog[$ji][$k]['sisa'] -= $pay;
                }
            }
        }

        $sisa = array_sum(array_map(fn ($q) => array_sum(array_column($q, 'sisa')), $backlog));
        $this->command->info("Riwayat 1 Agu → hari ini: {$nTrx} kotor, {$nKirim} kirim, sisa hutang {$sisa} pcs.");
    }

    private function detail(string $rfid, Rs $rs, Ruangan $ruangan, JenisLinen $j, JenisBahan $bahan, Supplier $supplier, User $admin, string $linen): void
    {
        DetailLinen::firstOrCreate(['detail_rfid' => $rfid], [
            'detail_id_rs' => $rs->rs_id, 'detail_id_ruangan' => $ruangan->ruangan_id,
            'detail_id_jenis' => $j->jenis_id, 'detail_id_bahan' => $bahan->bahan_id,
            'detail_id_supplier' => $supplier->supplier_id,
            'detail_status_cuci' => CuciEnum::CUCI, 'detail_status_kepemilikan' => RsStatusEnum::DEDICATED,
            'detail_status_register' => RegisterEnum::REGISTER, 'detail_status_linen' => $linen,
            'detail_created_by' => $admin->id,
        ]);
    }

    private function kotor(string $rfid, Rs $rs, Ruangan $ruangan, User $admin, string $at): void
    {
        DB::table('transaksi')->insert([
            'transaksi_key' => 'KTR-'.$rfid, 'transaksi_rfid' => $rfid,
            'transaksi_rs_ori' => $rs->rs_id, 'transaksi_rs_scan' => $rs->rs_id,
            'transaksi_beda_rs' => 'TIDAK', 'transaksi_id_ruangan' => $ruangan->ruangan_id,
            'transaksi_status' => 'KOTOR',
            'transaksi_created_at' => $at, 'transaksi_created_by' => $admin->id,
            'transaksi_updated_at' => $at, 'transaksi_updated_by' => $admin->id,
        ]);
    }

    private function kirim(string $rfid, Rs $rs, Ruangan $ruangan, User $admin, string $code, string $at): void
    {
        DB::table('bersih')->insert([
            'bersih_rfid' => $rfid, 'bersih_status' => 'BERSIH',
            'bersih_id_rs' => $rs->rs_id, 'bersih_id_ruangan' => $ruangan->ruangan_id,
            'bersih_barcode' => 'BC-'.$rfid, 'bersih_delivery' => $code,
            'bersih_created_at' => $at, 'bersih_updated_at' => $at,
            'bersih_created_by' => $admin->id, 'bersih_updated_by' => $admin->id,
            'bersih_report' => substr($at, 0, 10),
        ]);
    }
}
