<?php

namespace App\Http\Controllers;

use App\Dao\Models\JenisLinen;
use App\Dao\Models\Opname;
use App\Dao\Models\ViewOpname;
use App\Dao\Repositories\OpnameRepository;
use App\Http\Requests\OpnameReportRequest;
use Plugins\Query;

class ReportRekapOpnameController extends MinimalController
{
    public $data;

    public function __construct(OpnameRepository $repository)
    {
        self::$repository = self::$repository ?? $repository;
    }

    protected function beforeForm()
    {
        $opname = Query::getOpnameList();
        $jenis = JenisLinen::getOptions();

        self::$share = [
            'jenis' => $jenis,
            'opname' => $opname,
        ];
    }

    public function getPrint(OpnameReportRequest $request)
    {
        set_time_limit(0);
        $location = $linen = $lawan = $nama = [];

        $this->data = Opname::with([
            'has_rs',
        ])->find($request->opname_id);

        $opname = ViewOpname::where(Opname::field_primary(), $request->opname_id)
            ->where('rs_id', $this->data->opname_id_rs);

        if ($jenis_id = $request->jenis_id) {
            $opname->where('jenis_id', $jenis_id);
        }

        $rs = $this->data->has_rs ?? false;

        $opname = $opname->get();

        if ($rs) {
            $location = $opname->sortBy('ruangan_nama')->pluck('ruangan_nama', 'ruangan_id')->unique();
            $linen = $opname->sortBy('jenis_nama')->pluck('jenis_nama', 'jenis_id')->unique();
        }

        return moduleView(modulePathPrint(), $this->share([
            'data' => $this->data,
            'rs' => $rs,
            'opname' => $opname,
            'location' => $location,
            'linen' => $linen,
        ]));
    }
}
