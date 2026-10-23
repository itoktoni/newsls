<?php

namespace App\Dao\Repositories;

use App\Dao\Interfaces\CrudInterface;
use Illuminate\Database\QueryException;
use Plugins\Notes;

class MasterRepository implements CrudInterface
{
    public $model;

    public static $paginate = true;

    public function setDisablePaginate()
    {
        self::$paginate = false;

        return $this;
    }

    public function dataRepository()
    {
        $query = $this->model
            ->select($this->model->getSelectedField())
            ->sortable()->filter();

        if (request()->hasHeader('authorization')) {
            if ($paging = request()->get('paginate')) {
                return Notes::data($query->paginate($paging));
            }

            if (method_exists($this->model, 'getApiResource')) {
                return $this->model->getApiCollection($query->get());
            }

            return Notes::data($query->get());
        }

        $query = env('PAGINATION_SIMPLE') ? $query->simplePaginate(env('PAGINATION_NUMBER')) : $query->paginate(env('PAGINATION_NUMBER'));

        return $query;
    }

    public function saveRepository($request)
    {
        try {
            $activity = $this->model->create($request);

            return Notes::create($activity);
        } catch (QueryException $ex) {
            return Notes::error($ex->getMessage());
        }
    }

    public function updateRepository($request, $code)
    {
        try {
            $update = $this->model->findOrFail($code);
            $update->update($request);

            return Notes::update($update);
        } catch (QueryException $ex) {
            return Notes::error($ex->getMessage());
        }
    }

    public function deleteRepository($request)
    {
        try {
            is_array($request) ? $this->model->destroy(array_values($request)) : $this->model->destroy($request);

            return Notes::delete($request);
        } catch (QueryException $ex) {
            return Notes::error($ex->getMessage());
        }
    }

    public function singleRepository($code, $relation = false)
    {
        try {
            return $relation ? $this->model->with($relation)->findOrFail($code) : $this->model->findOrFail($code);
        } catch (QueryException $ex) {
            abort(500, $ex->getMessage());
        }
    }
}
