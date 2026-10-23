<?php

namespace App\Http\Requests;

use App\Dao\Traits\ValidationTrait;
use Illuminate\Foundation\Http\FormRequest;

class SortRequest extends FormRequest
{
    use ValidationTrait;

    public function prepareForValidation()
    {
        $this->offsetUnset('_token');
    }

    public function validation(): array
    {
        return [
            'sort' => 'required',
        ];
    }
}
