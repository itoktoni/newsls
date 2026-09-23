<?php

namespace App\Http\Controllers;

use App\Dao\Models\Detail;
use App\Dao\Models\Opname;
use App\Dao\Models\OpnameDetail;
use App\Dao\Repositories\OpnameRepository;
use Illuminate\Http\Request;
use Plugins\Query;

class ReportOpnameSummaryController extends MinimalController
{
    public $data;

    public function __construct(OpnameRepository $repository)
    {
        self::$repository = self::$repository ?? $repository;
    }

    protected function beforeForm()
    {

        self::$share = [
            'rs' => Query::getOpnameList(),
        ];
    }

    private function getQuery($opname_id)
    {
        $query = self::$repository->getOpnameByID($opname_id);

        return $query;
    }

    public function getPrint(Request $request)
    {
        set_time_limit(0);
        $this->data = $this->getQuery($request->opname_id)->orderBy('opname_detail_waktu', 'ASC')->get();

        $opname = Opname::with(['has_rs'])->find(request()->get(Opname::field_primary()));
        // $register = Detail::where(Detail::field_rs_id(), $opname->field_rs_id)->count();

        $register = OpnameDetail::join(Opname::getTableName(), OpnameDetail::getTableName() . '.opname_detail_id_opname', '=', Opname::getTableName() . '.opname_id')
            ->whereNotNull(OpnameDetail::field_transaksi())
            ->where(OpnameDetail::field_opname(), $request->opname_id)
            ->count();

        return moduleView(modulePathPrint(), $this->share([
            'data' => $this->data,
            'opname' => $opname,
            'register' => $register,
        ]));
    }
}
