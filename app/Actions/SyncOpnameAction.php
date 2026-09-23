<?php

namespace App\Actions;

use App\Models\OpnameDetail;
use App\Support\DashboardCache;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class SyncOpnameAction
{
    use AsAction;

    public function handle(int $opnameId, array $rfids, string $code = 'SYNC'): array
    {
        $rfids = collect($rfids)->filter()->unique()->values()->all();
        if (empty($rfids)) {
            throw new \InvalidArgumentException('RFID kosong');
        }

        DB::beginTransaction();
        try {
            $waktu = now()->format('Y-m-d H:i:s');
            $userId = auth()->id();

            $existing = OpnameDetail::where('opname_detail_id_opname', $opnameId)->whereIn('opname_detail_rfid', $rfids)->get()->keyBy('opname_detail_rfid');

            $toInsert = [];
            $toUpdate = [];

            foreach ($rfids as $rfid) {
                if (isset($existing[$rfid])) {
                    if ((int) $existing[$rfid]->opname_detail_ketemu === 1) {
                        // sudah ketemu, update sync saja
                        $toUpdate[] = $rfid;
                    } else {
                        $toUpdate[] = $rfid;
                    }
                } else {
                    // RFID baru tidak ada di snapshot — insert sebagai ketemu
                    $toInsert[] = [
                        'opname_detail_id_opname' => $opnameId,
                        'opname_detail_rfid' => $rfid,
                        'opname_detail_code' => $code,
                        'opname_detail_transaksi' => 'UNKNOWN',
                        'opname_detail_proses' => 'UNKNOWN',
                        'opname_detail_ketemu' => 1,
                        'opname_detail_scan_rs' => 1,
                        'opname_detail_sync' => 1,
                        'opname_detail_reff' => $code,
                        'opname_detail_scan_by' => 'OPNAME',
                        'opname_detail_waktu' => $waktu,
                        'opname_detail_created_at' => $waktu,
                        'opname_detail_created_by' => $userId,
                    ];
                }
            }

            if (! empty($toInsert)) {
                foreach (array_chunk($toInsert, 500) as $chunk) {
                    OpnameDetail::insert($chunk);
                }
            }

            if (! empty($toUpdate)) {
                OpnameDetail::where('opname_detail_id_opname', $opnameId)->whereIn('opname_detail_rfid', $toUpdate)->update([
                    'opname_detail_ketemu' => 1,
                    'opname_detail_scan_rs' => 1,
                    'opname_detail_waktu' => $waktu,
                    'opname_detail_sync' => 1,
                    'opname_detail_reff' => $code,
                    'opname_detail_scan_by' => 'OPNAME',
                ]);
            }

            // juga update grouping sync seperti andalan: opname yang sama, rfid ketemu
            // plus sync ke outstanding if needed

            DB::commit();
            DashboardCache::flush();

            return ['inserted' => count($toInsert), 'updated' => count($toUpdate), 'total' => count($rfids)];
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
