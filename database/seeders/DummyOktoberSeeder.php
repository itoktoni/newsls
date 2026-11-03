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
use Illuminate\Support\Facades\Hash;

/**
 * Dummy data RFID 1-50 untuk eksplorasi report (Oktober 2026).
 *
 * - RFID 1-10   : KOTOR harian 1 Okt → 30 Okt (rotasi, transaksi_master + transaksi + outstanding)
 * - RFID 11-25  : BERSIH delivery 21-30 Okt (detail_report + tabel bersih, untuk invoice)
 * - RFID 26-30  : RETUR mid-Okt (transaksi REJECT + outstanding)
 * - RFID 31-35  : REWASH mid-Okt (transaksi REWASH + outstanding)
 * - RFID 36-45  : BERSIH tua (detail_report September → muncul di report cycle & stagnan)
 * - RFID 46-50  : REGISTER baru (belum bergerak sama sekali)
 *
 * Idempoten: hanya menyentuh RFID 1-50 + kode berprefix DMY-, aman
 * dijalankan ulang tanpa menghapus data asli. Jalan di DB koneksi aktif
 * (.env DB_DATABASE), BUKAN hardcode test_bka.
 *
 * Jalankan: php artisan db:seed --class=DummyOktoberSeeder
 */
class DummyOktoberSeeder extends Seeder
{
    public function run(): void
    {
        $rfids = array_map(strval(...), range(1, 50));

        $admin = User::firstOrCreate(
            ['email' => 'dummy@bka.test'],
            ['name' => 'Admin Dummy', 'password' => Hash::make('password'), 'role' => 'admin', 'email_verified_at' => now()]
        );

        $rs = Rs::firstOrCreate(
            ['rs_nama' => 'RS Dummy'],
            ['rs_code' => 'DMY', 'rs_status' => RsStatusEnum::DEDICATED, 'rs_aktif' => 1, 'rs_harga_cuci' => 12000]
        );
        DB::table('rs_dan_user')->updateOrInsert(['user_id' => $admin->id, 'rs_id' => $rs->rs_id], []);

        $ruangA = Ruangan::firstOrCreate(['ruangan_nama' => 'Ruang Dummy A'], ['ruangan_code' => 'DMYA']);
        $ruangB = Ruangan::firstOrCreate(['ruangan_nama' => 'Ruang Dummy B'], ['ruangan_code' => 'DMYB']);
        $bahan = JenisBahan::firstOrCreate(['bahan_nama' => 'Katun Dummy'], ['bahan_deskripsi' => 'Katun dummy']);
        $supplier = Supplier::firstOrCreate(['supplier_nama' => 'Supplier Dummy'], ['supplier_email' => 'dummy@supplier.local']);
        $jenis = [];
        foreach ([['Seprai Dummy', 0.8], ['Sarung Bantal Dummy', 0.25], ['Selimut Dummy', 1.2]] as [$nama, $berat]) {
            $jenis[] = JenisLinen::firstOrCreate(['jenis_nama' => $nama], ['jenis_deskripsi' => $nama, 'jenis_berat' => $berat]);
        }

        DB::table('rs_dan_ruangan')->updateOrInsert(['rs_id' => $rs->rs_id, 'ruangan_id' => $ruangA->ruangan_id], []);
        DB::table('rs_dan_ruangan')->updateOrInsert(['rs_id' => $rs->rs_id, 'ruangan_id' => $ruangB->ruangan_id], []);
        foreach ($jenis as $j) {
            DB::table('rs_dan_jenis')->updateOrInsert(['rs_id' => $rs->rs_id, 'jenis_id' => $j->jenis_id], ['parstock' => 50]);
        }
        DB::table('warehouse')->updateOrInsert(['warehouse_id' => 1], ['warehouse_nama' => 'Gudang Utama']);

        // ---- Bersihkan sisa run sebelumnya (scoped, data asli tidak tersentuh) ----
        DB::table('transaksi')->whereIn('transaksi_rfid', $rfids)->delete();
        DB::table('transaksi_master')->where('master_kode', 'like', 'DMY-%')->delete();
        DB::table('outstanding')->whereIn('outstanding_rfid', $rfids)->delete();
        DB::table('bersih')->whereIn('bersih_rfid', $rfids)->delete();
        DB::table('cetak')->where('cetak_code', 'like', 'DMY-%')->delete();
        DB::table('config_linen')->whereIn('detail_rfid', $rfids)->delete();
        DetailLinen::whereIn('detail_rfid', $rfids)->delete();

        // ---- Register 1-50 ----
        foreach ($rfids as $i => $rfid) {
            $n = (int) $rfid;
            DetailLinen::create([
                'detail_rfid' => $rfid,
                'detail_id_rs' => $rs->rs_id,
                'detail_id_ruangan' => $n % 2 ? $ruangA->ruangan_id : $ruangB->ruangan_id,
                'detail_id_jenis' => $jenis[$i % 3]->jenis_id,
                'detail_id_bahan' => $bahan->bahan_id,
                'detail_id_supplier' => $supplier->supplier_id,
                'detail_status_cuci' => CuciEnum::CUCI,
                'detail_status_kepemilikan' => RsStatusEnum::DEDICATED,
                'detail_status_register' => RegisterEnum::REGISTER,
                'detail_status_linen' => LinenStatusEnum::REGISTER,
                'detail_tgl_cek' => '2026-09-28',
                'detail_created_by' => $admin->id,
            ]);
            DB::table('config_linen')->insert(['detail_rfid' => $rfid, 'rs_id' => $rs->rs_id]);
        }

        $beratOf = fn (string $rfid): float => (float) DB::table('detail_linen')
            ->join('jenis_linen', 'jenis_linen.jenis_id', '=', 'detail_linen.detail_id_jenis')
            ->where('detail_linen.detail_rfid', $rfid)
            ->value('jenis_linen.jenis_berat');

        // ---- KOTOR harian 1-30 Okt untuk RFID 1-10 (rotasi 3 RFID/hari) ----
        $kotorIds = array_map(strval(...), range(1, 10));
        for ($day = 1; $day <= 30; $day++) {
            $date = Carbon::create(2026, 10, $day);
            $batch = array_values(array_filter($kotorIds, fn ($r) => ((int) $r + $day) % 3 === 0));
            if ($batch === []) {
                $batch = [$kotorIds[$day % 10]];
            }
            $key = sprintf('DMY-KTR-202610%02d', $day);
            $ts = $date->format('Y-m-d').' 08:00:00';
            $qty = 0.0;
            foreach ($batch as $rfid) {
                $qty += $beratOf($rfid);
                DB::table('transaksi')->insert([
                    'transaksi_key' => $key,
                    'transaksi_rfid' => $rfid,
                    'transaksi_rs_ori' => $rs->rs_id,
                    'transaksi_rs_scan' => $rs->rs_id,
                    'transaksi_beda_rs' => 'TIDAK',
                    'transaksi_id_ruangan' => (int) $rfid % 2 ? $ruangA->ruangan_id : $ruangB->ruangan_id,
                    'transaksi_status' => 'KOTOR',
                    'transaksi_created_at' => $ts,
                    'transaksi_updated_at' => $ts,
                    'transaksi_created_by' => $admin->id,
                    'transaksi_updated_by' => $admin->id,
                ]);
                DB::table('outstanding')->updateOrInsert(
                    ['outstanding_rfid' => $rfid],
                    [
                        'outstanding_key' => $key,
                        'outstanding_rs_ori' => $rs->rs_id,
                        'outstanding_rs_scan' => $rs->rs_id,
                        'outstanding_id_ruangan' => (int) $rfid % 2 ? $ruangA->ruangan_id : $ruangB->ruangan_id,
                        'outstanding_status_transaksi' => 'KOTOR',
                        'outstanding_status_proses' => 'SCAN',
                        'outstanding_status_hilang' => 'NORMAL',
                        'outstanding_created_at' => $ts,
                        'outstanding_updated_at' => $ts,
                        'outstanding_created_by' => $admin->id,
                        'outstanding_updated_by' => $admin->id,
                    ]
                );
            }
            DB::table('transaksi_master')->insert([
                'master_kode' => $key,
                'master_rs_id' => $rs->rs_id,
                'master_status' => 'KOTOR',
                'master_tanggal' => $date->format('Y-m-d'),
                'master_qty' => round($qty * 1.03, 3), // timbangan ~3% di atas estimasi
                'master_created_at' => $ts,
                'master_updated_at' => $ts,
                'master_created_by' => $admin->id,
                'master_updated_by' => $admin->id,
            ]);
            DetailLinen::whereIn('detail_rfid', $batch)->update(['detail_status_linen' => LinenStatusEnum::KOTOR]);
        }

        // ---- BERSIH delivery 21-30 Okt untuk RFID 11-25 ----
        $deliveryCode = 'DMY-DLV-20261030';
        DB::table('cetak')->insert([
            'cetak_code' => $deliveryCode,
            'cetak_date' => '2026-10-30',
            'cetak_user' => $admin->name,
            'cetak_id_rs' => $rs->rs_id,
            'cetak_type' => 2,
            'cetak_delivery' => $deliveryCode,
        ]);
        foreach (range(11, 25) as $n) {
            $rfid = (string) $n;
            $report = Carbon::create(2026, 10, 20 + (($n - 11) % 10) + 1)->format('Y-m-d'); // 21-30 Okt
            $ts = $report.' 14:00:00';
            DB::table('bersih')->insert([
                'bersih_rfid' => $rfid,
                'bersih_status' => 'BERSIH',
                'bersih_id_rs' => $rs->rs_id,
                'bersih_id_ruangan' => $n % 2 ? $ruangA->ruangan_id : $ruangB->ruangan_id,
                'bersih_delivery' => $deliveryCode,
                'bersih_created_at' => $ts,
                'bersih_updated_at' => $ts,
                'bersih_created_by' => $admin->id,
                'bersih_updated_by' => $admin->id,
                'bersih_report' => $report,
            ]);
            DB::table('detail_linen')->where('detail_rfid', $rfid)->update([
                'detail_status_linen' => LinenStatusEnum::BERSIH,
                'detail_report' => $report,
                'detail_total_bersih' => 2,
                'detail_updated_at' => $ts,
                'detail_updated_by' => $admin->id,
            ]);
        }

        // ---- RETUR 15 Okt (26-30) + REWASH 18 Okt (31-35), dari stok BERSIH ----
        foreach ([['26', '27', '28', '29', '30', 'REJECT', 'RETUR', 'DMY-RTR-20261015', '2026-10-15'], ['31', '32', '33', '34', '35', 'REWASH', 'REWASH', 'DMY-RWS-20261018', '2026-10-18']] as [$a, $b, $c, $d, $e, $status, $label, $key, $tgl]) {
            $batch = [$a, $b, $c, $d, $e];
            $ts = $tgl.' 10:00:00';
            $qty = 0.0;
            foreach ($batch as $rfid) {
                $qty += $beratOf($rfid);
                DB::table('transaksi')->insert([
                    'transaksi_key' => $key,
                    'transaksi_rfid' => $rfid,
                    'transaksi_rs_ori' => $rs->rs_id,
                    'transaksi_rs_scan' => $rs->rs_id,
                    'transaksi_beda_rs' => 'TIDAK',
                    'transaksi_id_ruangan' => (int) $rfid % 2 ? $ruangA->ruangan_id : $ruangB->ruangan_id,
                    'transaksi_status' => $status,
                    'transaksi_created_at' => $ts,
                    'transaksi_updated_at' => $ts,
                    'transaksi_created_by' => $admin->id,
                    'transaksi_updated_by' => $admin->id,
                ]);
                DB::table('outstanding')->updateOrInsert(
                    ['outstanding_rfid' => $rfid],
                    [
                        'outstanding_key' => $key,
                        'outstanding_rs_ori' => $rs->rs_id,
                        'outstanding_rs_scan' => $rs->rs_id,
                        'outstanding_id_ruangan' => (int) $rfid % 2 ? $ruangA->ruangan_id : $ruangB->ruangan_id,
                        'outstanding_status_transaksi' => $status,
                        'outstanding_status_proses' => 'SCAN',
                        'outstanding_status_hilang' => 'NORMAL',
                        'outstanding_created_at' => $ts,
                        'outstanding_updated_at' => $ts,
                        'outstanding_created_by' => $admin->id,
                        'outstanding_updated_by' => $admin->id,
                    ]
                );
            }
            DB::table('transaksi_master')->insert([
                'master_kode' => $key,
                'master_rs_id' => $rs->rs_id,
                'master_status' => $status,
                'master_tanggal' => $tgl,
                'master_qty' => round($qty, 3),
                'master_created_at' => $ts,
                'master_updated_at' => $ts,
                'master_created_by' => $admin->id,
                'master_updated_by' => $admin->id,
            ]);
            DB::table('detail_linen')->whereIn('detail_rfid', $batch)->update([
                'detail_status_linen' => LinenStatusEnum::BERSIH,
                'detail_report' => '2026-10-10',
                'detail_total_bersih' => 1,
                'detail_updated_at' => '2026-10-10 14:00:00',
                'detail_updated_by' => $admin->id,
            ]);
        }

        // ---- BERSIH tua September (36-45) → report cycle & stagnan ----
        foreach (range(36, 45) as $n) {
            $rfid = (string) $n;
            $report = Carbon::create(2026, 9, 3 + ($n - 36))->format('Y-m-d'); // 3-12 Sep
            $ts = $report.' 14:00:00';
            DB::table('bersih')->insert([
                'bersih_rfid' => $rfid,
                'bersih_status' => 'BERSIH',
                'bersih_id_rs' => $rs->rs_id,
                'bersih_id_ruangan' => $n % 2 ? $ruangA->ruangan_id : $ruangB->ruangan_id,
                'bersih_delivery' => 'DMY-DLV-20260915',
                'bersih_created_at' => $ts,
                'bersih_updated_at' => $ts,
                'bersih_created_by' => $admin->id,
                'bersih_updated_by' => $admin->id,
                'bersih_report' => $report,
            ]);
            DB::table('detail_linen')->where('detail_rfid', $rfid)->update([
                'detail_status_linen' => LinenStatusEnum::BERSIH,
                'detail_report' => $report,
                'detail_total_bersih' => 1,
                'detail_updated_at' => $ts,
                'detail_updated_by' => $admin->id,
            ]);
        }

        // ---- 46-50: REGISTER saja (tetap status awal, tgl_cek akhir Okt) ----
        DB::table('detail_linen')->whereIn('detail_rfid', ['46', '47', '48', '49', '50'])->update([
            'detail_tgl_cek' => '2026-10-29',
        ]);

        $this->command->info('DummyOktoberSeeder selesai (RS Dummy, RFID 1-50):');
        $this->command->info('  detail: '.DetailLinen::whereIn('detail_rfid', $rfids)->count().' transaksi: '.DB::table('transaksi')->whereIn('transaksi_rfid', $rfids)->count().' master: '.DB::table('transaksi_master')->where('master_kode', 'like', 'DMY-%')->count().' outstanding: '.DB::table('outstanding')->whereIn('outstanding_rfid', $rfids)->count().' bersih: '.DB::table('bersih')->whereIn('bersih_rfid', $rfids)->count());
        $this->command->info('  Login: dummy@bka.test / password — filter report per RS "RS Dummy".');
    }
}
