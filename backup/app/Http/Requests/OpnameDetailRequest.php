<?php

namespace App\Http\Requests;

use App\Dao\Models\Opname;
use App\Dao\Models\OpnameDetail;
use App\Dao\Traits\ValidationTrait;
use Illuminate\Foundation\Http\FormRequest;

class OpnameDetailRequest extends FormRequest
{
    use ValidationTrait;

    public function validation(): array
    {
        return [
            Opname::field_primary() => 'required',
            'code' => 'required',
            'rfid' => 'required|array',
        ];
    }

    public function prepareForValidation()
    {
        $send = [];
        $data = OpnameDetail::whereIn(OpnameDetail::field_rfid(), $this->rfid)
            ->where(OpnameDetail::field_opname(), $this->opname_id)
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->opname_detail_rfid => $item];
            });

        $send['opname'] = $data;
        $send['rfid'] = $this->rfid;
        $send['code'] = $this->code;

        $this->merge([
            'data' => $send,
        ]);
    }
}
