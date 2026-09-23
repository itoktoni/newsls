<?php

namespace App\Charts;

use App\Models\Notification;
use App\Models\User;
use ArielMejiaDev\LarapexCharts\LarapexChart;
use Carbon\Carbon;

class DashboardChart
{
    /**
     * User registrations over the last 7 days.
     */
    public function userRegistrations(): LarapexChart
    {
        $days = collect(range(6, 0))->map(function ($i) {
            $date = Carbon::today()->subDays($i);

            return [
                'label' => $date->format('d M'),
                'count' => User::whereDate('created_at', $date)->count(),
            ];
        });

        return (new LarapexChart)->areaChart()
            ->setTitle('User Registrations')
            ->setSubtitle('New users — last 7 days')
            ->addData($days->pluck('count')->toArray())
            ->setXAxis($days->pluck('label')->toArray())
            ->setColors(['#3755c3'])
            ->setGrid()
            ->setMarkers(['#3755c3'], 4, 6);
    }

    /**
     * Notifications: read vs unread.
     */
    public function notificationStats(): LarapexChart
    {
        $read = Notification::where('read', true)->count();
        $unread = Notification::where('read', false)->count();

        return (new LarapexChart)->donutChart()
            ->setTitle('Notifications')
            ->setSubtitle('Read / Unread')
            ->addData([$read, $unread])
            ->setLabels(['Read', 'Unread'])
            ->setColors(['#16a34a', '#d97706']);
    }
}
