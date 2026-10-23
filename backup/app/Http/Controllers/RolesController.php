<?php

namespace App\Http\Controllers;

use App\Dao\Enums\UserLevel;
use App\Dao\Models\Rs;
use App\Dao\Models\SystemGroup;
use App\Dao\Repositories\RolesRepository;
use App\Http\Requests\RoleRequest;
use App\Http\Services\CreateService;
use App\Http\Services\SingleService;
use App\Http\Services\UpdateRoleService;
use Plugins\Response;

class RolesController extends MasterController
{
    public function __construct(RolesRepository $repository, SingleService $service)
    {
        self::$repository = self::$repository ?? $repository;
        self::$service = self::$service ?? $service;
    }

    protected function beforeForm()
    {

        $group = SystemGroup::getOptions();
        $level = UserLevel::getOptions();
        $rs = Rs::getOptions();

        self::$share = [
            'group' => $group,
            'rs' => $rs,
        ];
    }

    public function postCreate(RoleRequest $request, CreateService $service)
    {
        $data = $service->save(self::$repository, $request);

        return Response::redirectBack($data);
    }

    public function postUpdate($code, RoleRequest $request, UpdateRoleService $service)
    {
        $data = $service->update(self::$repository, $request, $code);

        return Response::redirectBack($data);
    }

    public function getUpdate($code)
    {
        $this->beforeForm();
        $this->beforeUpdate($code);

        $data = $this->get($code, ['has_group']);
        $selected = $data->has_group->pluck('system_group_code') ?? [];

        return moduleView(modulePathForm(), $this->share([
            'model' => $data,
            'selected' => $selected,
        ]));
    }
}
