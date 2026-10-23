<?php

namespace App\Dao\Traits;

trait ValidationTrait
{
    abstract public function validation(): array;

    public function rules()
    {
        return $this->filterValidation();
    }

    public function filterValidation()
    {
        if (request()->segment(5) == 'update') {

            $collection = collect($this->validation())->map(function ($item, $key) {
                if (strpos($item, 'unique') !== false) {
                    $string = explode('|', $item);
                    $builder = '';
                    foreach ($string as $value) {
                        if (strpos($value, 'unique') === false) {
                            $builder = $builder.$value.'|';
                        }
                    }
                    $key = rtrim($builder, '|');
                } else {
                    $key = $item;
                }

                return $key;
            });

            return $collection->toArray();
        }

        if (request()->segment(5) == 'delete') {

            return [
                'code' => 'required',
            ];
        }

        return $this->validation();
    }
}
