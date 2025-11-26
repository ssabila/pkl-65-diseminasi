<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Riset;
use Spatie\Permission\Models\Role; // Pastikan 'use' ini ada

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $defaultRisetId = Riset::value('id');

        // 1. Buat HANYA user admin
        $superuser = User::create([
            'name' => 'Admin', // Ganti nama jika mau
            'email' => 'admin@admin.com', // Ganti email jika mau
            'password' => bcrypt('password'), // Ganti password jika mau
            'riset_id' => $defaultRisetId,
        ]);

        // 2. Beri user ini role 'Super Admin' (yang dibuat di RoleSeeder)
        $superuser->assignRole('Super Admin');
    }
}