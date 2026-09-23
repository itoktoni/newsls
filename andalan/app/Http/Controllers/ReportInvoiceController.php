<?php

namespace App\Http\Controllers;

use App\Dao\Enums\CuciType;
use App\Dao\Enums\TransactionType;
use App\Dao\Models\JenisLinen;
use App\Dao\Models\Rs;
use App\Dao\Models\User;
use App\Dao\Models\ViewDetailLinen;
use App\Dao\Models\ViewInvoice;
use App\Dao\Repositories\TransaksiRepository;
use App\Dao\Repositories\ViewInvoiceRepository;
use App\Http\Requests\InvoiceReportRequest;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class ReportInvoiceController extends MinimalController
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

    private function getQueryBersih($request)
    {
        $query = DB::table('view_invoice')
            ->where('view_rs_id', $request->rs_id)
            ->where('view_status', TransactionType::BERSIH);

        if ($start_date = $request->start_rekap) {
            $query = $query->whereDate('view_tanggal', '>=', $start_date);
        }

        if ($end_date = $request->end_rekap) {
            $query = $query->whereDate('view_tanggal', '<=', $end_date);
        }

        return $query->get();
    }

    public function getPrint(InvoiceReportRequest $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '512M');
        $tanggal = $linen = $lawan = $nama = [];

        $bersih = $this->getQueryBersih($request);

        $rs = Rs::find(request()->get(Rs::field_primary()));
        $linen = $bersih->sortBy(ViewDetailLinen::field_name())->pluck(ViewDetailLinen::field_name(), ViewDetailLinen::field_id());

        $tanggal = CarbonPeriod::create($request->start_rekap, $request->end_rekap);

        return moduleView(modulePathPrint(), $this->share([
            'data' => $bersih,
            'rs' => $rs,
            'tanggal' => $tanggal,
            'linen' => $linen,
        ]));
    }
}
