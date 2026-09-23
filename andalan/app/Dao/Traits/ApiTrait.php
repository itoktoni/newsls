<?php

namespace App\Dao\Traits;

use Plugins\Notes;

trait ApiTrait
{
    abstract public function apiTransform();

    public function getApiResource($query)
    {

        return Notes::data($this->apiTransform()::make($query->first()));
    }

    public function getApiCollection($query)
    {

        return Notes::data($this->apiTransform()::collection($query));
    }

    public static function getTableName()
    {
        return with(new static)->getTable();
    }
}
