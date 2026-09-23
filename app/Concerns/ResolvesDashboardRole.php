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
            default => 'dashboard.laundry',
        };
    }
}
