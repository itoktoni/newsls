<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyVerified
{
    protected array $except = [
        'stories.generate',
        'stories.preview',
        'activities.generate-idea',
        'activities.ideas-list',
        'activities.ideas-users',
        'activities.idea-update',
        'activities.idea-delete',
        'activities.idea-batch-delete',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $routeName = optional($request->route())->getName();

        if (in_array($routeName, $this->except, true)) {
            return $next($request);
        }

        // Determine if API or Web request
        $isApi = $request->expectsJson();
        $bypassKey = $isApi ? 'api' : 'web';

        // Check email verification bypass
        if (config('langkahkecil.bypass.email_verification.'.$bypassKey, false)) {
            return $next($request);
        }

        // Only enforce verification if register_backend verification is enabled
        if (! config('langkahkecil.verification.register_backend', false)) {
            return $next($request);
        }

        // Check if user has verified via custom verified_at field
        if ($request->user() && ! $request->user()->verified_at) {
            if ($isApi) {
                return response()->json([
                    'message' => 'Akun belum terverifikasi. Silakan verifikasi terlebih dahulu.',
                    'needs_verification' => true,
                    'verification_gateway' => config('langkahkecil.verification.gateway', 'whatsapp'),
                ], 403);
            }

            return redirect()->route('verification.notice');
        }

        return $next($request);
    }
}
