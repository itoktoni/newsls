<?php

namespace App\Http\Services;

use App\Dao\Interfaces\CrudInterface;

class ShowService
{
    public function show(CrudInterface $repository, $code, $relation = false)
    {
        return $repository->singleRepository($code, $relation);
    }
}
