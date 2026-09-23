<?php

namespace Modules\Cms\Http\Controllers;

use Modules\Cms\Models\Tag;

class TagController extends Controller
{
    public function __construct(Tag $model)
    {
        $this->model = $model::getModel();
    }
}
