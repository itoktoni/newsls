<?php

namespace App\Http\Controllers;

use App\Dao\Models\Rs;
use App\Dao\Models\ViewDetailLinen;
use App\Dao\Repositories\DetailRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportRegisterObsesimanController extends MinimalController
{
    public $data;

    public function __construct(DetailRepository $repository)
    {
        self::$repository = self::$repository ?? $repository;
    }

    protected function beforeForm()
    {

        $rs = Rs::getOptions();
        $rs = DB::connection('server')
            ->table('system_company')
            ->get()
            ->pluck('company_name', 'company_id');

        self::$share = [
            'rs' => $rs,
        ];
    }

    private function getQuery($request)
    {
        $query = DB::connection('server')->table('view_linen');
        if ($request->start_date) {
            $query->whereDate('item_linen_created_at', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('item_linen_created_at', '<=', $request->end_date);
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
