<?php

namespace App\Charts;

use App\Models\DetailLinen;
use App\Models\Transaksi;
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

        return (new LarapexChart)->areaChart()
            ->setTitle('Kotor vs Bersih ('.$days.' hari)')
            ->setSubtitle('Transaksi kotor vs linen bersih harian')
            ->addData($kotor)
            ->addData($bersih)
            ->setXAxis($labels)
            ->setColors(['#dc2626', '#16a34a'])
            ->setGrid()
            ->setMarkers(['#dc2626', '#16a34a'], 4, 6);
    }

    /**
     * Distribusi status linen (Register / Kotor / Bersih) — donut global.
     */
    public function statusLinenDonut(): LarapexChart
    {
        $register = DetailLinen::where('detail_status_linen', 'REGISTER')->count();
        $kotor = DetailLinen::where('detail_status_linen', 'KOTOR')->count();
        $bersih = DetailLinen::where('detail_status_linen', 'BERSIH')->count();

        return (new LarapexChart)->donutChart()
            ->setTitle('Status Linen')
            ->setSubtitle('Register / Kotor / Bersih')
            ->addData([$register, $kotor, $bersih])
            ->setLabels(['Register', 'Kotor', 'Bersih'])
            ->setColors(['#3755c3', '#d97706', '#16a34a']);
    }
}
