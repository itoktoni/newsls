<?php

namespace App\Http\Resources;

use App\Dao\Enums\BooleanType;
use App\Dao\Enums\OpnameType;
use App\Dao\Enums\ProcessType;
use App\Dao\Enums\TransactionType;
use App\Dao\Models\ConfigLinen;
use App\Dao\Models\JenisLinen;
use App\Dao\Models\Opname;
use App\Dao\Models\OpnameDetail;
use App\Dao\Models\Outstanding;
use App\Dao\Models\Rs;
use App\Dao\Models\Ruangan;
use App\Dao\Models\Transaksi;
use App\Dao\Models\ViewDetailLinen;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\DB;

class DownloadCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $rsid = $request->rsid;

        $rs = Rs::find($rsid);
        $data_rs = [
            Rs::field_primary() => $rs->field_primary,
            Rs::field_name() => $rs->field_name,
        ];

        $ruangan = Ruangan::addSelect([DB::raw('ruangan.ruangan_id, ruangan.ruangan_nama')])
            ->join('rs_dan_ruangan', 'rs_dan_ruangan.ruangan_id', 'ruangan.ruangan_id')
            ->where('rs_id', $rsid)
            ->get();

        $rfid = ConfigLinen::select(ConfigLinen::field_primary())
            ->where(ConfigLinen::field_rs_id(), $rsid)
            ->get()
            ->pluck(ConfigLinen::field_primary())
            ->toArray() ?? [];

        $opname = Opname::with(['has_detail' => function ($query) {
            $query->where(OpnameDetail::field_ketemu(), BooleanType::YES);
        }])
            ->where(Opname::field_rs_id(), $rsid)
            ->where(Opname::field_status(), OpnameType::Proses)
            ->first();

        $sendOpname = [];
        if (! empty($opname)) {
            if ($opname->has_detail) {
                $sendOpname = $opname->has_detail->pluck(OpnameDetail::field_rfid());
            }
        }

        $outstanding = Outstanding::whereIn(Outstanding::field_primary(), $rfid)
            ->get()->mapWithKeys(function($item){
                return [$item->outstanding_rfid => $item];
            });

        $transaksi = Transaksi::whereIn(Transaksi::field_rfid(), $rfid)
            ->groupBy(Transaksi::field_rfid())
            ->get()->mapWithKeys(function($item){
                return [$item->transaksi_rfid => $item];
            });

        $check = [];

        $data = $this->collection->map(function ($item) use ($outstanding, $transaksi) {
            $tanggal = $item->view_tanggal_update;
            $status_transaction = TransactionType::BERSIH;
            $status_proses = TransactionType::BERSIH;

            if(isset($outstanding[$item->field_primary])){
                $tanggal = date('Y-m-d H:i:s');
                $out = $outstanding[$item->field_primary];
                $status_transaction = $out->field_status_transaction;
                $status_proses = $out->field_status_process;
            }

            if(!isset($transaksi[$item->field_primary])){
                $status_transaction = TransactionType::BERSIH;
            }

            return [
                'rfid' => $item->field_primary,
                'rs_id' => $item->field_rs_id,
                'rs_nama' => $item->field_rs_name,
                'ruangan_id' => $item->field_ruangan_id,
                'ruangan_nama' => $item->field_ruangan_name,
                'jenis_id' => $item->field_id,
                'jenis_nama' => $item->field_name,
                'status_transaksi' => $status_transaction,
                'status_proses' => $status_proses,
                'tanggal' => $tanggal,
            ];
        });

        return [
            'status' => true,
            'code' => 200,
            'name' => 'List',
            'message' => 'Data berhasil diambil',
            'data' => $data,
            'rs' => $data_rs,
            'ruangan' => $ruangan,
            'opname' => $sendOpname,
        ];
        // return parent::toArray($request);
    }
}
