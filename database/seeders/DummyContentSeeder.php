<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Riset;
use App\Models\Topic;
use App\Models\Visualization;
use App\Models\Document; // Tambahkan Model Document
use Illuminate\Support\Facades\DB;

class DummyContentSeeder extends Seeder
{
    public function run()
    {
        // 1. BERSIH-BERSIH DATABASE
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Visualization::truncate();
        Topic::truncate();
        Riset::truncate();
        Document::truncate(); // Bersihkan tabel dokumen juga
        DB::table('visualization_types')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. SETUP TIPE CHART
        $barChartId   = DB::table('visualization_types')->insertGetId(['type_code' => 'bar-chart', 'type_name' => 'Bar Chart']);
        $pieChartId   = DB::table('visualization_types')->insertGetId(['type_code' => 'pie-chart', 'type_name' => 'Pie Chart']);
        $donutChartId = DB::table('visualization_types')->insertGetId(['type_code' => 'donut-chart', 'type_name' => 'Donut Chart']);
        $lineChartId  = DB::table('visualization_types')->insertGetId(['type_code' => 'line-chart', 'type_name' => 'Line Chart']);
        $areaChartId  = DB::table('visualization_types')->insertGetId(['type_code' => 'area-chart', 'type_name' => 'Area Chart']);

        // =========================================================================
        // RISET I: KEPENDUDUKAN
        // =========================================================================
        $riset1 = Riset::create([
            'name' => 'RISET I: KEPENDUDUKAN', 
            'slug' => 'riset-i', 
            'description' => 'Analisis mendalam mengenai demografi, fertilitas, dan mobilitas penduduk.', 
            'is_published' => true
        ]);

        // --- TOPIK 1.1: Struktur Umur (Bar Chart) ---
        $topik1_1 = Topic::create(['riset_id' => $riset1->id, 'name' => 'Struktur Umur', 'slug' => 'struktur-umur', 'description' => 'Komposisi penduduk berdasarkan kelompok umur dan jenis kelamin.', 'is_published' => true, 'order' => 1]);
        
        Visualization::create([
            'topic_id' => $topik1_1->id,
            'visualization_type_id' => $barChartId,
            'title' => 'Piramida Penduduk 2025',
            'chart_data' => [
                'categories' => ['0-14 Thn', '15-64 Thn', '65+ Thn'],
                'series' => [
                    ['name' => 'Laki-laki', 'data' => [1500, 4200, 500]],
                    ['name' => 'Perempuan', 'data' => [1450, 4300, 600]]
                ]
            ],
            'interpretation' => 'Penduduk usia produktif (15-64 tahun) mendominasi populasi, menunjukkan potensi bonus demografi yang besar bagi wilayah ini.',
            'order' => 1,
            'is_published' => true
        ]);

        // --- TOPIK 1.2: Tren Kelahiran (Line Chart) ---
        $topik1_2 = Topic::create(['riset_id' => $riset1->id, 'name' => 'Tren Kelahiran', 'slug' => 'tren-kelahiran', 'description' => 'Dinamika angka kelahiran dalam lima tahun terakhir.', 'is_published' => true, 'order' => 2]);
        
        Visualization::create([
            'topic_id' => $topik1_2->id,
            'visualization_type_id' => $lineChartId,
            'title' => 'Angka Kelahiran Total (TFR) 2020-2024',
            'chart_data' => [
                'categories' => ['2020', '2021', '2022', '2023', '2024'],
                'series' => [
                    ['name' => 'Total Kelahiran', 'data' => [2.4, 2.3, 2.1, 2.2, 2.1]]
                ]
            ],
            'interpretation' => 'Angka kelahiran cenderung stabil di angka 2.1, mendekati tingkat penggantian penduduk (replacement level).',
            'order' => 1,
            'is_published' => true
        ]);

        // --- TOPIK 1.3: Mobilitas Penduduk (Area Chart) ---
        $topik1_3 = Topic::create(['riset_id' => $riset1->id, 'name' => 'Mobilitas Penduduk', 'slug' => 'mobilitas-penduduk', 'description' => 'Pola pergerakan penduduk masuk dan keluar wilayah.', 'is_published' => true, 'order' => 3]);
        
        Visualization::create([
            'topic_id' => $topik1_3->id,
            'visualization_type_id' => $areaChartId,
            'title' => 'Arus Migrasi Masuk vs Keluar',
            'chart_data' => [
                'categories' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                'series' => [
                    ['name' => 'Migrasi Masuk', 'data' => [120, 150, 180, 200, 250, 300]],
                    ['name' => 'Migrasi Keluar', 'data' => [80, 90, 100, 110, 120, 130]]
                ]
            ],
            'interpretation' => 'Terjadi peningkatan signifikan pada migrasi masuk menjelang pertengahan tahun, kemungkinan didorong oleh faktor pekerjaan dan pendidikan.',
            'order' => 1,
            'is_published' => true
        ]);


        // =========================================================================
        // RISET II: EKONOMI
        // =========================================================================
        $riset2 = Riset::create([
            'name' => 'RISET II: EKONOMI', 
            'slug' => 'riset-ii', 
            'description' => 'Analisis potensi ekonomi lokal, UMKM, dan sektor pariwisata.', 
            'is_published' => true
        ]);

        // --- TOPIK 2.1: Sektor UMKM (Donut + Pie) ---
        $topik2_1 = Topic::create(['riset_id' => $riset2->id, 'name' => 'Potensi Sektor UMKM', 'slug' => 'potensi-umkm', 'description' => 'Sebaran dan proporsi jenis usaha mikro kecil dan menengah.', 'is_published' => true, 'order' => 1]);
        
        Visualization::create([
            'topic_id' => $topik2_1->id,
            'visualization_type_id' => $donutChartId,
            'title' => 'Proporsi Jenis Usaha',
            'chart_data' => [
                'labels' => ['Kuliner', 'Fashion', 'Jasa', 'Kerajinan', 'Teknologi'],
                'series' => [45, 20, 15, 15, 5]
            ],
            'interpretation' => 'Sektor kuliner mendominasi pasar UMKM sebesar 45%, menjadi tulang punggung ekonomi mikro.',
            'order' => 1,
            'is_published' => true
        ]);

        Visualization::create([
            'topic_id' => $topik2_1->id,
            'visualization_type_id' => $pieChartId,
            'title' => 'Sebaran UMKM per Kecamatan',
            'chart_data' => [
                'labels' => ['Kec. Kota', 'Kec. Selatan', 'Kec. Utara'],
                'series' => [300, 150, 50]
            ],
            'interpretation' => 'Kecamatan Kota menjadi pusat aktivitas ekonomi dengan jumlah UMKM terbanyak.',
            'order' => 2,
            'is_published' => true
        ]);

        // --- TOPIK 2.2: Pendapatan Daerah (Bar Chart) ---
        $topik2_2 = Topic::create(['riset_id' => $riset2->id, 'name' => 'Pendapatan Daerah', 'slug' => 'pendapatan-daerah', 'description' => 'Realisasi pendapatan asli daerah per sektor.', 'is_published' => true, 'order' => 2]);
        
        Visualization::create([
            'topic_id' => $topik2_2->id,
            'visualization_type_id' => $barChartId,
            'title' => 'Sumbangan PAD per Sektor (Miliar Rp)',
            'chart_data' => [
                'categories' => ['Pajak Hotel', 'Pajak Restoran', 'Retribusi', 'Pajak Hiburan'],
                'series' => [
                    ['name' => 'Target', 'data' => [10, 15, 5, 8]],
                    ['name' => 'Realisasi', 'data' => [12, 14, 6, 9]]
                ]
            ],
            'interpretation' => 'Sektor perhotelan dan hiburan melampaui target pendapatan, menunjukkan pulihnya sektor pariwisata.',
            'order' => 1,
            'is_published' => true
        ]);

        // --- TOPIK 2.3: Pariwisata (Line Chart) ---
        $topik2_3 = Topic::create(['riset_id' => $riset2->id, 'name' => 'Sektor Pariwisata', 'slug' => 'pariwisata', 'description' => 'Tren kunjungan wisatawan domestik dan mancanegara.', 'is_published' => true, 'order' => 3]);
        
        Visualization::create([
            'topic_id' => $topik2_3->id,
            'visualization_type_id' => $lineChartId,
            'title' => 'Kunjungan Wisatawan 2024',
            'chart_data' => [
                'categories' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                'series' => [
                    ['name' => 'Domestik', 'data' => [5000, 4500, 4800, 7000, 6500, 8000]],
                    ['name' => 'Mancanegara', 'data' => [200, 150, 300, 400, 350, 500]]
                ]
            ],
            'interpretation' => 'Puncak kunjungan terjadi pada bulan Juni (libur sekolah) dan April (libur lebaran).',
            'order' => 1,
            'is_published' => true
        ]);


        // =========================================================================
        // RISET V: MODAL SOSIAL
        // =========================================================================
        $riset5 = Riset::create([
            'name' => 'RISET V: MODAL SOSIAL', 
            'slug' => 'riset-v', 
            'description' => 'Analisis tingkat kepercayaan, partisipasi, dan jaringan sosial masyarakat.', 
            'is_published' => true
        ]);

        // --- TOPIK 5.1: Kepercayaan Publik (Bar Chart Horizontal) ---
        $topik5_1 = Topic::create(['riset_id' => $riset5->id, 'name' => 'Kepercayaan Publik', 'slug' => 'kepercayaan-publik', 'description' => 'Tingkat kepercayaan masyarakat terhadap institusi.', 'is_published' => true, 'order' => 1]);
        
        Visualization::create([
            'topic_id' => $topik5_1->id,
            'visualization_type_id' => $barChartId,
            'title' => 'Tingkat Kepercayaan terhadap Lembaga (%)',
            'chart_data' => [
                'categories' => ['Pemerintah Desa', 'Tokoh Agama', 'Polisi', 'Tetangga'],
                'series' => [
                    ['name' => 'Sangat Percaya', 'data' => [60, 85, 55, 70]],
                    ['name' => 'Cukup Percaya', 'data' => [30, 10, 35, 25]]
                ]
            ],
            'interpretation' => 'Tokoh agama memiliki tingkat kepercayaan tertinggi (85%) dibandingkan lembaga formal lainnya.',
            'order' => 1,
            'is_published' => true
        ]);

        // --- TOPIK 5.2: Partisipasi Warga (Donut Chart) ---
        $topik5_2 = Topic::create(['riset_id' => $riset5->id, 'name' => 'Partisipasi Warga', 'slug' => 'partisipasi-warga', 'description' => 'Keterlibatan warga dalam kegiatan sosial kemasyarakatan.', 'is_published' => true, 'order' => 2]);
        
        Visualization::create([
            'topic_id' => $topik5_2->id,
            'visualization_type_id' => $donutChartId,
            'title' => 'Keikutsertaan dalam Gotong Royong',
            'chart_data' => [
                'labels' => ['Selalu Hadir', 'Kadang-kadang', 'Jarang', 'Tidak Pernah'],
                'series' => [50, 30, 15, 5]
            ],
            'interpretation' => 'Semangat gotong royong masih sangat kuat, dengan 50% warga selalu hadir dalam kegiatan.',
            'order' => 1,
            'is_published' => true
        ]);

        // --- TOPIK 5.3: Jaringan Sosial (Area Chart) ---
        $topik5_3 = Topic::create(['riset_id' => $riset5->id, 'name' => 'Jaringan Sosial', 'slug' => 'jaringan-sosial', 'description' => 'Konektivitas dan keaktifan organisasi komunitas.', 'is_published' => true, 'order' => 3]);
        
        Visualization::create([
            'topic_id' => $topik5_3->id,
            'visualization_type_id' => $areaChartId,
            'title' => 'Pertumbuhan Komunitas Lokal (2020-2024)',
            'chart_data' => [
                'categories' => ['2020', '2021', '2022', '2023', '2024'],
                'series' => [
                    ['name' => 'Komunitas Pemuda', 'data' => [5, 8, 12, 15, 20]],
                    ['name' => 'Kelompok Tani', 'data' => [10, 10, 11, 13, 14]]
                ]
            ],
            'interpretation' => 'Komunitas pemuda mengalami pertumbuhan pesat, menunjukkan aktivisme generasi muda yang meningkat.',
            'order' => 1,
            'is_published' => true
        ]);

        // =========================================================================
        // BAGIAN DOKUMEN PUBLIKASI (DUMMY DATA)
        // =========================================================================
        
        // Kategori 1: Laporan Resmi
        Document::create([
            'name' => 'Laporan Akhir PKL Angkatan 65',
            'category' => 'Laporan Resmi',
            'description' => 'Dokumen lengkap hasil penelitian PKL 65 yang mencakup seluruh riset kependudukan, ekonomi, dan sosial.',
            'file_url' => '/files/laporan_akhir_pkl65.pdf', // Path dummy
            'created_at' => now(),
        ]);

        Document::create([
            'name' => 'Executive Summary: Potensi Desa',
            'category' => 'Laporan Resmi',
            'description' => 'Ringkasan eksekutif untuk pemangku kepentingan strategis mengenai potensi desa.',
            'file_url' => '/files/executive_summary.pdf',
            'created_at' => now()->subDays(2),
        ]);

        // Kategori 2: Infografis
        Document::create([
            'name' => 'Infografis: Bonus Demografi',
            'category' => 'Infografis',
            'description' => 'Visualisasi ringkas data kependudukan dalam satu lembar yang mudah dipahami.',
            'file_url' => '/files/infografis_kependudukan.png',
            'created_at' => now()->subDays(5),
        ]);

        Document::create([
            'name' => 'Peta Sebaran UMKM',
            'category' => 'Infografis',
            'description' => 'Peta tematik yang menggambarkan konsentrasi UMKM di berbagai wilayah.',
            'file_url' => '/files/peta_umkm.jpg',
            'created_at' => now()->subDays(5),
        ]);

        // Kategori 3: Dataset
        Document::create([
            'name' => 'Raw Data: Survei Rumah Tangga (Anonymized)',
            'category' => 'Dataset',
            'description' => 'Dataset mentah hasil survei rumah tangga dalam format CSV untuk analisis lanjutan.',
            'file_url' => '/files/dataset_ruta_v1.csv',
            'created_at' => now()->subWeeks(1),
        ]);

        Document::create([
            'name' => 'Raw Data: Survei Usaha/UMKM',
            'category' => 'Dataset',
            'description' => 'Data mentah karakteristik usaha di wilayah penelitian.',
            'file_url' => '/files/dataset_usaha.xlsx',
            'created_at' => now()->subWeeks(1),
        ]);
    }
}