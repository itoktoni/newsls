<?php

namespace App\Http\Requests;

use App\Dao\Models\Detail;
use App\Dao\Traits\ValidationTrait;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    use ValidationTrait;

    public function validation(): array
    {
        return [
            RFID => 'required|array',
            RS_ID => 'required',
            SUPPLIER_ID => 'required',
            BAHAN_ID => 'required',
            JENIS_ID => 'required',
            STATUS_CUCI => 'required|in:"RENTAL","CUCI"',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            Detail::field_rs_id() => $this->rs_id,
            Detail::field_ruangan_id() => $this->ruangan_id ?? null,
            Detail::field_jenis_id() => $this->jenis_id,
            Detail::field_status_cuci() => $this->status_cuci,
        ]);
    }
}
