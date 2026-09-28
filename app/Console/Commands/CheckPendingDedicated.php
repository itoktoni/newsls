<?php

namespace App\Console\Commands;

use App\Enums\LogType;
use App\Enums\RsStatusEnum;
use App\Models\DetailLinen;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Pending Dedicated — linen DEDICATED yang nyangkut di laundry.
 *
 * Tiap jalan: cari baris outstanding proses SCAN yang tidak bergerak
 * > 24 jam milik linen DEDICATED, masukkan ke tabel `pending` (sumber
 * report-pending-linen/table), lalu tandai outstanding-nya hilang=PENDING
 * supaya tidak diproses ulang.
 *
 * Idempoten: RFID yang sudah punya pending terbuka (bersih_at NULL)
 * dilewati. Penutupan pending tetap lewat delivery (pending_bersih_at).
 */
class CheckPendingDedicated extends Command
{
    protected $signature = 'check:pending-dedicated';

    protected $description = 'Masukkan outstanding SCAN >24 jam milik linen DEDICATED ke tabel pending';

    public function handle(): int
    {
        if (! Schema::hasTable('pending')) {
            $this->warn('Tabel pending tidak ada — dilewati.');

            return self::SUCCESS;
        }

        $cutoff = now()->subDay()->format('Y-m-d H:i:s');
        $now = now()->format('Y-m-d H:i:s');

        $inserted = 0;

        DB::table('outstanding as o')
            ->join('detail_linen as d', 'd.detail_rfid', '=', 'o.outstanding_rfid')
            ->where('o.outstanding_status_proses', 'SCAN')
            ->where('o.outstanding_updated_at', '<=', $cutoff)
            ->where('o.outstanding_status_hilang', 'NORMAL')
            ->whereNotNull('o.outstanding_rs_ori')
            ->where('d.detail_status_kepemilikan', RsStatusEnum::DEDICATED)
            ->orderBy('o.outstanding_updated_at')
            ->select([
                'o.outstanding_rfid', 'o.outstanding_key', 'o.outstanding_rs_ori',
                'o.outstanding_id_ruangan', 'o.outstanding_status_transaksi',
                'o.outstanding_status_proses', 'o.outstanding_created_at',
                'o.outstanding_created_by', 'd.detail_id_jenis',
            ])
            ->chunk(500, function ($rows) use ($now, &$inserted) {
                $rfids = $rows->pluck('outstanding_rfid')->all();

                // Lewati yang sudah punya pending terbuka (perbaikan bug
                // andalan yang hanya cek RFID terakhir loop).
                $open = DB::table('pending')
                    ->whereIn('pending_rfid', $rfids)
                    ->whereNull('pending_bersih_at')
                    ->pluck('pending_rfid')
                    ->flip()
                    ->all();

                $insert = [];
                foreach ($rows as $row) {
                    if (isset($open[$row->outstanding_rfid])) {
                        continue;
                    }

                    $insert[] = [
                        'pending_rfid' => $row->outstanding_rfid,
                        'pending_key' => $row->outstanding_key,
                        'pending_id_rs' => $row->outstanding_rs_ori,
                        'pending_id_ruangan' => $row->outstanding_id_ruangan,
                        'pending_id_jenis' => $row->detail_id_jenis,
                        'pending_created_at' => $now,
                        'pending_updated_at' => $now,
                        'pending_kotor_at' => $row->outstanding_created_at,
                        'pending_transaksi' => $row->outstanding_status_transaksi,
                        'pending_proses' => $row->outstanding_status_proses,
                        'pending_created_by' => $row->outstanding_created_by,
                        'pending_updated_by' => $row->outstanding_created_by,
                        'pending_kotor_by' => $row->outstanding_created_by,
                        'pending_status' => LogType::PENDING,
                    ];
                }

                if ($insert === []) {
                    return;
                }

                DB::table('pending')->insert($insert);

                $insertedRfids = collect($insert)->pluck('pending_rfid')->all();

                DB::table('outstanding')->whereIn('outstanding_rfid', $insertedRfids)->update([
                    'outstanding_status_hilang' => 'PENDING',
                    'outstanding_pending_created_at' => $now,
                    'outstanding_pending_updated_at' => $now,
                ]);

                $this->logPending($insertedRfids, $now);

                $inserted += count($insert);
            });

        $this->info("Pending dedicated: {$inserted} RFID dimasukkan.");

        return self::SUCCESS;
    }

    private function logPending(array $rfids, string $now): void
    {
        $rows = [];
        foreach ($rfids as $rfid) {
            $rows[] = [
                'log_name' => LogType::PENDING,
                'description' => 'RFID Pending '.$rfid,
                'event' => null,
                'subject_type' => DetailLinen::class,
                'subject_id' => $rfid,
                'causer_type' => null,
                'causer_id' => null,
                'attribute_changes' => '[]',
                'properties' => json_encode(['rfid' => $rfid, 'by' => 'check:pending-dedicated']),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('activity_log')->insert($chunk);
        }
    }
}
