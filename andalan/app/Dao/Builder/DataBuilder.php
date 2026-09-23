<?php

namespace App\Dao\Builder;

class DataBuilder
{
    public $code;

    public $name;

    public $show;

    public $class;

    public $width;

    public $sort;

    public $filter;

    public $excel;

    private static $_instance = null;

    public static function build($code)
    {
        self::$_instance = new self($code);

        return self::$_instance;
    }

    public function __construct($code)
    {
        $this->code = $code;
        $this->name = $this->name ?? str_replace('_', ' ', $code);
        $this->show = $this->show ?? true;
        $this->sort = $this->sort ?? false;
        $this->width = $this->width ?? false;
        $this->filter = $this->filter ?? false;
        $this->excel = $this->excel ?? false;
    }

    public function excel($excel = true)
    {
        $this->excel = $excel;

        return $this;
    }

    public function name($name = true)
    {
        $this->name = $name;

        return $this;
    }

    public function show($show = true)
    {
        $this->show = $show;

        return $this;
    }

    public function sort($sort = true)
    {
        $this->sort = $sort;

        return $this;
    }

    public function width($width)
    {
        $this->width = $width;

        return $this;
    }

    public function class($class)
    {
        $this->class = $class;

        return $this;
    }
}
