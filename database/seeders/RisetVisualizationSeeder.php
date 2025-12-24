<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Riset;
use App\Models\Topic;
use App\Models\Visualization;
use Illuminate\Support\Facades\DB;

class RisetVisualizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get visualization type IDs
        $chartTypes = [
            'bar' => DB::table('visualization_types')->where('type_code', 'bar-chart')->value('id'),
            'pie' => DB::table('visualization_types')->where('type_code', 'pie-chart')->value('id'),
            'donut' => DB::table('visualization_types')->where('type_code', 'donut-chart')->value('id'),
            'line' => DB::table('visualization_types')->where('type_code', 'line-chart')->value('id'),
            'area' => DB::table('visualization_types')->where('type_code', 'area-chart')->value('id'),
            'stacked-bar' => DB::table('visualization_types')->where('type_code', 'stacked-bar-chart')->value('id'),
            'grouped-bar' => DB::table('visualization_types')->where('type_code', 'grouped-bar-chart')->value('id'),
            'histogram' => DB::table('visualization_types')->where('type_code', 'histogram')->value('id'),
            'population-pyramid' => DB::table('visualization_types')->where('type_code', 'population-pyramid')->value('id'),
        ];

        // Ambil riset dari RisetSeeder
        $riset1 = Riset::where('slug', 'riset-1')->first();
        $riset2 = Riset::where('slug', 'riset-2')->first();
        $riset5 = Riset::where('slug', 'riset-5')->first();

        if (!$riset1 || !$riset2 || !$riset5) {
            $this->command->warn('⚠ Riset 1, 2, atau 5 tidak ditemukan. Pastikan RisetSeeder berjalan terlebih dahulu.');
            return;
        }

        // ========================================
        // RISET 1
        // ========================================
        // Topic 1.1
        $topic1_1 = Topic::where('riset_id', $riset1->id)->where('order', 1)->first();
        if (!$topic1_1) {
            $topic1_1 = Topic::create([
                'riset_id' => $riset1->id,
                'name' => 'Sub Topik 1',
                'slug' => 'riset-1-sub-topik-1',
                'description' => '',
                'is_published' => true,
                'order' => 1
            ]);
        }

        Visualization::updateOrCreate(
            ['topic_id' => $topic1_1->id, 'title' => 'Grafik Riset 1 - Topik 1'],
            [
                'visualization_type_id' => $chartTypes['donut'],
                'chart_data' => [
                    'labels' => ['Data 1', 'Data 2', 'Data 3'],
                    'datasets' => [['name' => 'Nilai', 'data' => [30, 45, 25]]]
                ],
                'interpretation' => 'Visualisasi data untuk Riset 1 - Sub Topik 1',
                'order' => 1,
                'is_published' => true
            ]
        );

        // Topic 1.2
        $topic1_2 = Topic::where('riset_id', $riset1->id)->where('order', 2)->first();
        if (!$topic1_2) {
            $topic1_2 = Topic::create([
                'riset_id' => $riset1->id,
                'name' => 'Sub Topik 2',
                'slug' => 'riset-1-sub-topik-2',
                'description' => '',
                'is_published' => true,
                'order' => 2
            ]);
        }

        Visualization::updateOrCreate(
            ['topic_id' => $topic1_2->id, 'title' => 'Grafik Riset 1 - Topik 2'],
            [
                'visualization_type_id' => $chartTypes['bar'],
                'chart_data' => [
                    'labels' => ['Kategori A', 'Kategori B', 'Kategori C'],
                    'datasets' => [['name' => 'Nilai', 'data' => [40, 55, 35]]]
                ],
                'interpretation' => 'Visualisasi data untuk Riset 1 - Sub Topik 2',
                'order' => 1,
                'is_published' => true
            ]
        );

        // Topic 1.3
        $topic1_3 = Topic::where('riset_id', $riset1->id)->where('order', 3)->first();
        if (!$topic1_3) {
            $topic1_3 = Topic::create([
                'riset_id' => $riset1->id,
                'name' => 'Sub Topik 3',
                'slug' => 'riset-1-sub-topik-3',
                'description' => '',
                'is_published' => true,
                'order' => 3
            ]);
        }

        Visualization::updateOrCreate(
            ['topic_id' => $topic1_3->id, 'title' => 'Grafik Riset 1 - Topik 3'],
            [
                'visualization_type_id' => $chartTypes['line'],
                'chart_data' => [
                    'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
                    'datasets' => [['name' => 'Tren', 'data' => [25, 30, 28, 35, 40]]]
                ],
                'interpretation' => 'Visualisasi data untuk Riset 1 - Sub Topik 3',
                'order' => 1,
                'is_published' => true
            ]
        );

        // ========================================
        // RISET 2
        // ========================================
        // Topic 2.1
        $topic2_1 = Topic::where('riset_id', $riset2->id)->where('order', 1)->first();
        if (!$topic2_1) {
            $topic2_1 = Topic::create([
                'riset_id' => $riset2->id,
                'name' => 'Sub Topik 1',
                'slug' => 'riset-2-sub-topik-1',
                'description' => '',
                'is_published' => true,
                'order' => 1
            ]);
        }

        Visualization::updateOrCreate(
            ['topic_id' => $topic2_1->id, 'title' => 'Grafik Riset 2 - Topik 1'],
            [
                'visualization_type_id' => $chartTypes['pie'],
                'chart_data' => [
                    'labels' => ['Kategori 1', 'Kategori 2', 'Kategori 3', 'Kategori 4'],
                    'datasets' => [['name' => 'Persentase', 'data' => [25, 30, 20, 25]]]
                ],
                'interpretation' => 'Visualisasi data untuk Riset 2 - Sub Topik 1',
                'order' => 1,
                'is_published' => true
            ]
        );

        // Topic 2.2
        $topic2_2 = Topic::where('riset_id', $riset2->id)->where('order', 2)->first();
        if (!$topic2_2) {
            $topic2_2 = Topic::create([
                'riset_id' => $riset2->id,
                'name' => 'Sub Topik 2',
                'slug' => 'riset-2-sub-topik-2',
                'description' => '',
                'is_published' => true,
                'order' => 2
            ]);
        }

        Visualization::updateOrCreate(
            ['topic_id' => $topic2_2->id, 'title' => 'Grafik Riset 2 - Topik 2'],
            [
                'visualization_type_id' => $chartTypes['bar'],
                'chart_data' => [
                    'labels' => ['Item 1', 'Item 2', 'Item 3', 'Item 4'],
                    'datasets' => [['name' => 'Nilai', 'data' => [50, 45, 60, 55]]]
                ],
                'interpretation' => 'Visualisasi data untuk Riset 2 - Sub Topik 2',
                'order' => 1,
                'is_published' => true
            ]
        );

        // Topic 2.3
        $topic2_3 = Topic::where('riset_id', $riset2->id)->where('order', 3)->first();
        if (!$topic2_3) {
            $topic2_3 = Topic::create([
                'riset_id' => $riset2->id,
                'name' => 'Sub Topik 3',
                'slug' => 'riset-2-sub-topik-3',
                'description' => '',
                'is_published' => true,
                'order' => 3
            ]);
        }

        Visualization::updateOrCreate(
            ['topic_id' => $topic2_3->id, 'title' => 'Grafik Riset 2 - Topik 3'],
            [
                'visualization_type_id' => $chartTypes['area'],
                'chart_data' => [
                    'labels' => ['Q1', 'Q2', 'Q3', 'Q4'],
                    'datasets' => [['name' => 'Pertumbuhan', 'data' => [30, 40, 50, 60]]]
                ],
                'interpretation' => 'Visualisasi data untuk Riset 2 - Sub Topik 3',
                'order' => 1,
                'is_published' => true
            ]
        );

        // Topic 2.4
        $topic2_4 = Topic::where('riset_id', $riset2->id)->where('order', 4)->first();
        if (!$topic2_4) {
            $topic2_4 = Topic::create([
                'riset_id' => $riset2->id,
                'name' => 'Sub Topik 4',
                'slug' => 'riset-2-sub-topik-4',
                'description' => '',
                'is_published' => true,
                'order' => 4
            ]);
        }

        Visualization::updateOrCreate(
            ['topic_id' => $topic2_4->id, 'title' => 'Grafik Riset 2 - Topik 4'],
            [
                'visualization_type_id' => $chartTypes['donut'],
                'chart_data' => [
                    'labels' => ['Type A', 'Type B', 'Type C'],
                    'datasets' => [['name' => 'Distribusi', 'data' => [35, 40, 25]]]
                ],
                'interpretation' => 'Visualisasi data untuk Riset 2 - Sub Topik 4',
                'order' => 1,
                'is_published' => true
            ]
        );

        // ========================================
        // RISET 5
        // ========================================
        // Topic 5.1
        $topic5_1 = Topic::where('riset_id', $riset5->id)->where('order', 1)->first();
        if (!$topic5_1) {
            $topic5_1 = Topic::create([
                'riset_id' => $riset5->id,
                'name' => 'Sub Topik 1',
                'slug' => 'riset-5-sub-topik-1',
                'description' => '',
                'is_published' => true,
                'order' => 1
            ]);
        }

        Visualization::updateOrCreate(
            ['topic_id' => $topic5_1->id, 'title' => 'Grafik Riset 5 - Topik 1'],
            [
                'visualization_type_id' => $chartTypes['line'],
                'chart_data' => [
                    'labels' => ['2020', '2021', '2022', '2023', '2024'],
                    'datasets' => [['name' => 'Tren Data', 'data' => [45, 50, 55, 60, 65]]]
                ],
                'interpretation' => 'Visualisasi data untuk Riset 5 - Sub Topik 1',
                'order' => 1,
                'is_published' => true
            ]
        );

        // Topic 5.2
        $topic5_2 = Topic::where('riset_id', $riset5->id)->where('order', 2)->first();
        if (!$topic5_2) {
            $topic5_2 = Topic::create([
                'riset_id' => $riset5->id,
                'name' => 'Sub Topik 2',
                'slug' => 'riset-5-sub-topik-2',
                'description' => '',
                'is_published' => true,
                'order' => 2
            ]);
        }

        Visualization::updateOrCreate(
            ['topic_id' => $topic5_2->id, 'title' => 'Grafik Riset 5 - Topik 2'],
            [
                'visualization_type_id' => $chartTypes['grouped-bar'],
                'chart_data' => [
                    'labels' => ['Kategori A', 'Kategori B', 'Kategori C'],
                    'datasets' => [
                        ['name' => 'Series 1', 'data' => [30, 40, 35]],
                        ['name' => 'Series 2', 'data' => [25, 35, 40]]
                    ]
                ],
                'interpretation' => 'Visualisasi data untuk Riset 5 - Sub Topik 2',
                'order' => 1,
                'is_published' => true
            ]
        );

        // Topic 5.3
        $topic5_3 = Topic::where('riset_id', $riset5->id)->where('order', 3)->first();
        if (!$topic5_3) {
            $topic5_3 = Topic::create([
                'riset_id' => $riset5->id,
                'name' => 'Sub Topik 3',
                'slug' => 'riset-5-sub-topik-3',
                'description' => '',
                'is_published' => true,
                'order' => 3
            ]);
        }

        Visualization::updateOrCreate(
            ['topic_id' => $topic5_3->id, 'title' => 'Grafik Riset 5 - Topik 3'],
            [
                'visualization_type_id' => $chartTypes['pie'],
                'chart_data' => [
                    'labels' => ['Opsi 1', 'Opsi 2', 'Opsi 3', 'Opsi 4'],
                    'datasets' => [['name' => 'Proporsi', 'data' => [28, 32, 22, 18]]]
                ],
                'interpretation' => 'Visualisasi data untuk Riset 5 - Sub Topik 3',
                'order' => 1,
                'is_published' => true
            ]
        );

        // Topic 5.4
        $topic5_4 = Topic::where('riset_id', $riset5->id)->where('order', 4)->first();
        if (!$topic5_4) {
            $topic5_4 = Topic::create([
                'riset_id' => $riset5->id,
                'name' => 'Sub Topik 4',
                'slug' => 'riset-5-sub-topik-4',
                'description' => '',
                'is_published' => true,
                'order' => 4
            ]);
        }

        Visualization::updateOrCreate(
            ['topic_id' => $topic5_4->id, 'title' => 'Grafik Riset 5 - Topik 4'],
            [
                'visualization_type_id' => $chartTypes['bar'],
                'chart_data' => [
                    'labels' => ['Elemen 1', 'Elemen 2', 'Elemen 3', 'Elemen 4'],
                    'datasets' => [['name' => 'Kuantitas', 'data' => [55, 60, 50, 65]]]
                ],
                'interpretation' => 'Visualisasi data untuk Riset 5 - Sub Topik 4',
                'order' => 1,
                'is_published' => true
            ]
        );

        $this->command->info('✓ Riset Visualization seeded successfully');
    }
}
