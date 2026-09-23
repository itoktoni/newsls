<?php

namespace App\Charts;

use App\Dao\Enums\TransactionType;
use App\Dao\Models\ViewDetailLinen;
use ArielMejiaDev\LarapexCharts\LarapexChart;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardKotorHarian
{
    protected $chart;

    public function __construct(LarapexChart $chart)
    {
        $this->chart = $chart;
    }

    public function build()
    {
        $ruangan = $total = [];
        $rs_id = auth()->user()->rs_id;

        $data_detail = ViewDetailLinen::select([ViewDetailLinen::field_rs_name(), DB::raw('count(view_linen_rfid) as total')])
                ->where(ViewDetailLinen::field_status_process(), TransactionType::BERSIH)
                ->groupBy(ViewDetailLinen::field_rs_name())
                ->orderBy(ViewDetailLinen::field_rs_name());

        if ($rs_id) {

            $data_detail = $data_detail->where(ViewDetailLinen::field_rs_id(), $rs_id);

        }

        $rs = $data_detail->pluck('view_rs_nama')->toArray();
        $total = $data_detail->pluck('total')->toArray();

        return $this->chart->donutChart()
            ->setSubtitle('Tanggal '.Carbon::now()->format('d-M-Y'))
            // ->setXAxis($rs)
            // ->addData('Sebaran Linen per Ruangan', $total)
            ->setLabels($rs)
            ->addData($total)
            ->setTitle('Sebaran Linen Bersih di Seluruh Rumah Sakit');
    }
}
