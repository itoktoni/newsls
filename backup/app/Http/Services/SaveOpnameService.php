<?php

namespace App\Http\Services;

use App\Dao\Enums\BooleanType;
use App\Dao\Enums\LogType;
use App\Dao\Enums\ProcessType;
use App\Dao\Enums\TransactionType;
use App\Dao\Models\OpnameDetail;
use Illuminate\Support\Facades\DB;
use Plugins\History as PluginsHistory;
use Plugins\Notes;

class SaveOpnameService
{
    public function save($opname_id, $data)
    {
        $check = false;
        try {

            DB::beginTransaction();
            $sent = [];
            $scan_rfid = $data['rfid'];
            $data_opname = $data['opname'];
            $code = $data['code'];
            $waktu = date('Y-m-d H:i:s');
            $insert_register = $scan_rs = [];

            foreach ($scan_rfid as $rfid) {
                $detail = isset($data_opname[$rfid]) ? $data_opname[$rfid] : false;
                $item = [
                    OpnameDetail::field_rfid() => $rfid,
                    OpnameDetail::field_opname() => $opname_id,
                    OpnameDetail::field_code() => $code,
                    OpnameDetail::field_register() => BooleanType::NO,
                    OpnameDetail::field_updated_at() => date('Y-m-d H:i:s'),
                    OpnameDetail::field_updated_by() => auth()->user()->id,
                    OpnameDetail::field_transaksi() => TransactionType::UNKNOWN,
                    OpnameDetail::field_proses() => ProcessType::UNKNOWN,
                    OpnameDetail::field_scan_rs() => BooleanType::YES,
                    OpnameDetail::field_ketemu() => BooleanType::YES,
                    OpnameDetail::field_reff() => $code,
                    OpnameDetail::field_scan_by() => LogType::OPNAME,
                ];

                if (! $detail) {
                    $item = array_merge($item, [
                        OpnameDetail::field_waktu() => $waktu,
                    ]);

                    $insert_register[] = $item;
                    $item = array_merge($item, [
                        OpnameDetail::field_sync() => BooleanType::YES,
                    ]);

                } elseif ($detail->opname_detail_ketemu == BooleanType::YES) {
                    $item = array_merge($item, [
                        OpnameDetail::field_register() => BooleanType::YES,
                        OpnameDetail::field_transaksi() => $detail->opname_detail_transaksi,
                        OpnameDetail::field_proses() => $detail->opname_detail_proses,
                        OpnameDetail::field_scan_rs() => BooleanType::YES,
                        OpnameDetail::field_ketemu() => BooleanType::YES,
                        OpnameDetail::field_waktu() => $detail->opname_detail_waktu,
                        OpnameDetail::field_sync() => BooleanType::YES,
                    ]);
                } elseif ($detail->opname_detail_ketemu == BooleanType::NO) {
                    $item = array_merge($item, [
                        OpnameDetail::field_waktu() => $waktu,
                        OpnameDetail::field_sync() => BooleanType::YES,
                    ]);

                    $scan_rs[] = $rfid;
                }

                $sent[] = $item;
            }

            if (!empty($insert_register)) {
                OpnameDetail::insert($insert_register);
            }

            if (!empty($scan_rs)) {
                OpnameDetail::whereIn(OpnameDetail::field_rfid(), $scan_rs)
                    ->where(OpnameDetail::field_opname(), $opname_id)
                    ->update([
                        OpnameDetail::field_ketemu() => BooleanType::YES,
                        OpnameDetail::field_scan_rs() => BooleanType::YES,
                        OpnameDetail::field_waktu() => $waktu,
                    ]);

                PluginsHistory::bulk($scan_rs, LogType::OPNAME, 'Ketemu ketika Opname');
            }

            DB::commit();

            return Notes::create($sent);

        } catch (\Throwable $th) {
            DB::rollBack();

            return Notes::error($th->getMessage());
        }

        return Notes::error('Unknown');
    }
}
