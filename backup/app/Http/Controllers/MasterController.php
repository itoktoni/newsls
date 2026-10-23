<?php

namespace App\Http\Controllers;

use App\Dao\Enums\BooleanType;
use App\Http\Requests\DeleteRequest;
use App\Http\Services\DeleteService;
use Plugins\Response;

class MasterController extends Controller
{
    public static $service;

    public static $repository;

    public static $template;

    public static $share = [];

    protected function beforeForm()
    {
    }

    protected function beforeCreate()
    {
    }

    protected function beforeUpdate($code)
    {
    }

    protected function share($data = [])
    {
        $status = BooleanType::getOptions();
        $view = [
            'status' => $status,
            'model' => false,
        ];

        return self::$share = array_merge($view, self::$share, $data);
    }

    public function getData()
    {
        $query = self::$repository->dataRepository();

        return $query;
    }

    public function getDelete()
    {
        $code = request()->get('code');
        $data = self::$service->delete(self::$repository, $code);

        return Response::redirectBack($data);
    }

    public function postDelete(DeleteRequest $request, DeleteService $service)
    {
        $code = $request->get('code');
        $data = $service->delete(self::$repository, $code);

        return Response::redirectBack($data);
    }

    public function getTable()
    {
        $data = $this->getData();

        return moduleView(modulePathTable(), [
            'data' => $data,
            'fields' => self::$repository->model->getShowField(),
        ]);
    }

    public function postTable()
    {
        if (request()->exists('delete')) {
            $code = array_unique(request()->get('code'));
            $data = self::$service->delete(self::$repository, $code);
        }

        if (request()->exists('sort')) {
            $sort = array_unique(request()->get('sort'));
            $data = self::$service->sort(self::$repository, $sort);
        }

        return Response::redirectBack($data);
    }

    public function getCreate()
    {
        $this->beforeForm();
        $this->beforeCreate();

        return moduleView(modulePathForm(), $this->share());
    }

    public function getUpdate($code)
    {
        $this->beforeForm();
        $this->beforeUpdate($code);

        return moduleView(modulePathForm(), $this->share([
            'model' => $this->get($code),
        ]));
    }

    public function get($code = null, $relation = null)
    {
        $relation = $relation ?? request()->get('relation');
        if ($relation) {
            return self::$service->get(self::$repository, $code, $relation);
        }

        return self::$service->get(self::$repository, $code);
    }
}
