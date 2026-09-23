<?php

namespace App\Http\Requests;

use App\Dao\Enums\ProcessType;
use App\Dao\Enums\TransactionType;
use App\Dao\Models\Bersih;
use App\Dao\Models\Outstanding;
use App\Dao\Models\Rs;
use Illuminate\Foundation\Http\FormRequest;
use Plugins\Query;

class DeliveryRequest extends FormRequest
{
    public function rules()
    {
        return [
            RS_ID => 'required',
            STATUS_TRANSAKSI => 'required',
        ];
    }

    public function withValidator($validator)
    {
        // CASE KETIKA RFID TIDAK DITEMUKAN
        $status_transaksi = $this->status_transaksi;

        /*
        notes : status transaksi berasal dari menu desktop
        */
        if ($this->status_transaksi == TransactionType::BERSIH) {
            $status_transaksi = TransactionType::KOTOR;
        }

        $outstanding = Outstanding::where(Outstanding::field_rs_ori(), $this->rs_id)
            ->where(Outstanding::field_status_process(), ProcessType::PACKING)
            ->where(Outstanding::field_status_transaction(), $status_transaksi)
            ->count();

        $validator->after(function ($validator) use ($outstanding) {
            if ($outstanding == 0) {
                $validator->errors()->add('rfid', 'RFID belum ada yang di packing !');
            }
        });

        if ($outstanding == 0) {
            return;
        }
    }

    public function prepareForValidation()
    {
        $code = '';
        switch ($this->status_transaksi) {
            case TransactionType::BERSIH:
                $code = env('CODE_DELIVERY_BERSIH', 'BSH');
                break;
            case TransactionType::REJECT:
                $code = env('CODE_DELIVERY_RETUR', 'RJK');
                break;
            case TransactionType::REWASH:
                $code = env('CODE_DELIVERY_REWASH', 'RWS');
                break;
            case TransactionType::REGISTER:
                $code = env('CODE_DELIVERY_REGISTER', 'REG');
                break;
            default:
                $code = env('CODE_DELIVERY_BERSIH', 'BSH');
                break;
        }

        $code_rs = Rs::find($this->rs_id)->rs_code;
        $code = $code.$code_rs.date('ymd');

        $autoNumber = Query::autoNumber(Bersih::getTableName(), Bersih::field_delivery(), $code, 17);
        $this->merge([
            'code' => $autoNumber,
        ]);
    }
}
