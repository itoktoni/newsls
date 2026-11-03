<?php

namespace App\Concerns;

use App\Enums\RoleEnum;

trait ResolvesDashboardRole
{
    public function defaultDashboardRoute(?string $role): string
    {
        return match ($role) {
            RoleEnum::ADMIN, RoleEnum::DEVELOPER => 'dashboard.admin',
            RoleEnum::RS => 'dashboard.rs',
            // Report-only: tidak punya dashboard (hindari loop
            // dashboard ↔ dashboard.laundry) — langsung ke report.
            RoleEnum::USER => 'report-rekap-kotor.getTable',
            default => 'dashboard.laundry',
        };
    }
}
