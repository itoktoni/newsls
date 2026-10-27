<?php

namespace App\Concerns;

use App\Enums\RoleEnum;

trait ResolvesDashboardRole
{
    public function defaultDashboardRoute(?string $role): string
    {
        return match ($role) {
            RoleEnum::ADMIN, RoleEnum::DEVELOPER => 'dashboard.admin',
            // ponytail: user = orang RS / customer laundry — dashboard RS
            // (tanpa gudang & pipeline laundry), bukan dashboard laundry.
            RoleEnum::RS, RoleEnum::USER => 'dashboard.rs',
            default => 'dashboard.laundry',
        };
    }
}
