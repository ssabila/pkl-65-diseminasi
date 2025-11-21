<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Riset;
use App\Models\Topic;
use App\Models\Visualization;
use Illuminate\Support\Facades\DB;

class DummyContentSeeder extends Seeder
{
    public function run()
    {
        // Setup Tipe Chart
        $barChartId = DB::table('visualization_types')->where('type_code', 'bar-chart')->value('id');
        if (!$barChartId) {
            $barChartId = DB::table('visualization_types')->insertGetId(['type_code' => 'bar-chart', 'type_name' => 'Bar Chart']);
        }
        $pieChartId = DB::table('visualization_types')->where('type_code', 'pie-chart')->value('id');
        if (!$pieChartId) {
            $pieChartId = DB::table('visualization_types')->insertGetId(['type_code' => 'pie-chart', 'type_name' => 'Pie Chart']);
        }

        // --- RISET I: KEPENDUDUKAN ---
        $riset1 = Riset::create(['name' => 'RISET I: KEPENDUDUKAN', 'slug' => 'riset-i', 'description' => 'Analisis demografi.', 'is_published' => true]);
        $topik1 = Topic::create(['riset_id' => $riset1->id, 'name' => 'Struktur Umur', 'slug' => 'struktur-umur', 'is_published' => true]);
        Visualization::create([
            'topic_id' => $topik1->id,
            'visualization_type_id' => $barChartId,
            'title' => 'Piramida Penduduk',
            'chart_data' => json_encode(['categories' => ['0-14', '15-64', '65+'], 'series' => [['name' => 'Laki', 'data' => [150, 400, 50]], ['name' => 'Perempuan', 'data' => [140, 410, 60]]]]),
            'interpretation' => 'Penduduk usia produktif mendominasi.', 'is_published' => true
        ]);
        Topic::create(['riset_id' => $riset1->id, 'name' => 'Sebaran Wilayah', 'slug' => 'sebaran-wilayah', 'is_published' => true]);

        // --- RISET II: EKONOMI ---
        $riset2 = Riset::create(['name' => 'RISET II: EKONOMI', 'slug' => 'riset-ii', 'description' => 'Analisis ekonomi.', 'is_published' => true]);
        $topik3 = Topic::create(['riset_id' => $riset2->id, 'name' => 'Sebaran UMKM', 'slug' => 'sebaran-umkm', 'is_published' => true]);
        Visualization::create([
            'topic_id' => $topik3->id,
            'visualization_type_id' => $pieChartId,
            'title' => 'Sektor UMKM',
            'chart_data' => json_encode(['labels' => ['Kuliner', 'Jasa', 'Retail'], 'series' => [50, 30, 20]]),
            'interpretation' => 'Kuliner mendominasi.', 'is_published' => true
        ]);
        
        // --- RISET V: MODAL SOSIAL (TAMBAHAN) ---
        $riset5 = Riset::create(['name' => 'RISET V: MODAL SOSIAL', 'slug' => 'riset-v', 'description' => 'Analisis modal sosial.', 'is_published' => true]);
        Topic::create(['riset_id' => $riset5->id, 'name' => 'Kepercayaan Publik', 'slug' => 'kepercayaan-publik', 'is_published' => true]);
        Topic::create(['riset_id' => $riset5->id, 'name' => 'Partisipasi Warga', 'slug' => 'partisipasi-warga', 'is_published' => true]);
    }
}