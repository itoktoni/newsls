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
            'logNameOptions' => Activity::query()
                ->distinct()->pluck('log_name')->filter()->values()
                ->mapWithKeys(fn ($v) => [$v => $v])->all(),
            // ponytail: opsi Event untuk dropdown filter (created/updated/deleted + custom).
            'eventOptions' => Activity::query()
                ->distinct()->pluck('event')->filter()->values()
                ->mapWithKeys(fn ($v) => [$v => $v])->all()
                + ['created' => 'created', 'updated' => 'updated', 'deleted' => 'deleted'],
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

        // ponytail: default terbaru dulu bila user belum memilih sort.
        if (! request('sort')) {
            $query->orderByDesc('id');
        }

        return $query;
    }
}
