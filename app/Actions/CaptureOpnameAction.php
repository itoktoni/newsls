<?php

namespace App\Actions;

use App\Models\DetailLinen;
use App\Models\OpnameDetail;
use App\Models\Outstanding;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class CaptureOpnameAction
{
    use AsAction;

    public function handle(\App\Models\Opname $opname): array
    {
        if (! empty($opname->opname_capture)) {
            throw new \RuntimeException('Opname sudah di capture !');
        }

        DB::beginTransaction();
        try {
            $tgl = now()->format('Y-m-d H:i:s');
            $opname->update(['opname_capture' => $tgl]);

            // Snapshot: semua linen yang ter-assign ke RS ini via config_linen + detail
            $rows = DB::table('config_linen')
                ->where('config_linen.rs_id', $opname->opname_id_rs)
                ->leftJoin('outstanding', 'config_linen.detail_rfid', '=', 'outstanding.outstanding_rfid')
                ->join('detail_linen', function ($q) {
                    $q->on('config_linen.detail_rfid', '=', 'detail_linen.detail_rfid');
                })
                ->select([
                    'config_linen.detail_rfid',
                    'outstanding.outstanding_status_transaksi',
                    'outstanding.outstanding_status_proses',
                    'outstanding.outstanding_status_hilang',
                    'outstanding.outstanding_pending_created_at',
                    'outstanding.outstanding_hilang_created_at',
                    'detail_linen.detail_updated_at',
                ])->get();

            if ($rows->isEmpty()) {
                DB::commit();
                return ['captured' => 0];
            }

            $userId = auth()->id();
            $data = [];
            foreach ($rows as $r) {
                $data[] = [
                    'opname_detail_id_opname' => $opname->opname_id,
                    'opname_detail_rfid' => $r->detail_rfid,
                    'opname_detail_transaksi' => $r->outstanding_status_transaksi ?? 'BERSIH',
                    'opname_detail_proses' => $r->outstanding_status_proses ?? 'BERSIH',
                    'opname_detail_hilang' => $r->outstanding_status_hilang ?? 'NORMAL',
                    'opname_detail_ketemu' => 0,
                    'opname_detail_scan_rs' => 0,
                    'opname_detail_sync' => 0,
                    'opname_detail_waktu' => $tgl,
                    'opname_detail_created_at' => $tgl,
                    'opname_detail_created_by' => $userId,
                    'opname_detail_updated_at' => $r->detail_updated_at ? \Carbon\Carbon::parse($r->detail_updated_at)->format('Y-m-d H:i:s') : null,
                    'opname_detail_pending_at' => $r->outstanding_pending_created_at ? \Carbon\Carbon::parse($r->outstanding_pending_created_at)->format('Y-m-d H:i:s') : null,
                    'opname_detail_hilang_at' => $r->outstanding_hilang_created_at ? \Carbon\Carbon::parse($r->outstanding_hilang_created_at)->format('Y-m-d H:i:s') : null,
                ];
            }

            foreach (array_chunk($data, 500) as $chunk) {
                OpnameDetail::insert($chunk);
            }

            DB::commit();
            return ['captured' => count($data)];
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
