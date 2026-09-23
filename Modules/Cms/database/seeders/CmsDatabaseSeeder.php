<?php

namespace Modules\Cms\Database\Seeders;

use Illuminate\Database\Seeder;

class CmsDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(CompanyProfileSeeder::class);
    }
}
