<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // 1. Membuat Role "Super Admin" DULU
            RoleSeeder::class,
            
            // 2. BARU Membuat User Admin dan memberinya role
            UserSeeder::class,

            // 3. Membuat setting default
            SettingSeeder::class,
        ]);
    }
}