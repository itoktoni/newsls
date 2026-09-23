<?php

namespace App\Http\Controllers;

use App\Charts\DashboardChart;
use App\Models\Notification;
use App\Models\User;

class DashboardController extends Controller
{
    public function __invoke(DashboardChart $chart)
    {
        $stats = [
            'total_users' => User::count(),
            'total_notifications' => Notification::count(),
            'unread_notifications' => Notification::where('read', false)->count(),
        ];

        $recentUsers = User::latest()->limit(5)->get();

        return view('dashboard', compact('stats', 'recentUsers'))
            ->with('userChart', $chart->userRegistrations())
            ->with('notifChart', $chart->notificationStats());
    }
}
