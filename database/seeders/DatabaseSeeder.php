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
        VisualizationTypeSeeder::class, // ← WAJIB sebelum Riset
        RisetVisualizationSeeder::class, // ← Buat riset dulu
        UserSeeder::class, // ← Baru buat user dengan riset_id
    ]);
}

}