<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Riset;
use App\Models\Topic;
use Illuminate\Support\Str;

class TopicSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua riset
        $risets = Riset::all();

        foreach ($risets as $index => $riset) {

            // Jumlah sub topik per riset (kamu bisa atur custom jika perlu)
            $totalTopics = match ($riset->slug) {
                'riset-1' => 3,
                'riset-2' => 4,
                'riset-3' => 4,
                'riset-4' => 2,
                'riset-5' => 4,
                default => 3,
            };

            for ($i = 1; $i <= $totalTopics; $i++) {

                $name = "Sub Topik $i";
                $slug = Str::slug("{$riset->slug}-{$name}"); 
                // contoh output: riset-1-sub-topik-1

                Topic::updateOrCreate(
                    [
                        'slug' => $slug,
                        'riset_id' => $riset->id,
                    ],
                    [
                        'name' => $name,
                        'description' => '',
                        'order' => $i,
                        'is_published' => true,
                    ]
                );
            }
        }

        $this->command->info("✓ Topics seeded successfully");
    }
}
