<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            SettingSeeder::class,
            PermissionRoleSeeder::class,
            UserSeeder::class,
            VisualizationTypeSeeder::class,
            RisetSeeder::class,
            TopicSeeder::class,
            RisetVisualizationSeeder::class,
        ]);
    }
}