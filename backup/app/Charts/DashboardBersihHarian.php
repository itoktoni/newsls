<?php

namespace App\Charts;

use App\Dao\Enums\TransactionType;
use App\Dao\Models\Bersih;
use App\Dao\Models\Transaksi;
use ArielMejiaDev\LarapexCharts\LarapexChart;
use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;

class DashboardBersihHarian
{
    protected $chart;

    public function __construct(LarapexChart $chart)
    {
        $this->chart = $chart;
    }

    public function build()
    {
        $start_date = Carbon::now()->subDay(7);
        $end_date = Carbon::now();
        $range = CarbonPeriod::create($start_date, $end_date)->toArray();
        $bersih = [];
        $kotor = [];

        $rs_id = auth()->user()->rs_id;

        $data_bersih = Bersih::where(Bersih::field_report(), '>=', $start_date->format('Y-m-d'))
            ->where(Bersih::field_report(), '<=', $end_date->format('Y-m-d'))
            ->where(Bersih::field_status(), TransactionType::BERSIH);

        $data_kotor = Transaksi::whereDate(Transaksi::field_created_at(), '>=', $start_date->format('Y-m-d'))
            ->whereDate(Transaksi::field_created_at(), '<=', $end_date->format('Y-m-d'))
            ->where(Transaksi::field_status_transaction(), TransactionType::KOTOR)->whereNotNull(Transaksi::field_rs_ori());

        $data_rewash = Transaksi::whereDate(Transaksi::field_created_at(), '>=', $start_date->format('Y-m-d'))
            ->whereDate(Transaksi::field_created_at(), '<=', $end_date->format('Y-m-d'))
            ->where(Transaksi::field_status_transaction(), TransactionType::REWASH)->whereNotNull(Transaksi::field_rs_ori());

        $data_reject = Transaksi::whereDate(Transaksi::field_created_at(), '>=', $start_date->format('Y-m-d'))
            ->whereDate(Transaksi::field_created_at(), '<=', $end_date->format('Y-m-d'))
            ->where(Transaksi::field_status_transaction(), TransactionType::REJECT)->whereNotNull(Transaksi::field_rs_ori());

        if ($rs_id) {

            $data_bersih = $data_bersih->where(Bersih::field_rs_id(), auth()->user()->rs_id);
            $data_kotor = $data_kotor->where(Transaksi::field_rs_ori(), auth()->user()->rs_id);
            $data_rewash = $data_rewash->where(Transaksi::field_rs_ori(), auth()->user()->rs_id);
            $data_reject = $data_reject->where(Transaksi::field_rs_ori(), auth()->user()->rs_id);
        }

        $data_bersih = $data_bersih->get();
        $data_kotor = $data_kotor->get()->map(function ($item) {
            $item['tanggal'] = $item->transaksi_created_at->format('Y-m-d') ?? null;
            return $item;
        });

        $data_reject = $data_reject->get()->map(function ($item) {
            $item['tanggal'] = $item->transaksi_created_at->format('Y-m-d') ?? null;
            return $item;
        });

        $data_rewash = $data_rewash->get()->map(function ($item) {
            $item['tanggal'] = $item->transaksi_created_at->format('Y-m-d') ?? null;
            return $item;
        });

        foreach ($range as $dates) {
            $date[] = $dates->format('m-d');
            $bersih[] = $data_bersih->where(Bersih::field_report(), $dates->format('Y-m-d'))->count();
            $kotor[] = $data_kotor->where('tanggal', $dates->format('Y-m-d'))->count();
            $reject[] = $data_reject->where('tanggal', $dates->format('Y-m-d'))->count();
            $rewash[] = $data_rewash->where('tanggal', $dates->format('Y-m-d'))->count();
        }

        return $this->chart->barChart()
            ->setTitle('Perbandingan Data Transaksi 7 hari kebelakang')
            ->setSubtitle('Bersih vs Kotor vs Rewash vs Reject.')
            ->addData('Bersih', $bersih)
            ->addData('Kotor', $kotor)
            ->addData('Rewash', $rewash)
            ->addData('Reject', $reject)
            ->setXAxis($date);
    }
}
