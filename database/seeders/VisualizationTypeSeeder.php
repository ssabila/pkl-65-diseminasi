<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VisualizationTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['type_code' => 'bar-chart', 'type_name' => 'Bar Chart'],
            ['type_code' => 'pie-chart', 'type_name' => 'Pie Chart'],
            ['type_code' => 'donut-chart', 'type_name' => 'Donut Chart'],
            ['type_code' => 'line-chart', 'type_name' => 'Line Chart'],
            ['type_code' => 'area-chart', 'type_name' => 'Area Chart'],
            ['type_code' => 'peta', 'type_name' => 'Peta Heatmap'],
            ['type_code' => 'choropleth', 'type_name' => 'Peta Choropleth '],
        ];

        foreach ($types as $type) {
            DB::table('visualization_types')->updateOrInsert(
                ['type_code' => $type['type_code']],
                $type
            );
        }

        $this->command->info('✓ Visualization types seeded successfully');
    }
}
