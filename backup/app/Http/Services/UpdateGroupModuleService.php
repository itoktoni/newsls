<?php

namespace App\Http\Services;

use App\Dao\Facades\ActionFacades;
use App\Dao\Facades\ModuleFacades;
use App\Dao\Interfaces\CrudInterface;
use App\Plugins\Alert;
use App\Plugins\Helper;
use Illuminate\Support\Str;

class UpdateGroupModuleService extends UpdateService
{
    public $visible = [
        'create', 'list', 'create', 'index', 'stock', 'report', 'payment', 'action',
    ];

    public $method = [
        'create' => 'GET',
        'save' => 'POST',
        'list' => 'GET',
        'data' => 'POST',
        'edit' => 'GET',
        'update' => 'POST',
        'delete' => 'POST',
        'payment' => 'GET',
        'action' => 'POST',
        'index' => 'GET',
        'report' => 'GET',
        'show' => 'GET',
        'get' => 'GET',
        'patch' => 'POST',
        'master' => 'POST',
        'batch' => 'POST',
        'download' => 'GET',
    ];

    public $api = [
        'save' => 1,
        'data' => 1,
        'update' => 1,
        'delete' => 1,
        'get' => 1,
        'patch' => 1,
        'master' => 1,
        'batch' => 1,
        'download' => 1,
        'action' => 1,
    ];

    public function update(CrudInterface $repository, $data, $code)
    {
        $check = $repository->updateRepository($data->all(), $code);
        if ($check['status']) {
            $getData = $check['data'] ?? [];
            if ($mod = $data->get('groups')) {
                $getData->module()->delete();
                foreach ($mod as $module => $func) {

                    $snake = Str::snake($module);
                    $code = $getData->system_group_module_code.'_'.$snake;
                    $moduleName = ucwords(str_replace('_', ' ', $snake));
                    $path = '\Modules\\'.ucfirst($getData->system_group_module_folder).'\Http\Controllers\\'.$module.'Controller';

                    $object = ModuleFacades::create([
                        'system_module_code' => $code,
                        'system_module_name' => $moduleName,
                        'system_module_link' => $getData->system_group_module_code.'/'.$snake,
                        'system_module_controller' => $module,
                        'system_module_sort' => 1,
                        'system_module_show' => $moduleName == 'Home' ? 0 : 1,
                        'system_module_enable' => 1,
                        'system_module_module' => 1,
                        'system_module_folder' => $getData->system_group_module_folder,
                        'system_module_path' => $path,
                        'system_module_api' => 1,
                    ]);

                    ActionFacades::where(ActionFacades::getModuleKeyName(), $code)->delete();
                    foreach ($func['real'] as $function) {
                        $visible = '0';
                        if (in_array($function, $this->visible)) {
                            $visible = '1';
                        }

                        $split = explode('_', $function);
                        $name = ucwords(str_replace('_', ' ', $function)).' '.$module;
                        if (count($split) > 1) {
                            $name = ucwords(str_replace('_', ' ', Str::snake($function)));
                        }
                        $name = str_replace('Index', 'List', $name);

                        $pathAction = '\Modules\\'.ucfirst($getData->system_group_module_folder).'\Http\Controllers\\'.$module.'Controller';

                        $metode = $this->method[$function] ?? 'GET';
                        if (in_array($function, $this->visible)) {
                            $visible = '1';
                        }

                        if (strpos($code, 'report') !== false) {

                            $visible = '1';
                            $metode = 'GET';

                            if (strpos(strtolower($function), 'export') !== false) {
                                $visible = '0';
                                $metode = 'GET';
                            }
                        }

                        if (strpos(strtolower($function), 'form') !== false) {
                            $visible = '0';
                            $metode = 'GET';
                        }

                        if (strpos(strtolower($function), 'index') !== false) {
                            $visible = '1';
                            $metode = 'GET';
                        }

                        if (strpos(strtolower($function), 'post') !== false) {
                            $visible = '0';
                            $metode = 'POST';
                        }

                        if (strpos(strtolower($function), 'data') !== false) {
                            $visible = '0';
                            $metode = 'POST';
                        }

                        if (strpos(strtolower($function), 'list') !== false) {
                            $visible = '1';
                            $metode = 'GET';
                        }

                        if (strpos(strtolower($function), 'print') !== false) {
                            $visible = '0';
                            $metode = 'GET';
                        }

                        ActionFacades::create([
                            'system_action_code' => $code.'_'.Str::snake($function),
                            'system_action_module' => $code,
                            'system_action_name' => ucwords(str_replace('_', ' ', Str::snake($name))),
                            'system_action_link' => $code.'/'.Str::snake($function),
                            'system_action_controller' => $module,
                            'system_action_function' => Helper::snake($function),
                            'system_action_sort' => 1,
                            'system_action_show' => $visible,
                            'system_action_enable' => 1,
                            'system_action_path' => $pathAction,
                            'system_action_method' => $metode,
                            'system_action_api' => $this->api[$function] ?? 0,
                        ]);
                    }
                    if (isset($func['data'])) {
                        $list_action = [];
                        foreach ($func['data'] as $method) {
                            $list_action[] = $code.'_'.Str::snake($method);
                        }
                        $object->connection_action()->sync($list_action);
                    }

                }

                $list_module = [];
                foreach ($data->get('module') as $mod) {

                    $list_module[] = $getData->system_group_module_code.'_'.Str::snake($mod);
                }

                $getData->connection_module()->sync($list_module);
            }
        }

        if ($check['status']) {
            Alert::update();
        } else {
            Alert::error($check['message']);
        }

        return $check;
    }
}
