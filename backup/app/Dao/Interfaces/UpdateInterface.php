<?php

namespace App\Dao\Interfaces;

interface UpdateInterface
{
    public function updateRepository($request, $where);
}
