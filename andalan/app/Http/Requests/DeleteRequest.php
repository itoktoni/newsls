<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function withValidator($validator)
    {
        // $validator->after(function ($validator) {
        //     $validator->errors()->add('system_action_code', 'The title cannot contain bad words!');
        // });
    }

    public function rules()
    {
        return [
            'code' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'code' => 'Data',
        ];
    }

    public function messages()
    {
        return [
            'code.required' => 'Checkbox harus dipilih',
        ];
    }
}
