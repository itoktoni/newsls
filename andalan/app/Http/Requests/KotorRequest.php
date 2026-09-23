<?php

namespace App\Http\Requests;

use App\Dao\Enums\TransactionType;
use App\Dao\Repositories\TransaksiRepository;
use App\Dao\Traits\ValidationTrait;
use Illuminate\Foundation\Http\FormRequest;

class KotorRequest extends FormRequest
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
            $this->model->field_rs_id() => $this->rs_id,
            $this->model->field_status_transaction() => TransactionType::Kotor,
        ]);
    }
}
