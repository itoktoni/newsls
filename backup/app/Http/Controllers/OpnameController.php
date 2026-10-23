<?php

namespace App\Http\Controllers;

use App\Dao\Enums\OpnameType;
use App\Dao\Models\OpnameDetail;
use App\Dao\Models\Rs;
use App\Dao\Repositories\OpnameRepository;
use App\Http\Requests\OpnameRequest;
use App\Http\Services\CaptureOpnameService;
use App\Http\Services\CreateService;
use App\Http\Services\SingleService;
use App\Http\Services\UpdateService;
use Plugins\Alert;
use Plugins\Response;

class OpnameController extends MasterController
{
    public function __construct(OpnameRepository $repository, SingleService $service)
    {
        self::$repository = self::$repository ?? $repository;
        self::$service = self::$service ?? $service;
    }

    protected function beforeForm()
    {
        $rs = Rs::getOptions();
        $status = OpnameType::getOptions();
        $detail = [];

        self::$share = [
            'rs' => $rs,
            'status' => $status,
            'detail' => $detail,
        ];
    }

    public function getData()
    {
        $query = self::$repository->dataRepository();

        return $query;
    }

    public function postCreate(OpnameRequest $request, CreateService $service)
    {
        $data = $service->save(self::$repository, $request);

        return Response::redirectBack($data);
    }

    public function getCapture($code, CaptureOpnameService $service)
    {
        $model = $this->get($code);
        if (! empty($model->opname_capture)) {
            Alert::error('Opname sudah di capture !');

            return Response::redirectBack();
        }

        $data = $service->save($model);

        return Response::redirectBack($data);
    }

    public function getUpdate($code)
    {
        $this->beforeForm();
        $this->beforeUpdate($code);

        $model = $this->get($code);
        $detail = OpnameDetail::with([
            'has_view',
        ])->where(OpnameDetail::field_opname(), $code)
            ->fastPaginate(200);

        return moduleView(modulePathForm(), $this->share([
            'model' => $model,
            'detail' => $detail,
        ]));
    }

    public function postUpdate($code, OpnameRequest $request, UpdateService $service)
    {
        $data = $service->update(self::$repository, $request, $code);

        return Response::redirectBack($data);
    }

    public function getDelete()
    {
        $code = request()->get('code');
        OpnameDetail::where(OpnameDetail::field_opname(), $code)->delete();
        $data = self::$service->delete(self::$repository, $code);

        return Response::redirectBack($data);
    }

    public function postTable()
    {
        // if (request()->exists('delete') and ! empty(request()->get('code'))) {
        //     $code = array_unique(request()->get('code'));
        //     OpnameDetail::whereIn(OpnameDetail::field_opname(), $code)->delete();
        //     $data = self::$service->delete(self::$repository, $code);
        // }

        return Response::redirectBack();
    }
}
