<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Visualization;
use App\Models\VisualizationType;
use App\Models\Topic;
use App\Models\Riset;

class TestPolygonSeeder extends Seeder
{
    public function run()
    {
        $type = VisualizationType::where('type_code', 'choropleth')->first();
        
        if (!$type) {
            $this->command->error('Choropleth type not found');
            return;
        }
        
        $testData = [
            'regions' => [
                [
                    'id' => '1',
                    'name' => 'Sleman',
                    'population' => 1200000, 
                    'density' => 2088,
                    'area' => 574.82,
                    'poverty' => 8.5,
                    'education' => 95.2
                ],
                [
                    'id' => '2',
                    'name' => 'Bantul',
                    'population' => 950000, 
                    'density' => 1874,
                    'area' => 506.85,
                    'poverty' => 12.3,
                    'education' => 89.7
                ],
                [
                    'id' => '3',
                    'name' => 'Gunungkidul',
                    'population' => 720000, 
                    'density' => 485,
                    'area' => 1485.36,
                    'poverty' => 15.8,
                    'education' => 85.4
                ],
                [
                    'id' => '4', 
                    'name' => 'Kulon Progo',
                    'population' => 440000, 
                    'density' => 750,
                    'area' => 586.27,
                    'poverty' => 11.2,
                    'education' => 88.9
                ],
                [
                    'id' => '5',
                    'name' => 'Yogyakarta Kota',
                    'population' => 420000, 
                    'density' => 12917,
                    'area' => 32.50,
                    'poverty' => 6.8,
                    'education' => 98.1
                ]
            ],
            'selectedVariable' => 'population',
            'variables' => ['population', 'density', 'area', 'poverty', 'education']
        ];
        
        // Get or create a topic for testing
        $topic = \App\Models\Topic::first();
        if (!$topic) {
            $riset = \App\Models\Riset::first();
            if (!$riset) {
                $riset = \App\Models\Riset::create([
                    'name' => 'Test Riset Choropleth',
                    'slug' => 'test-choropleth',
                    'description' => 'Test data untuk choropleth',
                    'is_published' => true
                ]);
            }
            $topic = \App\Models\Topic::create([
                'riset_id' => $riset->id,
                'name' => 'Test Choropleth Topic',
                'slug' => 'test-choropleth-topic',
                'description' => 'Test topic untuk choropleth',
                'is_published' => true,
                'order' => 1
            ]);
        }
        
        Visualization::updateOrCreate(
            ['title' => 'Peta Choropleth DIY'],
            [
                'visualization_type_id' => $type->id,
                'chart_data' => $testData,
                'interpretation' => 'Analisis sebaran data geografis Daerah Istimewa Yogyakarta dengan visualisasi choropleth.',
                'is_published' => true,
                'topic_id' => $topic->id
            ]
        );
        
        $this->command->info('Test polygon choropleth created successfully');
    }
}