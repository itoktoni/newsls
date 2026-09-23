<?php

namespace App\Http\Requests;

use App\Dao\Models\SystemLink;
use App\Dao\Traits\ValidationTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class LinkRequest extends FormRequest
{
    use ValidationTrait;

    public function validation(): array
    {
        return [
            'system_link_name' => 'required|unique:system_link|min:1',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            SystemLink::field_primary() => $this->{SystemLink::field_primary()} ?? Str::snake($this->{SystemLink::field_name()}),
            SystemLink::field_url() => $this->{SystemLink::field_url()} ?? Str::snake($this->{SystemLink::field_name()}),
        ]);
    }
}
