<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Models\Kategori;

class KategoriController extends Controller
{
    use ControllerTrait;

    public function __construct(Kategori $model)
    {
        $this->model = $model::getModel();
    }
}
