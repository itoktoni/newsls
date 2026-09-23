<?php

namespace App\Http\Requests;

use App\Dao\Traits\ValidationTrait;
use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
{
    use ValidationTrait;

    public function validation(): array
    {
        return [
            'name' => 'required|min:3',
        ];
    }
}
