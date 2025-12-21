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
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. GET EXISTING CHART TYPES (from VisualizationTypeSeeder)
        $barChartId           = DB::table('visualization_types')->where('type_code', 'bar-chart')->value('id');
        $pieChartId           = DB::table('visualization_types')->where('type_code', 'pie-chart')->value('id');
        $donutChartId         = DB::table('visualization_types')->where('type_code', 'donut-chart')->value('id');
        $lineChartId          = DB::table('visualization_types')->where('type_code', 'line-chart')->value('id');
        $areaChartId          = DB::table('visualization_types')->where('type_code', 'area-chart')->value('id');
        $stackedBarChartId    = DB::table('visualization_types')->where('type_code', 'stacked-bar-chart')->value('id');
        $groupedBarChartId    = DB::table('visualization_types')->where('type_code', 'grouped-bar-chart')->value('id');
        $histogramChartId     = DB::table('visualization_types')->where('type_code', 'histogram')->value('id');

        // =========================================================================
        // RISET I: EKONOMI
        // =========================================================================
        $riset1 = Riset::create([
            'name' => 'RISET I: EKONOMI', 
            'slug' => 'riset-i', 
            'description' => 'Analisis potensi ekonomi lokal, UMKM, dan sektor pariwisata.', 
            'is_published' => true
        ]);

        // --- TOPIK 1.1: Struktur Umur (Bar Chart) ---
        $topik1_1 = Topic::create(['riset_id' => $riset1->id, 'name' => 'Struktur Umur', 'slug' => 'struktur-umur', 'description' => 'Komposisi penduduk berdasarkan kelompok umur.', 'is_published' => true, 'order' => 1]);
        

        // --- TOPIK 1.2: Tren Kelahiran (Line Chart) ---
        $topik1_2 = Topic::create(['riset_id' => $riset1->id, 'name' => 'Tren Kelahiran', 'slug' => 'tren-kelahiran', 'description' => 'Dinamika angka kelahiran dalam lima tahun terakhir.', 'is_published' => true, 'order' => 2]);
        

        // --- TOPIK 1.3: Mobilitas Penduduk (Area Chart) ---
        $topik1_3 = Topic::create(['riset_id' => $riset1->id, 'name' => 'Mobilitas Penduduk', 'slug' => 'mobilitas-penduduk', 'description' => 'Pola pergerakan penduduk masuk dan keluar wilayah.', 'is_published' => true, 'order' => 3]);


        // =========================================================================
        // RISET II: KEPENDUDUKAN
        // =========================================================================
        $riset2 = Riset::create([
            'name' => 'RISET II: KEPENDUDUKAN', 
            'slug' => 'riset-ii', 
            'description' => 'Analisis mendalam mengenai demografi, fertilitas, dan mobilitas penduduk.', 
            'is_published' => true
        ]);

        // --- TOPIK 2.1: Sektor UMKM (Donut + Pie) ---
        $topik2_1 = Topic::create(['riset_id' => $riset2->id, 'name' => 'Potensi Sektor UMKM', 'slug' => 'potensi-umkm', 'description' => 'Sebaran dan proporsi jenis usaha mikro kecil dan menengah.', 'is_published' => true, 'order' => 1]);


        // --- TOPIK 2.2: Pendapatan Daerah (Stacked Bar Chart) ---
        $topik2_2 = Topic::create(['riset_id' => $riset2->id, 'name' => 'Pendapatan Daerah', 'slug' => 'pendapatan-daerah', 'description' => 'Realisasi pendapatan asli daerah per sektor.', 'is_published' => true, 'order' => 2]);
        

        // --- TOPIK 2.3: Pariwisata (Line Chart) ---
        $topik2_3 = Topic::create(['riset_id' => $riset2->id, 'name' => 'Sektor Pariwisata', 'slug' => 'pariwisata', 'description' => 'Tren kunjungan wisatawan domestik dan mancanegara.', 'is_published' => true, 'order' => 3]);
        

        // =========================================================================
        // RISET V: MODAL SOSIAL
        // =========================================================================
        $riset5 = Riset::create([
            'name' => 'RISET V: BIG DATA', 
            'slug' => 'riset-v', 
            'description' => 'Analisis tingkat kepercayaan, partisipasi, dan jaringan sosial masyarakat.', 
            'is_published' => true
        ]);

        // --- TOPIK 5.1: Kepercayaan Publik (Grouped Bar Chart) ---
        $topik5_1 = Topic::create(['riset_id' => $riset5->id, 'name' => 'Kepercayaan Publik', 'slug' => 'kepercayaan-publik', 'description' => 'Tingkat kepercayaan masyarakat terhadap institusi.', 'is_published' => true, 'order' => 1]);
        
        // --- TOPIK 5.2: Partisipasi Warga (Donut Chart) ---
        $topik5_2 = Topic::create(['riset_id' => $riset5->id, 'name' => 'Partisipasi Warga', 'slug' => 'partisipasi-warga', 'description' => 'Keterlibatan warga dalam kegiatan sosial kemasyarakatan.', 'is_published' => true, 'order' => 2]);
        

        // --- TOPIK 5.3: Jaringan Sosial (Area Chart) ---
        $topik5_3 = Topic::create(['riset_id' => $riset5->id, 'name' => 'Jaringan Sosial', 'slug' => 'jaringan-sosial', 'description' => 'Konektivitas dan keaktifan organisasi komunitas.', 'is_published' => true, 'order' => 3]);
        
            
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