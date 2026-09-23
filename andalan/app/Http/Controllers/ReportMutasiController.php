<?php

namespace App\Http\Controllers;

use App\Dao\Models\JenisLinen;
use App\Dao\Models\Mutasi;
use App\Dao\Models\Rs;
use App\Dao\Models\ViewDetailLinen;
use App\Dao\Repositories\MutasiRepository;
use App\Http\Requests\MutasiReportRequest;

class ReportMutasiController extends MinimalController
{
    public $data;

    public function __construct(MutasiRepository $repository)
    {
        self::$repository = self::$repository ?? $repository;
    }

    protected function beforeForm()
    {

        $rs = Rs::getOptions();
        $jenis = JenisLinen::getOptions();

        self::$share = [
            'jenis' => $jenis,
            'rs' => $rs,
        ];
    }

    private function getQuery($request)
    {
        $query = self::$repository->dataRepository();

        if ($awal = request()->get('start_date')) {
            $query = $query->where(Mutasi::field_tanggal(), '>=', $awal);
        }

        if ($akhir = request()->get('end_date')) {
            $query = $query->where(Mutasi::field_tanggal(), '<=', $akhir);
        }

        if ($rs_id = request()->get(ViewDetailLinen::field_rs_id())) {
            $query = $query->where(Mutasi::field_rs_id(), $rs_id);
        }

        if ($linen_id = request()->get(ViewDetailLinen::field_id())) {
            $query = $query->where(Mutasi::field_linen_id(), $linen_id);
        }

        return $query->get();
    }

    public function getPrint(MutasiReportRequest $request)
    {
        set_time_limit(0);
        $rs_id = intval($request->view_rs_id);
        $rs = Rs::find($rs_id);

        $this->data = $this->getQuery($request);

        return moduleView(modulePathPrint(), $this->share([
            'data' => $this->data,
            'rs' => $rs,
        ]));
    }
}
