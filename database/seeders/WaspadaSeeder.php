<?php

namespace Database\Seeders;

use App\Models\DetailLinen;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Backfill batas cuci + contoh data Linen Waspada (aman, idempoten).
 *
 * - Tidak truncate apa pun; hanya UPDATE null + updateOrCreate 3 RFID demo.
 * - Kata yang dipakai: Waspada / Prioritas (tanpa tua/rusak).
 */
class WaspadaSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Batas default 100 untuk yang masih null/0.
        DB::table('jenis_linen')
            ->whereNull('jenis_max_cuci')
            ->orWhere('jenis_max_cuci', '<=', 0)
            ->update(['jenis_max_cuci' => 100]);

        // 2) Batas wajar per nama (keyword, lowercase).
        $defaults = [
            'handuk' => 80,
            'seprai' => 100,
            'sprei' => 100,
            'duk' => 60,
            'baju' => 120,
            'kalong' => 120,
            'selimut' => 150,
            'gorden' => 200,
        ];
        foreach (DB::table('jenis_linen')->select('jenis_id', 'jenis_nama')->get() as $row) {
            $nama = strtolower((string) $row->jenis_nama);
            foreach ($defaults as $key => $maks) {
                if (str_contains($nama, $key)) {
                    DB::table('jenis_linen')->where('jenis_id', $row->jenis_id)->update(['jenis_max_cuci' => $maks]);
                    break;
                }
            }
        }

        // 3) Contoh data: RS Demo Pending (86) + Seprai Demo (413) + IGD (267).
        // Idempoten via updateOrCreate; hanya 3 baris demo.
        $rsId = 86;
        $jenisId = 413;
        $ruanganId = 267;
        if (! DB::table('rs')->where('rs_id', $rsId)->exists()) {
            $this->command->warn("RS demo $rsId tidak ada — lewati contoh data.");

            return;
        }
        if (! DB::table('jenis_linen')->where('jenis_id', $jenisId)->exists()) {
            $this->command->warn("Jenis demo $jenisId tidak ada — lewati contoh data.");

            return;
        }
        DB::table('jenis_linen')->where('jenis_id', $jenisId)->update(['jenis_max_cuci' => 100]);
        DB::table('rs_dan_ruangan')->updateOrInsert(['rs_id' => $rsId, 'ruangan_id' => $ruanganId], []);
        DB::table('rs_dan_jenis')->updateOrInsert(['rs_id' => $rsId, 'jenis_id' => $jenisId], ['parstock' => 50]);

        $demo = [
            'WASPADA-DEMO-01' => 85,
            'WASPADA-DEMO-02' => 100,
            'WASPADA-DEMO-03' => 115,
        ];
        foreach ($demo as $rfid => $pakai) {
            DetailLinen::updateOrCreate(
                ['detail_rfid' => $rfid],
                [
                    'detail_id_rs' => $rsId,
                    'detail_id_ruangan' => $ruanganId,
                    'detail_id_jenis' => $jenisId,
                    'detail_status_linen' => 'BERSIH',
                    'detail_total_bersih' => $pakai,
                    'detail_tgl_cek' => now()->format('Y-m-d'),
                ]
            );
            DB::table('config_linen')->updateOrInsert(['detail_rfid' => $rfid, 'rs_id' => $rsId], []);
        }

        $this->command->info('WaspadaSeeder selesai: batas cuci dibackfill + 3 contoh (85=Waspada, 100/115=Prioritas).');
    }
}
