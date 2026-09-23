<?php

namespace Modules\Cms\Http\Controllers;

use App\Http\Requests\GeneralRequest;
use Modules\Cms\Models\Section;
use Modules\Cms\Models\Type;

class TypeController extends Controller
{
    public function __construct()
    {
        $this->model = new Type;
    }

    protected function share($data = [])
    {
        $sectionCounts = Section::selectRaw('content_type_id, count(*) as cnt')
            ->groupBy('content_type_id')
            ->pluck('cnt', 'content_type_id')
            ->all();

        $default = [
            'model' => $this->model,
            'typeOptions' => Type::getTypeOptions(),
            'supportsOptions' => Type::getSupportsOptions(),
            'sectionCounts' => $sectionCounts,
        ];

        return array_merge($default, $data);
    }

    public function getCreate(GeneralRequest $request)
    {
        return $this->views($this->template(), ['model' => $this->model]);
    }
}
