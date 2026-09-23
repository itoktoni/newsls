<?php

namespace App\Http\Requests;

use App\Dao\Enums\ProcessType;
use App\Dao\Enums\TransactionType;
use App\Dao\Models\Bersih;
use App\Dao\Models\ConfigLinen;
use App\Dao\Models\Detail;
use App\Dao\Models\Outstanding;
use App\Dao\Models\Rs;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PackingRequest extends FormRequest
{
    public function rules()
    {
        return [
            RFID => 'required|array',
            RS_ID => 'required',
            RUANGAN_ID => 'required',
            STATUS_TRANSAKSI => 'required',
        ];
    }

    public function withValidator($validator)
    {
        $total = count($this->rfid);
        // CASE KETIKA RFID TIDAK DITEMUKAN
        $status_transaksi = $this->status_transaksi;

        /*
        notes : status transaksi berasal dari menu desktop
        */
        if ($this->status_transaksi == TransactionType::BERSIH) {
            $status_transaksi = TransactionType::KOTOR;
        }

        $rfid = Outstanding::whereIn(Outstanding::field_primary(), $this->rfid)
            ->where(Outstanding::field_status_process(), '!=', ProcessType::PACKING)
            ->where(Outstanding::field_status_transaction(), $status_transaksi)->count();

        $compare = $total !=  $rfid;

        $validator->after(function ($validator) use ($compare) {
            if ($compare) {
                $validator->errors()->add('rfid', 'RFID dengan status ready packing tidak ditemukan !');
            }
        });

        if ($compare) {
            return;
        }

        // CASE KETIKA RFID TIDAK ADA DI CONFIG

        $check_config = DB::table('config_linen')
            ->leftJoin(Detail::getTableName(), function($sql){
                $sql->on('config_linen.detail_rfid', '=', 'detail_linen.detail_rfid');
                $sql->on('config_linen.rs_id', '=', 'detail_linen.detail_id_rs');
            })
            ->where(ConfigLinen::field_rs_id(), $this->rs_id)
            ->whereIn('config_linen.'.ConfigLinen::field_primary(), $this->rfid)
            ->count();

        $comp = $check_config != $total;

        $validator->after(function ($validator) use ($comp) {
            if ($comp) {
                $validator->errors()->add('rfid', 'Status Kepemilikan RFID Bermasalah !');
            }
        });

        if ($comp) {
            return;
        }

        // CASE KETIKA RFID SUDAH BERSIH

        $check_bersih = Detail::whereIn(Detail::field_primary(), $this->rfid)
            ->where(Detail::field_status_linen(), TransactionType::BERSIH)
            ->count();

        $validator->after(function ($validator) use ($check_bersih) {
            if ($check_bersih > 0) {
                $validator->errors()->add('rfid', 'status RFID sudah bersih !');
            }
        });

        if ($compare) {
            return;
        }

        // CASE YANG DIBARCODE LEBIH DARI YANG DITENTUKAN
        $validator->after(function ($validator) use ($total) {
            $maksimal = env('TRANSACTION_BARCODE_MAXIMAL', 10);
            if ($total > $maksimal) {
                $validator->errors()->add('rfid', 'RFID maksimal '.$maksimal);
            }
        });
    }

    public function prepareForValidation()
    {
        $code = Str::orderedUuid()->toString();
        $date = date('Y-m-d H:i:s');
        $user = auth()->user()->id;

        $rs_name = Rs::find($this->rs_id)->field_name ?? '';

        $bersih = [];
        foreach($this->rfid as $item){
            $bersih[] = [
                Bersih::field_barcode() => $code,
                Bersih::field_rfid() => $item,
                Bersih::field_rs_id() => $this->rs_id,
                Bersih::field_ruangan_id() => $this->ruangan_id,
                Bersih::field_status() => $this->status_transaksi,
                Bersih::field_created_at() => $date,
                Bersih::field_created_by() => $user,
                Bersih::field_updated_at() => $date,
                Bersih::field_updated_by() => $user,
            ];
        }

        $this->merge([
            'uuid' => $code,
            'bersih' => $bersih,
            'rs_name' => $rs_name
        ]);
    }
}
