<?php

return [
    'understock_threshold' => 0.6,
    'top_ruangan' => 12,
    'chart_days' => 7,
    // Detik — payload KPI/sebaran/chart dashboard di-cache per scope.
    'cache_ttl' => (int) env('DASHBOARD_CACHE_TTL', 60),
];
