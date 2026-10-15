<?php

namespace App\Http\Controllers;

use App\Dao\Enums\TransactionType;
use App\Dao\Models\Rs;
use App\Dao\Models\User;
use App\Dao\Repositories\TransaksiRepository;
use App\Http\Requests\TransactionReportRequest;

class ReportDetailKotorController extends MinimalController
{
    public $data;

    public function __construct(TransaksiRepository $repository)
    {
        self::$repository = self::$repository ?? $repository;
    }

    protected function beforeForm()
    {
        $rs = Rs::getOptions();
        $user = User::getOptions();

        self::$share = [
            'user' => $user,
            'rs' => $rs,
        ];
    }

    private function getQuery($request)
    {
        $query = self::$repository->getDetailKotor(TransactionType::KOTOR);

        if($rs = $request->get('rs_ori_id')){
            $query->where('ori.rs_id', $rs);
        }

        return $query->orderBy('view_linen_nama', 'ASC')->get();
    }

    public function getPrint(TransactionReportRequest $request)
    {
        set_time_limit(0);
        $rs = Rs::find(request()->get('rs_ori_id'));

        $this->data = $this->getQuery($request);

        return moduleView(modulePathPrint(), $this->share([
            'data' => $this->data,
            'rs' => $rs,
        ]));
    }
}
