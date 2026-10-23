<?php

namespace App\Http\Controllers;

use App\Dao\Enums\BooleanType;
use App\Http\Requests\DeleteRequest;
use App\Http\Services\DeleteService;
use Plugins\Response;
use Plugins\SharedData;
use Plugins\Template;

class ReportController extends Controller
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
        ];

        return self::$share = array_merge($view, $data, self::$share);
    }

    public function getData()
    {
        $query = self::$repository->dataRepository();

        return $query;
    }

    public function getTable()
    {
        $data = $this->getData();

        return view(Template::table(SharedData::get('template')))->with($this->share([
            'data' => $data,
            'fields' => self::$repository->model->getShowField(),
        ]));
    }

    public function getCreate()
    {
        $this->beforeForm();
        $this->beforeCreate();

        return view(Template::form(SharedData::get('template')))->with($this->share());
    }

    public function getUpdate($code)
    {
        $this->beforeForm();
        $this->beforeUpdate($code);

        return view(Template::form(SharedData::get('template')))->with($this->share([
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

    public function postDelete(DeleteRequest $request, DeleteService $service)
    {
        $code = $request->get('code');
        $data = $service->delete(self::$repository, $code);

        return Response::redirectBack($data);
    }
}
