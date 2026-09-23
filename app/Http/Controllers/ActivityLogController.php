<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Models\Activity;

class ActivityLogController extends Controller
{
    use ControllerTrait;

    public function __construct(Activity $model)
    {
        $this->model = $model::getModel();
    }

    protected function share($data = [])
    {
        $default = [
            'model' => $this->model,
            'logNameOptions' => Activity::pluck('log_name')->filter()->unique()->values()->all(),
        ];

        return array_merge($default, $data);
    }

    protected function getData()
    {
        // ponytail: causer/subject are MorphTo (polymorphic) — PowerJoins
        // leftJoinRelationship() requires a concrete $morphable class and fatals
        // with null. Nothing sorts/filters on joined columns here, so eager-load.
        $query = $this->model
            ->with(['causer', 'subject'])
            ->filter()
            ->sort();

        if (request('log_name')) {
            $query->where('log_name', request('log_name'));
        }

        return $query;
    }
}
