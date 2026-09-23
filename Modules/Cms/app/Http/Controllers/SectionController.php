<?php

namespace Modules\Cms\Http\Controllers;

use App\Http\Requests\GeneralRequest;
use Modules\Cms\Models\Field;
use Modules\Cms\Models\Section;
use Modules\Cms\Models\Type;

class SectionController extends Controller
{
    public function __construct()
    {
        $this->model = new Section;
    }

    protected function share($data = [])
    {
        $topFields = Field::whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        $default = [
            'model' => $this->model,
            'contentTypes' => Type::pluck('name', 'id')->toArray(),
            'allFields' => $topFields,
        ];

        return array_merge($default, $data);
    }

    public function getCreate(GeneralRequest $request)
    {
        return $this->views($this->template());
    }
}
