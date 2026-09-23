<?php

namespace Modules\Cms\Http\Controllers;

use Modules\Cms\Models\Menu;

class MenuController extends Controller
{
    public function __construct(Menu $model)
    {
        $this->model = $model::getModel();
    }
}
