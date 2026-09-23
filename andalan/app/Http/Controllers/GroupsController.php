<?php

namespace App\Http\Controllers;

use App\Dao\Models\SystemMenu;
use App\Dao\Repositories\GroupsRepository;
use App\Http\Requests\GroupsRequest;
use App\Http\Services\CreateService;
use App\Http\Services\SingleService;
use App\Http\Services\UpdateGroupService;
use Plugins\Response;

class GroupsController extends MasterController
{
    public function __construct(GroupsRepository $repository, SingleService $service)
    {
        self::$repository = self::$repository ?? $repository;
        self::$service = self::$service ?? $service;
    }

    public function postCreate(GroupsRequest $request, CreateService $service)
    {
        $data = $service->save(self::$repository, $request);

        return Response::redirectBack($data);
    }

    public function postUpdate($code, GroupsRequest $request, UpdateGroupService $service)
    {
        $data = $service->update(self::$repository, $request, $code);

        return Response::redirectBack($data);
    }

    protected function beforeForm()
    {

        $menu = SystemMenu::getOptions();

        self::$share = [
            'menu' => $menu,
        ];
    }

    public function getUpdate($code)
    {
        $this->beforeForm();
        $this->beforeUpdate($code);

        $data = $this->get($code, ['has_menu', 'has_menu.has_link']);
        $selected = $data->has_menu->pluck('system_menu_code') ?? [];

        return moduleView(modulePathForm(), $this->share([
            'model' => $data,
            'selected' => $selected,
        ]));
    }
}
