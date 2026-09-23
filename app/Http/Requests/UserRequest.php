<?php

namespace App\Http\Requests;

class UserRequest extends GeneralRequest
{
    public function prepareForValidation()
    {
        $merge = [
            'password' => '',
        ];

        $this->merge($merge);
    }
}
