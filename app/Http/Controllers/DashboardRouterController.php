<?php

namespace App\Http\Controllers;

use App\Concerns\ResolvesDashboardRole;
use Illuminate\Http\RedirectResponse;

class DashboardRouterController extends Controller
{
    use ResolvesDashboardRole;

    public function __invoke(): RedirectResponse
    {
        return redirect()->route($this->defaultDashboardRoute(auth()->user()?->role));
    }
}
