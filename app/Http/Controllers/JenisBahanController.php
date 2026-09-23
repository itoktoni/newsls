<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Models\JenisBahan;

class JenisBahanController extends Controller
{
    use ControllerTrait;

    public function __construct(JenisBahan $model)
    {
        $this->model = $model::getModel();
    }
}
