<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Riset;
use App\Models\Topic;
use App\Models\Visualization;
use Illuminate\Support\Facades\DB; // Penting: Kita pakai DB facade

class DummyContentSeeder extends Seeder
{
    public function run()
    {
        // 1. Masukin Tipe Visualisasi Pakai 'DB::table' (Biar gak kena error timestamp)
        // Kita cek dulu biar gak duplikat
        $barChartId = DB::table('visualization_types')->where('type_code', 'bar-chart')->value('id');
        if (!$barChartId) {
            $barChartId = DB::table('visualization_types')->insertGetId([
                'type_code' => 'bar-chart',
                'type_name' => 'Bar Chart'
            ]);
        }

        $pieChartId = DB::table('visualization_types')->where('type_code', 'pie-chart')->value('id');
        if (!$pieChartId) {
            $pieChartId = DB::table('visualization_types')->insertGetId([
                'type_code' => 'pie-chart',
                'type_name' => 'Pie Chart'
            ]);
        }

        // --- RISET 1: EKONOMI ---
        // Riset, Topic, Visualization punya timestamps, jadi aman pakai Model biasa
        $riset1 = Riset::create([
            'name' => 'Analisis Dampak Ekonomi Pasca Pandemi',
            'slug' => 'analisis-dampak-ekonomi-2024',
            'description' => 'Penelitian mengenai pemulihan UMKM di Jawa Barat.',
            'is_published' => true
        ]);

        $topik1 = Topic::create([
            'riset_id' => $riset1->id,
            'name' => 'Sebaran UMKM',
            'slug' => 'sebaran-umkm',
            'is_published' => true
        ]);

        Visualization::create([
            'topic_id' => $topik1->id,
            'visualization_type_id' => $barChartId, // Pakai ID yang kita dapet dari DB::table tadi
            'title' => 'Jumlah UMKM per Kabupaten',
            'chart_data' => json_encode([
                'categories' => ['Bogor', 'Bandung', 'Bekasi', 'Depok'],
                'series' => [
                    [
                        'name' => 'Jumlah UMKM (Ribu)',
                        'data' => [120, 200, 150, 90]
                    ]
                ]
            ]),
            'interpretation' => 'Data menunjukkan bahwa Kabupaten Bandung memiliki jumlah UMKM tertinggi.',
            'is_published' => true
        ]);

        // --- RISET 2: SOSIAL ---
        $riset2 = Riset::create([
            'name' => 'Indeks Kebahagiaan Mahasiswa 2025',
            'slug' => 'indeks-kebahagiaan-mahasiswa-2025',
            'description' => 'Survei terhadap mahasiswa tingkat akhir.',
            'is_published' => true
        ]);

        $topik2 = Topic::create([
            'riset_id' => $riset2->id,
            'name' => 'Tingkat Stress',
            'slug' => 'tingkat-stress',
            'is_published' => true
        ]);

        Visualization::create([
            'topic_id' => $topik2->id,
            'visualization_type_id' => $pieChartId,
            'title' => 'Penyebab Utama Stress',
            'chart_data' => json_encode([
                'labels' => ['Skripsi', 'Keuangan', 'Asmara', 'Organisasi'],
                'series' => [60, 20, 10, 10]
            ]),
            'interpretation' => 'Sebagian besar mahasiswa (60%) merasa stress akibat beban Tugas Akhir/Skripsi.',
            'is_published' => true
        ]);
    }
}