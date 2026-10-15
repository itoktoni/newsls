<?php

namespace App\Http\Controllers;

use App\Dao\Enums\OwnershipType;
use App\Dao\Models\JenisLinen;
use App\Dao\Models\Rs;
use App\Dao\Models\Ruangan;
use App\Dao\Repositories\RsRepository;
use App\Http\Requests\RsRequest;
use App\Http\Services\CreateRsService;
use App\Http\Services\SingleService;
use App\Http\Services\UpdateJenisService;
use App\Http\Services\UpdateRsService;
use Illuminate\Http\Request;
use Plugins\Response;

class RsController extends MasterController
{
    public function __construct(RsRepository $repository, SingleService $service)
    {
        self::$repository = self::$repository ?? $repository;
        self::$service = self::$service ?? $service;
    }

    protected function beforeForm()
    {

        $ruangan = Ruangan::getOptions();
        $jenis = JenisLinen::getOptions();
        $status = OwnershipType::getOptions();

        self::$share = [
            'ruangan' => $ruangan,
            'jenis' => $jenis,
            'status' => $status,
        ];
    }

    public function postCreate(RsRequest $request, CreateRsService $service)
    {
        $data = $service->save(self::$repository, $request->all());

        return Response::redirectBack($data);
    }

    public function postUpdate($code, RsRequest $request, UpdateRsService $service)
    {
        $data = $service->update(self::$repository, $request->all(), $code);

        return Response::redirectBack($data);
    }

    public function postJenis($code, Request $request, UpdateJenisService $service){
        $data = $service->update(self::$repository, $request->all(), $code);

        return Response::redirectBack($data);
    }

    public function getJenis($code){
        $data = $this->get($code, [Rs::field_has_ruangan(), 'has_jenis']);
        $jenis = $data->has_jenis ?? [];

        $this->beforeForm();
        $this->beforeUpdate($code);

        return moduleView(modulePathForm('jenis'), $this->share([
            'code' => $code,
            'model' => $data,
            'jenis' => $jenis,
        ]));
    }

    public function getUpdate($code)
    {
        $data = $this->get($code, [Rs::field_has_ruangan()]);
        $selected_ruangan = $data->has_ruangan->pluck(Ruangan::field_primary()) ?? [];
        $selected_jenis = $data->has_jenis->pluck(JenisLinen::field_primary()) ?? [];

        $this->beforeForm();
        $this->beforeUpdate($code);

        return moduleView(modulePathForm(), $this->share([
            'model' => $data,
            'selected_jenis' => $selected_jenis,
            'selected_ruangan' => $selected_ruangan,
        ]));
    }
}
