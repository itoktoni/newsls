<?php

namespace App\Http\Controllers;

use App\Dao\Enums\UserLevel;
use App\Dao\Models\SystemRole;
use App\Dao\Models\User;
use App\Dao\Repositories\PermisionRepository;
use App\Http\Requests\GeneralRequest;
use App\Http\Requests\MenuRequest;
use App\Http\Requests\SortRequest;
use App\Http\Services\CreateService;
use App\Http\Services\SingleService;
use App\Http\Services\UpdateService;
use Plugins\Core;
use Plugins\Helper;
use Plugins\Response;

class PermissionController extends MasterController
{
    public function __construct(PermisionRepository $repository, SingleService $service)
    {
        self::$repository = self::$repository ?? $repository;
        self::$service = self::$service ?? $service;
    }

    protected function beforeForm()
    {
        $user = User::getOptions();
        $level = UserLevel::getOptions();
        $role = SystemRole::getOptions();
        $files = Helper::getControllerFile();

        self::$share = [
            'level' => $level,
            'user' => $user,
            'role' => $role,
            'model' => false,
            'file' => $files,
            'action' => [],
        ];
    }

    public function postCreate(MenuRequest $request, CreateService $service)
    {
        $data = $service->save(self::$repository, $request);

        return Response::redirectBack($data);
    }

    public function postUpdate($code, GeneralRequest $request, UpdateService $service)
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

        $data = $this->get($code, ['has_user']);
        $module = Core::getControllerName($data->field_controller);

        $data_action = Core::getMethod($data->field_controller, $module) ?? [];
        $action = $data_action->pluck('primary', 'action')->toArray();

        return moduleView(modulePathForm(), $this->share([
            'model' => $data,
            'action' => array_merge($action, [$module.'.empty' => 'Empty Data', $module.'.sort' => 'Sort Data']),
        ]));
    }
}
