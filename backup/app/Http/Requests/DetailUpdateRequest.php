<?php

namespace App\Http\Requests;

use App\Dao\Models\Detail;
use App\Dao\Traits\ValidationTrait;
use Illuminate\Foundation\Http\FormRequest;

class DetailUpdateRequest extends FormRequest
{
    use ValidationTrait;

    public function validation(): array
    {
        return [
            RS_ID => 'required',
            RUANGAN_ID => 'required',
            JENIS_ID => 'required',
            STATUS_CUCI => 'in:0,1,2',
            STATUS_REGISTER => 'in:0,1',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            Detail::field_jenis_id() => $this->jenis_id,
            Detail::field_rs_id() => $this->rs_id,
            Detail::field_ruangan_id() => $this->ruangan_id,
        ]);
    }
}
