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
            ['type_code' => '100-stacked-bar-chart', 'type_name' => '100% Stacked Bar Chart'],
            ['type_code' => 'bar-chart', 'type_name' => 'Bar Chart'],
            ['type_code' => 'box-plot', 'type_name' => 'Box Plot'],
            ['type_code' => 'density-plot', 'type_name' => 'Density Plot'],
            ['type_code' => 'donut-chart', 'type_name' => 'Donut Chart'],
            ['type_code' => 'grouped-bar-chart', 'type_name' => 'Grouped Bar Chart'],
            ['type_code' => 'grouped-stacked-bar-chart', 'type_name' => 'Grouped Stacked Bar Chart'],
            ['type_code' => 'heatmap-matrix', 'type_name' => 'Heatmap Matrix'],
            ['type_code' => 'histogram', 'type_name' => 'Histogram'],
            ['type_code' => 'line-chart', 'type_name' => 'Line Chart'],
            ['type_code' => 'choropleth', 'type_name' => 'Peta Choropleth'],
            ['type_code' => 'peta', 'type_name' => 'Peta Heatmap'],
            ['type_code' => 'pie-chart', 'type_name' => 'Pie Chart'],
            ['type_code' => 'stacked-bar-chart', 'type_name' => 'Stacked Bar Chart'],
            ['type_code' => 'venn-diagram', 'type_name' => 'Venn Diagram'],
            ['type_code' => 'clustered-bar-chart', 'type_name' => 'Clustered Bar Chart'],
            ['type_code' => 'population-pyramid', 'type_name' => 'Population Pyramid'],
            ['type_code' => 'area-chart', 'type_name' => 'Area Chart'],
            ['type_code' => 'horizontal-stacked-bar-chart', 'type_name' => 'Horizontal Stacked Bar Chart'], // NEW
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
