<?php

namespace App\Actions;

use App\Models\OpnameDetail;
use App\Support\DashboardCache;
use Carbon\Carbon;
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
                        // enum opname_detail_transaksi/proses tidak punya 'UNKNOWN';
                        // andalan memakai TransactionType::UNKNOWN yang bernilai null.
                        'opname_detail_transaksi' => null,
                        'opname_detail_proses' => null,
                        'opname_detail_register' => 0,
                        'opname_detail_ketemu' => 1,
                        'opname_detail_scan_rs' => 1,
                        'opname_detail_sync' => 1,
                        'opname_detail_reff' => $code,
                        'opname_detail_scan_by' => 'OPNAME',
                        'opname_detail_waktu' => $waktu,
                        'opname_detail_created_at' => $waktu,
                        'opname_detail_created_by' => $userId,
                        'opname_detail_updated_at' => $waktu,
                        'opname_detail_updated_by' => $userId,
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
                    'opname_detail_updated_at' => $waktu,
                    'opname_detail_updated_by' => $userId,
                ]);
            }

            // juga update grouping sync seperti andalan: opname yang sama, rfid ketemu
            // plus sync ke outstanding if needed

            DB::commit();
            DashboardCache::flush();

            // Respons legacy andalan (SaveOpnameService::$sent): item dibangun dari
            // input — satu item per RFID, key & urutannya sama persis, bukan baris DB.
            $sent = [];
            foreach ($rfids as $rfid) {
                $detail = $existing[$rfid] ?? null;

                $item = [
                    'opname_detail_rfid' => $rfid,
                    'opname_detail_id_opname' => $opnameId,
                    'opname_detail_code' => $code,
                    'opname_detail_register' => 0,
                    'opname_detail_updated_at' => $waktu,
                    'opname_detail_updated_by' => $userId,
                    'opname_detail_transaksi' => null,
                    'opname_detail_proses' => null,
                    'opname_detail_scan_rs' => 1,
                    'opname_detail_ketemu' => 1,
                    'opname_detail_reff' => $code,
                    'opname_detail_scan_by' => 'OPNAME',
                ];

                if ($detail !== null && (int) $detail->opname_detail_ketemu === 1) {
                    $item = array_merge($item, [
                        'opname_detail_register' => 1,
                        'opname_detail_transaksi' => $detail->opname_detail_transaksi,
                        'opname_detail_proses' => $detail->opname_detail_proses,
                        // Carbon tidak boleh bocor ke respons — legacy kirim string
                        // 'Y-m-d H:i:s' apa adanya (bukan ISO8601 UTC).
                        'opname_detail_waktu' => empty($detail->opname_detail_waktu)
                            ? null
                            : Carbon::parse($detail->opname_detail_waktu)->format('Y-m-d H:i:s'),
                        'opname_detail_sync' => 1,
                    ]);
                } else {
                    $item['opname_detail_waktu'] = $waktu;
                    $item['opname_detail_sync'] = 1;
                }

                $sent[] = $item;
            }

            return $sent;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
