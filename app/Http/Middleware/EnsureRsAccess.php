<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tolak akses langsung ke RS di luar hak user (pivot rs_dan_user).
 * Dipakai di grup route report web (param rs_id) dan grup API sanctum.
 * Cek input rs_id + route param {rsid} (download/rs/list/total API).
 */
class EnsureRsAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $rsId = $request->input('rs_id', $request->route('rsid'));
        if ($rsId !== null && $rsId !== '' && is_numeric($rsId)) {
            User::ensureRsAccess((int) $rsId);
        }

        return $next($request);
    }
}
