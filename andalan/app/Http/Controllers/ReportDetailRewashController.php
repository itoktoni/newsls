<?php

namespace App\Http\Controllers;

use App\Dao\Enums\TransactionType;
use App\Dao\Models\Rs;
use App\Dao\Models\Transaksi;
use App\Dao\Models\User;
use App\Dao\Repositories\TransaksiRepository;
use App\Http\Requests\TransactionReportRequest;

class ReportDetailRewashController extends MinimalController
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
        return self::$repository
            ->getDetailKotor(TransactionType::REWASH)
            ->where(Transaksi::field_rs_ori(), request()->get('rs_ori_id'))
            ->get();
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
