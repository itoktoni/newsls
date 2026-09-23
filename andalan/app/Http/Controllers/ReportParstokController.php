<?php

namespace App\Http\Controllers;

use App\Dao\Models\JenisLinen;
use App\Dao\Models\Rs;
use App\Dao\Repositories\JenisLinenRepository;
use Illuminate\Http\Request;

class ReportParstokController extends MinimalController
{
    public $data;

    public function __construct(JenisLinenRepository $repository)
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
        return self::$repository->getParstok()->get();
    }

    public function getPrint(Request $request)
    {
        set_time_limit(0);
        $rs = Rs::find(request()->get(JenisLinen::field_rs_id()));

        $this->data = $this->getQuery($request);

        return moduleView(modulePathPrint(), $this->share([
            'data' => $this->data,
            'rs' => $rs,
        ]));
    }
}
