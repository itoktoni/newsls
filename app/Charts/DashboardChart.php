<?php

namespace App\Charts;

use App\Models\DetailLinen;
use App\Models\Transaksi;
use App\Support\DashboardCache;
use ArielMejiaDev\LarapexCharts\LarapexChart;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardChart
{
    /**
     * Kotor (transaksi KOTOR) vs Bersih (tabel bersih) per hari — N hari terakhir.
     */
    public function kotorVsBersih(int $days = 7): LarapexChart
    {
        $data = $this->kotorVsBersihData($days);

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
    public function kotorVsBersihData(int $days = 7): array
    {
        return DashboardCache::remember('global', "chart:kotorBersih:{$days}", function () use ($days) {
            $labels = [];
            $kotor = [];
            $bersih = [];

            for ($i = 0; $i < $days; $i++) {
                $d = Carbon::today()->subDays($days - 1 - $i);
                $labels[] = $d->format('d M');
                $kotor[] = Transaksi::where('transaksi_status', 'KOTOR')
                    ->whereDate('transaksi_created_at', $d)
                    ->count();
                $bersih[] = DB::table('bersih')
                    ->where('bersih_status', 'BERSIH')
                    ->whereDate('bersih_created_at', $d)
                    ->count();
            }

            return compact('labels', 'kotor', 'bersih');
        });
    }

    /**
     * Distribusi status linen (Register / Kotor / Bersih) — donut global.
     */
    public function statusLinenDonut(): LarapexChart
    {
        $data = $this->statusLinenDonutData();

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
    public function statusLinenDonutData(): array
    {
        return DashboardCache::remember('global', 'chart:statusLinenDonut', function () {
            return [
                DetailLinen::where('detail_status_linen', 'REGISTER')->count(),
                DetailLinen::where('detail_status_linen', 'KOTOR')->count(),
                DetailLinen::where('detail_status_linen', 'BERSIH')->count(),
            ];
        });
    }
}
