<?php

namespace App\Http\Requests;

use App\Dao\Enums\TransactionType;
use App\Dao\Models\Detail;
use App\Dao\Models\Rs;
use App\Dao\Models\Ruangan;
use App\Dao\Models\Transaksi;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Plugins\Query;

class BarcodeRequest extends FormRequest
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

        $where = TransactionType::Register;

        /*
        notes : status transaksi berasal dari menu desktop
        */
        if ($this->status_transaksi == TransactionType::BersihKotor) {
            $where = TransactionType::Kotor;
        } elseif ($this->status_transaksi == TransactionType::BersihRetur) {
            $where = TransactionType::Retur;
        } elseif ($this->status_transaksi == TransactionType::BersihRewash) {
            $where = TransactionType::Rewash;
        } elseif ($this->status_transaksi == TransactionType::Kotor) {
            $where = TransactionType::Kotor;
        } elseif ($this->status_transaksi == TransactionType::Retur) {
            $where = TransactionType::Retur;
        } elseif ($this->status_transaksi == TransactionType::Rewash) {
            $where = TransactionType::Rewash;
        }

        $rfid = Detail::whereIn(Detail::field_primary(), $this->rfid)
            ->where(Detail::field_status_transaction(), $where)
            ->where(Detail::field_rs_id(), $this->rs_id)
            ->where(Detail::field_ruangan_id(), $this->ruangan_id);

        $total_rfid_original = $rfid->count();

        $compare = $total != $total_rfid_original;

        $validator->after(function ($validator) use ($compare) {
            if ($compare) {
                $validator->errors()->add('rfid', 'RFID tidak sesuai dengan proses !');
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
        $code = '';

        switch ($this->status_transaksi) {
            case TransactionType::BersihKotor:
                $code = env('CODE_KOTOR', 'KTR');
                break;
            case TransactionType::BersihRetur:
                $code = env('CODE_REJECT', 'RTR');
                break;
            case TransactionType::BersihRewash:
                $code = env('CODE_REWASH', 'WSH');
                break;
            case TransactionType::Kotor:
                $code = env('CODE_KOTOR', 'KTR');
                break;
            case TransactionType::Retur:
                $code = env('CODE_REJECT', 'RTR');
                break;
            case TransactionType::Rewash:
                $code = env('CODE_REWASH', 'WSH');
                break;
            case TransactionType::Register:
                $code = env('CODE_REGISTER', 'BRU');
                break;
            default:
                $code = env('CODE_BERSIH', 'BSH');
                break;
        }

        $code_ruangan = Ruangan::find($this->ruangan_id)->ruangan_code;
        $code_rs = Rs::find($this->rs_id)->rs_code;

        $code = $code.$code_rs.$code_ruangan.date('ymd');

        $autoNumber = Query::autoNumber(Transaksi::getTableName(), Transaksi::field_barcode(), $code, 22);

        $this->merge([
            'code' => $autoNumber,
            'uuid' => Str::uuid(),
        ]);
    }
}
