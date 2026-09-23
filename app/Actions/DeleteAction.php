<?php

namespace App\Actions;

use App\Concerns\PayloadTrait;
use App\Concerns\RulesTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteAction
{
    use AsAction, PayloadTrait, RulesTrait;

    public function rules(): array
    {
        return [
            'ids' => 'required|array',
        ];
    }

    public function handle(Request $request, $model)
    {
        $this->model = $model;
        $data = $request->validate($this->rules());

        try {

            $model->whereIn($model->field_primary(), $data['ids'])->delete();

            return $this->payload(TOAST_SUCCESS, $data['ids']);

        } catch (\Throwable $th) {
            return $this->payload(TOAST_FAILED, $th->getMessage());
        }
    }

    public function remove($id, $model)
    {
        $validator = Validator::make(['id' => request()->input('id', $id)], ['id' => 'required']);

        if ($validator->fails()) {
            $errors = $validator->errors();

            return $this->payload($validator->errors()->first(), $errors);
        }

        try {
            $model->findOrFail($id)->delete();

            return $this->payload(TOAST_SUCCESS, ['id' => $id]);

        } catch (\Throwable $th) {
            return $this->payload(TOAST_FAILED, $th->getMessage());
        }
    }
}
