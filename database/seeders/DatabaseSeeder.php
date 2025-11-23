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
            
            // --- GANTI BAGIAN INI ---
            // Jangan panggil RisetSeeder/TopicSeeder bawaan agar tidak bentrok
            // RisetSeeder::class,
            // TopicSeeder::class,
            // VisualizationTypeSeeder::class, 

            // PANGGIL INI AGAR CHART MUNCUL
            DummyContentSeeder::class,
        ]);
    }
}