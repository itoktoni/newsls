<?php

namespace App\Http\Controllers;

use App\Concerns\ResolvesDashboardRole;
use Illuminate\Http\RedirectResponse;

class DashboardRouterController extends Controller
{
    use ResolvesDashboardRole;

    public function __invoke(): RedirectResponse
    {
        return redirect()->route(static::defaultDashboardRoute(auth()->user()?->role));
    }
}
