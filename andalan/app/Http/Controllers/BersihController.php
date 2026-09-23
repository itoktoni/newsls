<?php

namespace App\Http\Controllers;

use App\Dao\Builder\DataBuilder;
use App\Dao\Enums\DetailType;
use App\Dao\Enums\ProcessType;
use App\Dao\Enums\TransactionType;
use App\Dao\Models\Bersih;
use App\Dao\Models\JenisLinen;
use App\Dao\Models\Outstanding;
use App\Dao\Models\Rs;
use App\Dao\Models\Ruangan;
use App\Dao\Models\User;
use App\Dao\Repositories\BersihRepository;
use App\Http\Requests\DeleteRequest;
use App\Http\Requests\DeliveryRequest;
use App\Http\Requests\GeneralRequest;
use App\Http\Requests\PackingRequest;
use App\Http\Services\CreateService;
use App\Http\Services\DeleteService;
use App\Http\Services\SingleService;
use App\Http\Services\UpdateDeliveryService;
use App\Http\Services\UpdatePackingService;
use App\Http\Services\UpdateService;
use Illuminate\Http\Request;
use Plugins\Response;

class BersihController extends MasterController
{
    public function __construct(BersihRepository $repository, SingleService $service)
    {
        self::$repository = self::$repository ?? $repository;
        self::$service = self::$service ?? $service;
    }

    public function getTable()
    {
        $data = $this->getData();
        $ruangan = Ruangan::getOptions();
        $rs = Rs::getOptions();
        $linen = JenisLinen::getOptions();
        $status = DetailType::getOptions();
        $user = User::getOptions();

        return moduleView(modulePathTable(), [
            'data' => $data,
            'ruangan' => $ruangan,
            'linen' => $linen,
            'rs' => $rs,
            'user' => $user,
            'status' => $status,
            'fields' => $this->fieldDatatable(),
        ]);
    }

    public function fieldDatatable(): array
    {
        return [
            DataBuilder::build(Outstanding::field_key())->name('No. Transaksi')->sort(),
            DataBuilder::build(Outstanding::field_primary())->name('No. RFID')->sort(),
            DataBuilder::build(Outstanding::field_name())->name('Nama Linen')->sort(),
        ];
    }

    public function postCreate(GeneralRequest $request, CreateService $service)
    {
        $data = $service->save(self::$repository, $request);

        return Response::redirectBack($data);
    }

    public function postUpdate($code, GeneralRequest $request, UpdateService $service)
    {
        $data = $service->update(self::$repository, $request, $code);

        return Response::redirectBack($data);
    }

    public function postTable()
    {
        if (request()->exists('delete')) {
            $code = array_unique(request()->get('code'));
            $where = Bersih::whereIn(Bersih::field_primary(), $code)
                ->get()->pluck(Bersih::field_rfid())
                ->toArray();

            Outstanding::whereIn(Outstanding::field_primary(), $where)->update([
                Outstanding::field_status_process() => ProcessType::QC
            ]);

            $data = self::$service->delete(self::$repository, $code);
        }

        return Response::redirectBack($data);
    }

    //============================== TRANSAKSI ====================================

    public function packing(PackingRequest $request, UpdatePackingService $service)
    {
       return $service->update($request);
    }

    public function delivery(DeliveryRequest $request, UpdateDeliveryService $service)
    {
       return $service->update($request);
    }
}
