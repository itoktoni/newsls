<?php

namespace App\Dao\Facades;

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Str;
use Plugins\Helper;

class RoutesFacades extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Str::snake(Helper::getClass(__CLASS__));
    }
}
