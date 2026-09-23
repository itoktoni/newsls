<?php

namespace App\Http\Controllers;

use App\Dao\Enums\CuciType;
use App\Dao\Enums\DetailType;
use App\Dao\Enums\LogType;
use App\Dao\Enums\ProcessType;
use App\Dao\Enums\RegisterType;
use App\Dao\Enums\TransactionType;
use App\Dao\Models\Bersih;
use App\Dao\Models\ConfigLinen;
use App\Dao\Models\Detail;
use App\Dao\Models\History;
use App\Dao\Models\JenisBahan;
use App\Dao\Models\JenisLinen;
use App\Dao\Models\OpnameDetail;
use App\Dao\Models\Outstanding;
use App\Dao\Models\Rs;
use App\Dao\Models\Ruangan;
use App\Dao\Models\Supplier;
use App\Dao\Models\Transaksi;
use App\Dao\Models\ViewDetailLinen;
use App\Dao\Repositories\DetailRepository;
use App\Http\Requests\DeleteRequest;
use App\Http\Requests\GeneralRequest;
use App\Http\Services\DeleteService;
use App\Http\Services\SingleService;
use App\Http\Services\UpdateDetailService;
use App\Http\Services\UpdateService;
use Plugins\History as PluginsHistory;
use Plugins\Response;

class DetailController extends MasterController
{
    public function __construct(DetailRepository $repository, SingleService $service)
    {
        self::$repository = self::$repository ?? $repository;
        self::$service = self::$service ?? $service;
    }

    public function postUpdate($code, GeneralRequest $request, UpdateDetailService $service)
    {
        $data = $service->update(self::$repository, $request, $code);
        if($data['status'])
        {
            return Response::redirectToRoute('detail.getUpdate', ['code' => $data['data']->detail_rfid]);
        }
        else
        {
            return Response::redirectBack($data);
        }
    }

    protected function beforeForm()
    {
        $rs = Rs::getOptions();
        $ruangan = Ruangan::getOptions();
        $jenis = JenisLinen::getOptions();
        $bahan = JenisBahan::getOptions();
        $supplier = Supplier::getOptions();
        $cuci = CuciType::getOptions();
        $transaction = TransactionType::getOptions();
        $process = ProcessType::getOptions();
        $register = RegisterType::getOptions();

        self::$share = [
            'register' => $register,
            'process' => $process,
            'transaction' => $transaction,
            'cuci' => $cuci,
            'jenis' => $jenis,
            'supplier' => $supplier,
            'bahan' => $bahan,
            'ruangan' => $ruangan,
            'rs' => $rs,
        ];
    }

    public function getData()
    {
        $query = self::$repository->dataRepository();

        if ($start = request()->get('start_date')) {
            $query = $query->whereDate(Detail::field_created_at(), '>=', $start);
        }

        if ($end = request()->get('end_date')) {
            $query = $query->whereDate(Detail::field_created_at(), '<=', $end);
        }

        if($rfid = request()->get('rfid')) {
            $explode = array_map('trim', explode(' ', $rfid));
            $query = $query->whereIn(Detail::field_primary(), $explode);
        }

        return $query->paginate(100);
    }

    public function getTable()
    {
        $data = $this->getData();
        $transaction = TransactionType::getOptions();
        $process = ProcessType::getOptions();
        $register = RegisterType::getOptions();
        $rfid = request()->get('rfid');

        return moduleView(modulePathTable(), [
            'data' => $data,
            'rfid' => $rfid,
            'register' => $register,
            'process' => $process,
            'transaction' => $transaction,
            'fields' => self::$repository->model->fieldDatatable(),
        ]);
    }

    public function getHistory($code)
    {
        $this->beforeForm();

        $model = $this->get($code);
        $history = History::where(History::field_name(), $code)
            ->addSelect('*')
            ->leftJoinRelationship('has_rs')
            ->orderBy(History::field_created_at(), 'DESC')
            ->limit(10)->get();

        return moduleView(modulePathForm('history'), $this->share([
            'model' => $model,
            'history' => $history,
        ]));
    }

    public function postDelete(DeleteRequest $request, DeleteService $service)
    {
        $code = $request->get('code');
        $data = $service->delete(self::$repository, $code);
        $this->deleteAll($code);

        return Response::redirectBack($data);
    }

    public function getDelete()
    {
        $code = request()->get('code');
        $data = self::$service->delete(self::$repository, $code);
        $this->deleteAll([$code]);

        return Response::redirectBack($data);
    }

    public function postTable()
    {
        if (request()->exists('delete')) {
            $code = array_unique(request()->get('code'));
            $data = self::$service->delete(self::$repository, $code);
            $this->deleteAll($code);
        }

        if (request()->exists('sort')) {
            $sort = array_unique(request()->get('sort'));
            $data = self::$service->sort(self::$repository, $sort);
        }

        return Response::redirectBack($data);
    }

    private function deleteAll($code)
    {
        PluginsHistory::bulk($code, LogType::DELETE_RFID, "DELETE");
        Transaksi::whereIn(Transaksi::field_rfid(), $code)->delete();
        ConfigLinen::whereIn(Detail::field_primary(), $code)->delete();
        Outstanding::whereIn(Outstanding::field_primary(), $code)->delete();
        Bersih::whereIn(Bersih::field_rfid(), $code)->delete();
    }
}
