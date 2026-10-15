<?php

namespace App\Http\Controllers;

use App\Dao\Enums\BooleanType;
use App\Http\Requests\SettingRequest;
use App\Http\Services\CreateSettingService;
use Plugins\Response;

class SettingController extends Controller
{
    protected function share($data = [])
    {
        $view = [
            'model' => false,
            'active' => BooleanType::getOptions(),
        ];

        return array_merge($view, $data);
    }

    public function getCreate()
    {
        return moduleView('pages.setting.form', $this->share());
    }

    public function postCreate(SettingRequest $request, CreateSettingService $service)
    {
        $data = $service->save($request);

        return Response::redirectBack($data);
    }
}
