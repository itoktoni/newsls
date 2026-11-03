<?php

namespace App\Http\Middleware;

use App\Enums\RoleEnum;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AccessMiddleware
{
    protected array $abilityMap = [
        'getTable' => 'view',
        'getCreate' => 'save',
        'postCreate' => 'save',
        'getUpdate' => 'save',
        'postUpdate' => 'save',
        'postDelete' => 'delete',
        'postDeleteBulk' => 'delete',
    ];

    /**
     * Handle an incoming request.
     * Implement role-based access control based on RoleEnum.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        $method = $request->route()->getActionMethod();
        $routeName = $request->route()->getName();

        // Route-based access control
        // Editors can only access content-entry routes
        // Admins and developers have full access
        if ($this->isBlueprintRoute($routeName)) {
            // Blueprint routes: content-type, custom-field, field-group
            if (in_array($user->role, ['user', 'editor'])) {
                abort(403, 'Unauthorized action. Admin access required for blueprint management.');
            }
        }

        // Report-only: role user hanya boleh halaman report kecuali invoice
        // (+ router dashboard yang mengarah ke report). RS yang boleh
        // diakses tetap dijaga pivot rs_dan_user via middleware rs.access.
        if ($user->role === RoleEnum::USER && ! $this->isReportOnlyAllowed($routeName)) {
            abort(403, 'Akun report-only: hanya halaman report yang boleh diakses.');
        }

        return $next($request);
    }

    /**
     * Halaman yang boleh dibuka role user (report-only).
     */
    private function isReportOnlyAllowed(?string $routeName): bool
    {
        // Closure tanpa nama (notifikasi/token user sendiri).
        if ($routeName === null) {
            return true;
        }

        // Router dashboard → redirect ke report default.
        if ($routeName === 'dashboard') {
            return true;
        }

        if (! str_starts_with($routeName, 'report-')) {
            return false;
        }

        // Invoice khusus internal — disembunyikan dari akun report-only.
        return ! str_starts_with($routeName, 'report-invoice');
    }

    /**
     * Check if the route is a blueprint management route.
     */
    protected function isBlueprintRoute(?string $routeName): bool
    {
        if (! $routeName) {
            return false;
        }

        $blueprintRoutes = [
            'cms-type',
            'field',
            'section',
        ];

        foreach ($blueprintRoutes as $blueprint) {
            if (str_contains($routeName, $blueprint)) {
                return true;
            }
        }

        return false;
    }
}
