<?php

namespace App\Http\Requests;

use App\Dao\Enums\OpnameType;
use App\Dao\Models\Opname;
use App\Dao\Models\Rs;
use App\Dao\Traits\ValidationTrait;
use Illuminate\Foundation\Http\FormRequest;

class OpnameRequest extends FormRequest
{
    use ValidationTrait;

    public function validation(): array
    {
        return [
            Opname::field_rs_id() => 'required',
            Opname::field_start() => 'required',
            Opname::field_end() => 'required',
        ];
    }

    public function prepareForValidation()
    {
        $nama = $this->{Opname::field_name()};
        $status = $this->{Opname::field_status()};

        if (empty($nama)) {
            $rs = Rs::find($this->{Opname::field_rs_id()});
            if ($rs) {
                $nama = $rs->field_name.PHP_EOL.$this->{Opname::field_start()}.' - '.$this->{Opname::field_end()};
            }
        }

        if (empty($status)) {
            $status = OpnameType::Proses;
        }

        $this->merge([
            Opname::field_name() => $nama,
            Opname::field_status() => $status,
        ]);
    }

    public function withValidator($validator)
    {
        $duplicate = Opname::where(Opname::field_rs_id(), $this->opname_id_rs)
            ->where(Opname::field_status(), OpnameType::Proses)
            ->count();

        $status = $this->opname_status != OpnameType::Selesai ? true : false;

        $check = $duplicate && $status;

        $validator->after(function ($validator) use ($check) {
            if ($check) {
                $validator->errors()->add(Opname::field_rs_id(), 'Opname sedang dilakukan!');
            }
        });
    }
}
