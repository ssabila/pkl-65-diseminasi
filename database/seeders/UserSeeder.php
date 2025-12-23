<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Riset;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat user admin
        $superuser = User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
        ]);

        // 2. Beri user ini role 'Super Admin'
        $superuser->assignRole('Super Admin');

        // 3. Buat user untuk masing-masing riset
        $risetUsers = [
            [
                'name' => 'User Riset 1',
                'email' => 'riset1@gmail.com',
                'password' => bcrypt('password'),
                'riset_slug' => 'riset-i-ekonomi', // Riset I: Ekonomi
            ],
            [
                'name' => 'User Riset 2',
                'email' => 'riset2@gmail.com',
                'password' => bcrypt('password'),
                'riset_slug' => 'riset-ii-kependudukan', // Riset II: Kependudukan
            ],
            [
                'name' => 'User Riset 5',
                'email' => 'riset5@gmail.com',
                'password' => bcrypt('password'),
                'riset_slug' => 'riset-v-big-data', // Riset V: Big Data
            ],
        ];

        foreach ($risetUsers as $userData) {
            $risetSlug = $userData['riset_slug'];
            unset($userData['riset_slug']);
            
            // Find riset by slug
            $riset = Riset::where('slug', $risetSlug)->first();
            
            if ($riset) {
                $userData['riset_id'] = $riset->id;
            }
            
            $user = User::create($userData);
            
            // Assign role (assuming there's a role for riset users, otherwise skip)
            if (Role::where('name', 'Riset User')->exists()) {
                $user->assignRole('Riset User');
            }
        }

        $this->command->info('✓ Users seeded successfully (Admin + 3 Riset Users)');
    }
}