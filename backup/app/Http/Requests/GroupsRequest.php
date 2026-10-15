<?php

namespace App\Http\Requests;

use App\Dao\Models\SystemGroup;
use App\Dao\Traits\ValidationTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class GroupsRequest extends FormRequest
{
    use ValidationTrait;

    public function prepareForValidation()
    {
        $this->merge([
            SystemGroup::field_primary() => Str::snake($this->system_group_name),
        ]);
    }

    public function validation(): array
    {
        return [
            'system_group_name' => 'required|min:3|unique:system_group',
        ];
    }
}
