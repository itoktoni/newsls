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

        return $next($request);
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
