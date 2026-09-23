<?php

namespace App\Http\Requests;

use App\Dao\Models\SystemMenu;
use App\Dao\Traits\ValidationTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class MenuRequest extends FormRequest
{
    use ValidationTrait;

    public function validation(): array
    {
        return [
            'system_menu_name' => 'required|unique:system_menu|min:1',
            'system_menu_type' => 'required',
        ];
    }

    public function prepareForValidation()
    {
        $merge = [];
        if (empty($this->{SystemMenu::field_url()})) {
            $merge = [
                SystemMenu::field_url() => $this->{SystemMenu::field_url()} ?? Str::snake($this->{SystemMenu::field_name()}),
            ];
        }

        $this->merge($merge);
    }
}
