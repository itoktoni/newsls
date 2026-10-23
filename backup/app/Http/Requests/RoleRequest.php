<?php

namespace App\Http\Requests;

use App\Dao\Traits\ValidationTrait;
use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
{
    use ValidationTrait;

    public function validation(): array
    {
        return [
            'system_role_name' => 'required|min:3',
        ];
    }
}
