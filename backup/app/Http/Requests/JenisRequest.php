<?php

namespace App\Http\Requests;

use App\Dao\Models\JenisLinen;
use App\Dao\Traits\ValidationTrait;
use Illuminate\Foundation\Http\FormRequest;

class JenisRequest extends FormRequest
{
    use ValidationTrait;

    public function validation(): array
    {
        return [
            JenisLinen::field_name() => 'required',
        ];
    }
}
