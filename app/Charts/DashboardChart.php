<?php

namespace App\Charts;

use App\Models\DetailLinen;
use App\Models\Transaksi;
use App\Models\User;
use App\Support\DashboardCache;
use ArielMejiaDev\LarapexCharts\LarapexChart;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardChart
{
    /**
     * Kotor (transaksi KOTOR) vs Bersih (tabel bersih) per hari — N hari terakhir.
     */
    public function kotorVsBersih(int $days = 7, ?int $rsId = null): LarapexChart
    {
        $data = $this->kotorVsBersihData($days, $rsId);

        return (new LarapexChart)->areaChart()
            ->setTitle('Kotor vs Bersih ('.$days.' hari)')
            ->setSubtitle('Transaksi kotor vs linen bersih harian')
            ->addData($data['kotor'])
            ->addData($data['bersih'])
            ->setXAxis($data['labels'])
            ->setColors(['#dc2626', '#16a34a'])
            ->setGrid()
            ->setMarkers(['#dc2626', '#16a34a'], 4, 6);
    }

    /**
     * Data mentah chart area (di-cache sebagai array — bukan object Larapex).
     *
     * @return array{labels: string[], kotor: int[], bersih: int[]}
     */
    public function kotorVsBersihData(int $days = 7, ?int $rsId = null): array
    {
        return DashboardCache::remember('global', "chart:kotorBersih:{$days}:rs:".($rsId ?? 'all'), function () use ($days, $rsId) {
            $labels = [];
            $kotor = [];
            $bersih = [];

            for ($i = 0; $i < $days; $i++) {
                $d = Carbon::today()->subDays($days - 1 - $i);
                $labels[] = $d->format('d M');
                $kotor[] = User::applyRsFilter(
                    Transaksi::where('transaksi_status', 'KOTOR'),
                    ['transaksi.transaksi_rs_scan', 'transaksi.transaksi_rs_ori'],
                    $rsId
                )->whereDate('transaksi_created_at', $d)->count();
                $bersih[] = User::applyRsFilter(
                    DB::table('bersih')->where('bersih_status', 'BERSIH'),
                    'bersih.bersih_id_rs',
                    $rsId
                )->whereDate('bersih_created_at', $d)->count();
            }

            return compact('labels', 'kotor', 'bersih');
        });
    }

    /**
     * Distribusi status linen (Register / Kotor / Bersih) — donut global.
     */
    public function statusLinenDonut(?int $rsId = null): LarapexChart
    {
        $data = $this->statusLinenDonutData($rsId);

        return (new LarapexChart)->donutChart()
            ->setTitle('Status Linen')
            ->setSubtitle('Register / Kotor / Bersih')
            ->addData($data)
            ->setLabels(['Register', 'Kotor', 'Bersih'])
            ->setColors(['#3755c3', '#d97706', '#16a34a']);
    }

    /**
     * @return array{0: int, 1: int, 2: int} [register, kotor, bersih]
     */
    public function statusLinenDonutData(?int $rsId = null): array
    {
        return DashboardCache::remember('global', 'chart:statusLinenDonut:rs:'.($rsId ?? 'all'), function () use ($rsId) {
            $count = fn (string $status) => User::applyRsFilter(
                DetailLinen::where('detail_status_linen', $status),
                'detail_linen.detail_id_rs',
                $rsId
            )->count();

            return [$count('REGISTER'), $count('KOTOR'), $count('BERSIH')];
        });
    }
}
