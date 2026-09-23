<?php

namespace App\Http\Controllers;

use App\Dao\Enums\TransactionType;
use App\Dao\Models\JenisLinen;
use App\Dao\Models\Kategori;
use App\Dao\Models\Rs;
use App\Dao\Models\Ruangan;
use App\Dao\Models\User;
use App\Dao\Models\ViewConfigLinen;
use App\Dao\Models\ViewDetailLinen;
use App\Dao\Models\ViewTransaksi;
use App\Dao\Repositories\TransaksiRepository;
use App\Http\Requests\GeneralRequest;
use App\Http\Requests\RekapReportRequest;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportRekapKotorLinenController extends MinimalController
{
    public $data;

    public function __construct(TransaksiRepository $repository)
    {
        self::$repository = self::$repository ?? $repository;
    }

    protected function beforeForm()
    {
        $rs = Rs::getOptions();
        $ruangan = Ruangan::getOptions();
        $kategori = Kategori::getOptions();
        $jenis = JenisLinen::getOptions();

        self::$share = [
            'jenis' => $jenis,
            'kategori' => $kategori,
            'ruangan' => $ruangan,
            'rs' => $rs,
        ];
    }

    private function getQueryKotor($request)
    {
        $query = DB::table('view_rekap_kotor')
            ->where('view_rs_id', $request->view_rs_id)
            ->where('view_status', TransactionType::KOTOR)
            ;

        if ($ruangan_id = $request->view_ruangan_id) {
            $query = $query->where('view_ruangan_id', $ruangan_id);
        }

        if ($jenis_id = $request->view_linen_id) {
            $query = $query->where('view_linen_id', $jenis_id);
        }

        if ($start_date = $request->start_rekap) {
            $query = $query->where('view_tanggal', '>=', $start_date);
        }

        if ($end_date = $request->end_rekap) {
            $query = $query->where('view_tanggal', '<=', $end_date);
        }

        return $query->get();
    }

    public function getPrint(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '512M');
        $location = $linen = $lawan = $nama = [];

        // $rs = Rs::with([HAS_RUANGAN, HAS_JENIS])->find(request()->get(Rs::field_primary()));
        // $location = $rs->has_ruangan;
        // $linen = $rs->has_jenis;

        $rs = Rs::find(request()->get(ViewDetailLinen::field_rs_id()));
        $ruangan = Rs::find(request()->get(ViewDetailLinen::field_ruangan_id()));
        $register = ViewConfigLinen::query()->where('view_rs_id', request()->get(ViewDetailLinen::field_rs_id()));

        if($linen = request()->get('view_linen_id'))
        {
            $register = $register->where('view_linen_id', $linen);
        }

        if($ruangan = request()->get('view_ruangan_id'))
        {
            $register = $register->where('view_ruangan_id', $ruangan);
        }

        $register = $register->get();

        $kotor = $this->getQueryKotor($request);

        $linen = $register->sortBy(ViewDetailLinen::field_name())->pluck(ViewDetailLinen::field_name(), ViewDetailLinen::field_id());

        $location = $register->sortBy(ViewDetailLinen::field_ruangan_name())->pluck(ViewDetailLinen::field_ruangan_name(), ViewDetailLinen::field_ruangan_id());

        $tanggal = CarbonPeriod::create($request->start_rekap, $request->end_rekap);

        return moduleView(modulePathPrint(), $this->share([
            'data' => $this->data,
            'rs' => $rs,
            'location' => $location,
            'ruangan' => $ruangan,
            'tanggal' => $tanggal,
            'linen' => $linen,
            'kotor' => $kotor,
            'register' => $register,
        ]));
    }
}
