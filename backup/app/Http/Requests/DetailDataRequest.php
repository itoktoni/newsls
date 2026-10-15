<?php

namespace App\Http\Requests;

use App\Dao\Traits\ValidationTrait;
use Illuminate\Foundation\Http\FormRequest;

class DetailDataRequest extends FormRequest
{
    use ValidationTrait;

    public function validation(): array
    {
        return [
            RFID => 'required',
        ];
    }
}
