<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

/**
 * Cache pendek untuk payload dashboard (array plain, bukan object chart).
 *
 * Key: dash:{gen}:{scope}:{segment}
 * - gen   → di-bump oleh flush() setelah commit write path (basi semua key lama).
 * - scope → role + hash RS yang boleh dilihat (cegah bocor antar RS).
 * - segment → mis. "kpi", "sebaran", "chart:kotorBersih:7".
 */
class DashboardCache
{
    public const GEN_KEY = 'dashboard:gen';

    public static function remember(string $scope, string $segment, \Closure $callback): mixed
    {
        $ttl = max(1, (int) config('dashboard.cache_ttl', 60));
        $key = self::key($scope, $segment);

        return Cache::remember($key, $ttl, $callback);
    }

    /**
     * Basikan seluruh cache dashboard (panggil SETELAH DB::commit).
     */
    public static function flush(): void
    {
        try {
            Cache::forever(self::GEN_KEY, self::generation() + 1);
        } catch (\Throwable) {
            // cache store down — TTL tetap jadi batas kebasahan.
        }
    }

    public static function key(string $scope, string $segment): string
    {
        return 'dash:'.self::generation().':'.$scope.':'.$segment;
    }

    /**
     * Scope per user login: "{role}:{hash rs}" atau "{role}:all".
     */
    public static function userScope(): string
    {
        $user = auth()->user();
        $role = (string) ($user?->role ?? 'guest');

        if (! $user) {
            return $role.':all';
        }

        $ids = User::allowedRsIds();

        if ($ids === null) {
            return $role.':all';
        }

        sort($ids);

        return $role.':'.md5(implode(',', $ids));
    }

    private static function generation(): int
    {
        try {
            return max(1, (int) Cache::get(self::GEN_KEY, 1));
        } catch (\Throwable) {
            return 1;
        }
    }
}
