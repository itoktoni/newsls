<?php

namespace App\Http\Requests;

use App\Dao\Traits\ValidationTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class UserRequest extends FormRequest
{
    use ValidationTrait;

    public function validation(): array
    {
        return [
            'name' => 'required|min:2',
            'username' => 'required|min:2',
            'email' => 'required|email|unique:users',
        ];
    }

    public function prepareForValidation()
    {
        if ($this->password) {
            $this->merge([
                'password' => Hash::make($this->password),
            ]);
        } else {
            $this->offsetUnset('password');
        }

        $this->merge([
            'active' => 1,
        ]);
    }
}
