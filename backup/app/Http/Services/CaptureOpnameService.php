<?php

namespace App\Http\Services;

use App\Dao\Enums\BooleanType;
use App\Dao\Enums\HilangType;
use App\Dao\Enums\LogType;
use App\Dao\Enums\ProcessType;
use App\Dao\Enums\TransactionType;
use App\Dao\Models\ConfigLinen;
use App\Dao\Models\Detail;
use App\Dao\Models\History as ModelsHistory;
use App\Dao\Models\OpnameDetail;
use App\Dao\Models\Outstanding;
use App\Dao\Models\Rs;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Laravel\Prompts\Note;
use Plugins\Alert;
use Plugins\Notes;

class CaptureOpnameService
{
    public function save($model)
    {
        $check = false;
        try {

            DB::beginTransaction();

            $tgl = date('Y-m-d H:i:s');
            $model->opname_capture = $tgl;
            $model->save();

            $opname = $model;
            $opname_id = $opname->opname_id;

            $data_rfid = ConfigLinen::where(Rs::field_primary(), $opname->opname_id_rs)
                ->leftJoin(Outstanding::getTableName(), 'config_linen.detail_rfid', '=', 'outstanding.outstanding_rfid')
                ->join(Detail::getTableName(), function($sql){
                    $sql->on('config_linen.detail_rfid', '=', 'detail_linen.detail_rfid');
                    $sql->on('config_linen.rs_id', '=', 'detail_linen.detail_id_rs');
                })
                ->get();

            $log = [];
            if ($data_rfid->count() > 0) {
                $id = auth()->user()->id;
                foreach ($data_rfid as $item) {

                    $status_transaksi = $item->outstanding_status_transaksi ?? TransactionType::BERSIH;
                    $status_proses = $item->outstanding_status_proses ?? TransactionType::BERSIH;
                    $status_hilang = $item->outstanding_status_hilang ?? HilangType::NORMAL;

                    $ketemu = $this->checkKetemu($item);


                    //capture diambil dari detail update at
                    $data[] = [
                        OpnameDetail::field_rfid() => $item->detail_rfid,
                        OpnameDetail::field_transaksi() => $status_transaksi,
                        OpnameDetail::field_proses() => $status_proses,
                        OpnameDetail::field_status_hilang() => $status_hilang,
                        OpnameDetail::field_created_at() => $tgl,
                        OpnameDetail::field_created_by() => $id,
                        OpnameDetail::field_updated_at() => ! empty($item->detail_updated_at) ? Carbon::make($item->detail_updated_at)->format('Y-m-d H:i:s') : null,
                        OpnameDetail::field_updated_by() => $id,
                        OpnameDetail::field_waktu() => $tgl,
                        OpnameDetail::field_ketemu() => $ketemu,
                        OpnameDetail::field_opname() => $opname_id,
                        OpnameDetail::field_pending() => ! empty($item->outstanding_pending_created_at) ? Carbon::make($item->outstanding_pending_created_at)->format('Y-m-d H:i:s') : null,
                        OpnameDetail::field_hilang() => ! empty($item->outstanding_hilang_created_at) ? Carbon::make($item->outstanding_hilang_created_at)->format('Y-m-d H:i:s') : null,
                    ];
                }

                foreach (array_chunk($data, env('TRANSACTION_CHUNK')) as $save_transaksi) {
                    OpnameDetail::insert($save_transaksi);
                }

                foreach (array_chunk($log, env('TRANSACTION_CHUNK')) as $log_transaksi) {
                    ModelsHistory::insert($log_transaksi);
                }
            }
            else{
                Notes::error('data RFID tidak ditemukan');
            }

            Alert::create();

            DB::commit();

        } catch (\Throwable $th) {
            DB::rollBack();
            Alert::error($th->getMessage());
            return Notes::error($th->getMessage());
        }

        return $check;
    }

    private function checkKetemu($item)
    {

        return BooleanType::NO;

        if (in_array($item->outstanding_status_hilang, [HilangType::PENDING, HilangType::HILANG])) {
            return BooleanType::YES;
        }

        if (in_array($item->outstanding_status_transaksi, [TransactionType::REJECT, TransactionType::REWASH])) {
            return BooleanType::YES;
        }

        return BooleanType::NO;
    }
}
