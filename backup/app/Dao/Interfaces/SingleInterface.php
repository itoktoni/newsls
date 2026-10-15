<?php

namespace App\Dao\Interfaces;

interface SingleInterface
{
    public function singleRepository($code, $relation = false);
}
