<?php

namespace App\Concerns;

trait RulesTrait
{
    public $model;

    public function mergeRules($model = null)
    {
        $rules = [];

        if (! empty($model) && method_exists($model, 'rules')) {
            $rules = $model->rules();
        }

        return $rules;
    }
}
