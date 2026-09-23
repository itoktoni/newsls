<?php

namespace App\Dao\Builder;

class OptionBuilder
{
    public $model;

    public $name;

    public $id;

    private static $_instance = null;

    public static function build($model)
    {
        self::$_instance = new self($model);

        return self::$_instance;
    }

    public function __construct($model)
    {
        $this->model = empty($this->model) ? new $model() : $this->model;
    }

    public function setName($name = true)
    {
        $this->name = $name;

        return $this;
    }

    public function setId($id = true)
    {
        $this->id = $id;

        return $this;
    }

    public function generate()
    {
        return $this->model->get();
    }
}
