<?php

namespace App\Http\Controllers;

use App\Dao\Models\JenisLinen;
use App\Dao\Models\Logs;
use App\Dao\Models\Mutasi;
use App\Dao\Models\Rs;
use App\Dao\Models\Ruangan;
use App\Dao\Models\ViewDetailLinen;
use App\Dao\Repositories\LogRepository;
use App\Dao\Repositories\MutasiRepository;
use App\Http\Requests\MutasiReportRequest;
use Carbon\CarbonPeriod;

class ReportLogController extends MinimalController
{
    public $data;

    public function __construct(LogRepository $repository)
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
        $query = self::$repository->dataRepository()
            ->addSelect(['logs.*',JenisLinen::field_name(), Ruangan::field_name(), Rs::field_name()])
            ->leftJoinRelationship('has_rs')
            ->leftJoinRelationship('has_jenis')
            ->leftJoinRelationship('has_ruangan')
        ;

        if ($awal = request()->get('start_date')) {
            $query = $query->whereDate(Logs::field_tanggal(), '>=', $awal);
        }

        if ($akhir = request()->get('end_date')) {
            $query = $query->whereDate(Logs::field_tanggal(), '<=', $akhir);
        }

        if ($rs_id = request()->get('view_rs_id')) {
            $query = $query->where(Logs::field_rs(), $rs_id);
        }

        if ($linen_id = request()->get(Logs::field_jenis())) {
            $query = $query->where(Logs::field_jenis(), $linen_id);
        }

        return $query->get();
    }

    public function getPrint(MutasiReportRequest $request)
    {
        set_time_limit(0);
        $rs_id = intval($request->view_rs_id);
        $rs = Rs::find($rs_id);
        $tanggal = CarbonPeriod::create($request->start_date, $request->end_date);

        $this->data = $this->getQuery($request);

        return moduleView(modulePathPrint(), $this->share([
            'data' => $this->data,
            'rs' => $rs,
            'tanggal' => $tanggal,
        ]));
    }
}
