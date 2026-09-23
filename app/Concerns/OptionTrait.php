<?php

namespace App\Concerns;

trait OptionTrait
{
    public static $option_data;

    public static $option_model;

    public static $option_query;

    public static $option_label;

    public static $option_name;

    public static $option_id;

    public static function getOptions($raw = false)
    {
        self::$option_model = self::getModel();
        $query = self::$option_model->query();

        if (is_bool($raw) && $raw) {
            return $query->get();
        } elseif (is_array($raw)) {

            $field_id = array_keys($raw)[0];
            $field_name = array_values($raw)[0];

            $query = $query->select($field_id, $field_name);
            self::$option_model = $query->get()->pluck($field_name, $field_id) ?? [];

        } else {

            $query = $query->select(self::$option_model->field_name(), self::$option_model->getKeyName());
            self::$option_model = $query->get()->pluck(self::$option_model->field_name(), self::$option_model->getKeyName()) ?? [];
        }

        return self::$option_model;
    }
}
