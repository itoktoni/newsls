<?php

namespace App\Http\Controllers;

use App\Dao\Enums\BooleanType;
use App\Dao\Enums\MenuType;
use App\Dao\Models\SystemLink;
use App\Dao\Repositories\MenuRepository;
use App\Http\Requests\MenuRequest;
use App\Http\Requests\SortRequest;
use App\Http\Services\CreateService;
use App\Http\Services\SingleService;
use App\Http\Services\UpdateMenuService;
use Plugins\Helper;
use Plugins\Response;

class MenuController extends MasterController
{
    public function __construct(MenuRepository $repository, SingleService $service)
    {
        self::$repository = self::$repository ?? $repository;
        self::$service = self::$service ?? $service;
    }

    protected function beforeForm()
    {
        $status = BooleanType::getOptions();
        $type = MenuType::getOptions();
        $link = SystemLink::getOptions();

        $files = Helper::getControllerFile();

        self::$share = [
            'status' => $status,
            'type' => $type,
            'model' => false,
            'link' => $link,
            'file' => $files,
            'action' => [

            ],
        ];
    }

    public function postCreate(MenuRequest $request, CreateService $service)
    {
        $data = $service->save(self::$repository, $request);

        return Response::redirectBack($data);
    }

    public function postUpdate($code, MenuRequest $request, UpdateMenuService $service)
    {
        $data = $service->update(self::$repository, $request, $code);

        return Response::redirectBack($data);
    }

    public function postSort(SortRequest $request)
    {
        $data = self::$service->sort($request);

        return Response::redirectBack($data);
    }

    public function getUpdate($code)
    {
        $this->beforeForm();
        $this->beforeUpdate($code);

        $data = $this->get($code, ['has_link']);
        $selected = $data->has_link->pluck('system_link_code') ?? [];

        $action = [];
        if ($data->field_type == MenuType::Menu) {
            $action = Helper::getFunction($data->field_controller, $data->field_primary);
        }

        return moduleView(modulePathForm(), $this->share([
            'model' => $data,
            'selected' => $selected,
            'action' => $action,
        ]));
    }
}
