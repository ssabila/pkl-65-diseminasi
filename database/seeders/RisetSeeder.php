<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Riset;

class RisetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $risets = [
            [
                'name' => 'Riset 1',
                'slug' => 'riset-1',
                'description' => '',
                'is_published' => true,
            ],
            [
                'name' => 'Riset 2',
                'slug' => 'riset-2',
                'description' => '',
                'is_published' => true,
            ],
            [
                'name' => 'Riset 3',
                'slug' => 'riset-3',
                'description' => '',
                'is_published' => true,
            ],
            [
                'name' => 'Riset 4',
                'slug' => 'riset-4',
                'description' => '',
                'is_published' => true,
            ],
            [
                'name' => 'Riset 5',
                'slug' => 'riset-5',
                'description' => '',
                'is_published' => true,
            ],
        ];

        foreach ($risets as $riset) {
            Riset::updateOrCreate(
                ['slug' => $riset['slug']],
                $riset
            );
        }

        $this->command->info('✓ Risets seeded successfully');
    }
}
