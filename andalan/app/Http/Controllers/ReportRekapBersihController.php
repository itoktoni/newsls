<?php

namespace App\Http\Controllers;

use App\Dao\Enums\TransactionType;
use App\Dao\Models\Rs;
use App\Dao\Models\User;
use App\Dao\Models\ViewDetailLinen;
use App\Dao\Repositories\TransaksiRepository;
use App\Http\Requests\RekapReportRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportRekapBersihController extends MinimalController
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
        $query = DB::table('view_rekap_bersih')
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

    private function getQueryKotor($request)
    {
        $query = DB::table('view_rekap_kotor')->where('view_rs_id', $request->rs_id);

        if ($start_date = $request->start_rekap) {
            $bersih_from = Carbon::createFromFormat('Y-m-d', $start_date) ?? false;
            if ($bersih_from) {
                $query = $query->where('view_tanggal', '>=', $bersih_from->addDay(-1)->format('Y-m-d'));
            }
        }

        if ($end_date = $request->end_rekap) {
            $bersih_to = Carbon::createFromFormat('Y-m-d', $end_date) ?? false;
            if ($bersih_to) {
                $query = $query->where('view_tanggal', '<=', $bersih_to->addDay(-1)->format('Y-m-d'));
            }
        }

        return $query->get();
    }

    public function getPrint(RekapReportRequest $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        $location = $linen = $lawan = [];

        // $rs = Rs::with([HAS_RUANGAN, HAS_JENIS])->find(request()->get(Rs::field_primary()));
        // $location = $rs->has_ruangan;
        // $linen = $rs->has_jenis;

        $bersih = $this->getQueryBersih($request);

        $rs = Rs::find(request()->get(Rs::field_primary()));
        $linen = $bersih->sortBy(ViewDetailLinen::field_name())->pluck(ViewDetailLinen::field_name(), ViewDetailLinen::field_id());
        $location = $bersih->sortBy(ViewDetailLinen::field_ruangan_name())->pluck(ViewDetailLinen::field_ruangan_name(), ViewDetailLinen::field_ruangan_id());

        return moduleView(modulePathPrint(), $this->share([
            'data' => $this->data,
            'rs' => $rs,
            'location' => $location,
            'linen' => $linen,
            'bersih' => $bersih,
        ]));
    }
}
