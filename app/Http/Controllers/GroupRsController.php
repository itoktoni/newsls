<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Models\GroupRs;

class GroupRsController extends Controller
{
    use ControllerTrait;

    public function __construct(GroupRs $model)
    {
        $this->model = $model::getModel();
    }
}
