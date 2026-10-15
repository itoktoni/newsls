<?php

namespace App\Http\Controllers;

use App\Dao\Enums\CuciType;
use App\Dao\Enums\HilangType;
use App\Dao\Enums\ProcessType;
use App\Dao\Enums\RegisterType;
use App\Dao\Models\JenisLinen;
use App\Dao\Models\Rs;
use App\Dao\Models\Ruangan;
use App\Dao\Models\ViewDetailLinen;
use App\Dao\Models\ViewLog;
use App\Dao\Models\ViewOutstanding;
use App\Dao\Repositories\DetailRepository;
use App\Dao\Repositories\OutstandingRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportDetailPendingLinenController extends MinimalController
{
    public $data;

    public function __construct(OutstandingRepository $repository)
    {
        self::$repository = self::$repository ?? $repository;
    }

    protected function beforeForm()
    {
        $rs = Rs::getOptions();
        $jenis = JenisLinen::getOptions();
        $ruangan = Ruangan::getOptions();
        $cuci = CuciType::getOptions();
        $register = RegisterType::getOptions();

        self::$share = [
            'rs' => $rs,
            'ruangan' => $ruangan,
            'jenis' => $jenis,
            'register' => $register,
            'cuci' => $cuci,
        ];
    }

    private function getQuery($request)
    {
        $query = self::$repository->getPrint()
            ->where('outstanding.'.ViewOutstanding::field_status_hilang(), HilangType::PENDING);

        if ($start_date = $request->start_pending) {
            $query = $query->whereDate(ViewOutstanding::field_pending_created_at(), '>=', $start_date);
        }

        if ($end_date = $request->end_pending) {
            $query = $query->whereDate(ViewOutstanding::field_pending_created_at(), '<=', $end_date);
        }

        return $query->get();
    }

    public function getPrint(Request $request)
    {
        set_time_limit(0);
        $rs = Rs::find(request()->get(ViewDetailLinen::field_rs_id()));

        $this->data = $this->getQuery($request);

        return moduleView(modulePathPrint(), $this->share([
            'data' => $this->data,
            'rs' => $rs,
        ]));
    }
}
