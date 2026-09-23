<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GeneralRequest extends FormRequest
{
    public function authorize(): bool
    {
        $controller = request()->route()->getController();
        $model = $controller->model ?? null;

        // ponytail: JSON-store controllers don't set $this->model; AccessMiddleware gates by role.
        if ($model === null) {
            return true;
        }

        $action = str_replace(['get', 'post'], '', strtolower(request()->route()->getActionMethod()));
        $action = $action === 'index' ? 'table' : $action;

        return $this->user()->can($action, $model);
    }

    public function rules(): array
    {
        return [];
    }
}
