<?php

namespace App\Http\Requests;

use App\Dao\Models\Opname;
use App\Dao\Traits\ValidationTrait;
use Illuminate\Foundation\Http\FormRequest;

class OpnameReportRequest extends FormRequest
{
    use ValidationTrait;

    public function validation(): array
    {
        return [
            Opname::field_primary() => 'required',
        ];
    }
}
