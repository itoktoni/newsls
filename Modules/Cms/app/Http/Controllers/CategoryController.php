<?php

namespace Modules\Cms\Http\Controllers;

use Modules\Cms\Models\Category;

class CategoryController extends Controller
{
    public function __construct(Category $model)
    {
        $this->model = $model::getModel();
    }
}
