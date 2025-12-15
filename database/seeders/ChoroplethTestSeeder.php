<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Visualization;
use App\Models\Topic;
use App\Models\VisualizationType;

class ChoroplethTestSeeder extends Seeder
{
    public function run(): void
    {
        // Get first topic
        $topic = Topic::first();
        
        // Get choropleth visualization type
        $choroplethType = VisualizationType::where('type_code', 'choropleth')->first();
        
        if (!$topic || !$choroplethType) {
            $this->command->error('Topic or Choropleth type not found');
            return;
        }

        // Load GeoJSON data
        $geojsonPath = public_path('geojson/yogyakarta.geojson');
        $geojsonData = null;
        
        if (file_exists($geojsonPath)) {
            $geojsonContent = file_get_contents($geojsonPath);
            $geojsonData = json_decode($geojsonContent, true);
        }

        // Create test choropleth data
        $chartData = [
            'regions' => [
                [
                    'id' => '1',
                    'nama_daerah' => 'Sleman',
                    'populasi' => 1200000,
                    'pendapatan' => 75000000,
                    'pendidikan' => 85.5,
                    'lat' => -7.6,
                    'lng' => 110.35
                ],
                [
                    'id' => '2',
                    'nama_daerah' => 'Bantul',
                    'populasi' => 950000,
                    'pendapatan' => 65000000,
                    'pendidikan' => 80.2,
                    'lat' => -7.9,
                    'lng' => 110.4
                ],
                [
                    'id' => '3',
                    'nama_daerah' => 'Gunungkidul',
                    'populasi' => 720000,
                    'pendapatan' => 55000000,
                    'pendidikan' => 75.8,
                    'lat' => -7.8,
                    'lng' => 110.65
                ],
                [
                    'id' => '4',
                    'nama_daerah' => 'Kulon Progo',
                    'populasi' => 450000,
                    'pendapatan' => 60000000,
                    'pendidikan' => 78.3,
                    'lat' => -7.8,
                    'lng' => 110.2
                ],
                [
                    'id' => '5',
                    'nama_daerah' => 'Yogyakarta Kota',
                    'populasi' => 390000,
                    'pendapatan' => 95000000,
                    'pendidikan' => 92.1,
                    'lat' => -7.785,
                    'lng' => 110.375
                ]
            ],
            'selectedVariable' => 'populasi',
            'variables' => ['populasi', 'pendapatan', 'pendidikan'],
            'geojson' => $geojsonData
        ];

        Visualization::create([
            'topic_id' => $topic->id,
            'visualization_type_id' => $choroplethType->id,
            'title' => 'Peta Choropleth Test - Populasi Yogyakarta',
            'interpretation' => 'Ini adalah peta choropleth yang menunjukkan distribusi populasi di wilayah DIY. Data menunjukkan bahwa Sleman memiliki populasi tertinggi diikuti oleh Bantul dan Gunungkidul.',
            'chart_data' => $chartData,
            'chart_options' => null,
            'order' => 1,
            'is_published' => true
        ]);

        $this->command->info('Choropleth test visualization created successfully');
    }
}