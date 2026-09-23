<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Models\Supplier;

class SupplierController extends Controller
{
    use ControllerTrait;

    public function __construct(Supplier $model)
    {
        $this->model = $model::getModel();
    }
}
