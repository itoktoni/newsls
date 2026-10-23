<?php

namespace App\Http\Requests;

use App\Dao\Models\Detail;
use App\Dao\Repositories\TransaksiRepository;
use App\Dao\Traits\ValidationTrait;
use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
{
    use ValidationTrait;

    private $model;

    public function __construct(TransaksiRepository $repository)
    {
        $this->model = $repository->model;
    }

    public function validation(): array
    {
        return [
            KEY => 'required',
            RS_ID => 'required',
            RFID => 'required',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            Detail::field_rs_id() => $this->rs_id,
        ]);
    }
}
