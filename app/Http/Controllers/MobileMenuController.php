<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Models\MobileMenu;

class MobileMenuController extends Controller
{
    use ControllerTrait;

    public function __construct(MobileMenu $model)
    {
        $this->model = $model::getModel();
    }
}
