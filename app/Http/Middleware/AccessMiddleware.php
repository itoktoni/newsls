<?php

namespace App\Http\Middleware;

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
     * Modul yang tertutup total untuk role user (orang RS / customer
     * laundry): pengguna, audit, invoice, gudang, kelola menu mobile,
     * dan pengaturan aplikasi. Akses langsung = 403.
     */
    protected array $userBlockedModules = [
        'user',
        'users',
        'activity-log',
        'report-invoice',
        'warehouse',
        'mobile-menu',
        'settings',
        'appearance',
        'security',
        'native-bridge-test',
    ];

    /**
     * Aksi baca yang boleh untuk role user. Selain ini (create/update/
     * delete/capture/sync/...) = 403. getPrint/getExportExcel hanya
     * dipakai controller report (read-only) sehingga aman dibuka.
     */
    protected array $userReadActions = [
        'index',
        'getTable',
        'getShow',
        'getDetail',
        'getPrint',
        'getExportExcel',
        '__invoke',
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

        // ponytail: role user = orang RS / customer laundry — read-only.
        // Modul sensitif tertutup total; di luar itu hanya aksi baca.
        // Langsung abort 403 supaya akses langsung (URL) ikut ditolak,
        // bukan cuma disembunyikan dari menu.
        if ($user->role === 'user') {
            $this->denyRestrictedForUser($request);
        }

        return $next($request);
    }

    private function denyRestrictedForUser(Request $request): void
    {
        $routeName = $request->route()->getName();

        // Route tanpa nama di grup ini = utilitas mandiri milik user
        // sendiri (notifikasi-web, token centrifugo) — biarkan lewat.
        if (! is_string($routeName) || $routeName === '') {
            return;
        }

        $module = explode('.', $routeName, 2)[0];

        // Profil milik sendiri (profile.*) tetap boleh.
        if ($module === 'profile') {
            return;
        }

        if (in_array($module, $this->userBlockedModules, true)) {
            abort(403, 'Role user tidak boleh mengakses modul '.$module.'.');
        }

        $method = $request->route()->getActionMethod();
        if (is_string($method) && str_contains($method, '\\')) {
            // Single-action controller terdaftar sebagai nama class.
            $method = '__invoke';
        }
        if (! in_array($method, $this->userReadActions, true)) {
            abort(403, 'Role user hanya boleh melihat data.');
        }
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
