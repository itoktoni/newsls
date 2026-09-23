<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Models\Ruangan;

class RuanganController extends Controller
{
    use ControllerTrait;

    public function __construct(Ruangan $model)
    {
        $this->model = $model::getModel();
    }
}
