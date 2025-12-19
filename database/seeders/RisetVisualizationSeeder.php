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

        // ========================================
        // RISET I: EKONOMI
        // ========================================
        $riset1 = Riset::create([
            'name' => 'RISET I: EKONOMI',
            'slug' => 'riset-i-ekonomi',
            'description' => 'Analisis potensi ekonomi lokal, UMKM, dan sektor pariwisata.',
            'is_published' => true
        ]);

        // Topic 1.1: Potensi Sektor UMKM
        $topic1_1 = Topic::create([
            'riset_id' => $riset1->id,
            'name' => 'Potensi Sektor UMKM',
            'slug' => 'potensi-umkm',
            'description' => 'Sebaran dan proporsi jenis usaha mikro kecil dan menengah.',
            'is_published' => true,
            'order' => 1
        ]);

        Visualization::create([
            'topic_id' => $topic1_1->id,
            'visualization_type_id' => $chartTypes['donut'],
            'title' => 'Proporsi Jenis Usaha UMKM',
            'chart_data' => [
                'labels' => ['Kuliner', 'Fashion & Tekstil', 'Jasa & Perdagangan', 'Kerajinan', 'Teknologi', 'Pertanian'],
                'datasets' => [['name' => 'Jumlah Usaha', 'data' => [35, 22, 18, 15, 7, 3]]]
            ],
            'interpretation' => 'Sektor kuliner mendominasi UMKM sebesar 35%, menunjukkan peluang besar dalam industri makanan. Diikuti fashion (22%) dan jasa perdagangan (18%).',
            'order' => 1,
            'is_published' => true
        ]);

        Visualization::create([
            'topic_id' => $topic1_1->id,
            'visualization_type_id' => $chartTypes['pie'],
            'title' => 'Sebaran UMKM per Kecamatan',
            'chart_data' => [
                'labels' => ['Kecamatan Pusat', 'Kecamatan Utara', 'Kecamatan Timur', 'Kecamatan Barat', 'Kecamatan Selatan'],
                'datasets' => [['name' => 'Jumlah UMKM', 'data' => [400, 280, 240, 200, 180]]]
            ],
            'interpretation' => 'Konsentrasi UMKM terbesar berada di Kecamatan Pusat (40%), mencerminkan pusat ekonomi wilayah. Penyebaran di kecamatan lain menunjukkan potensi pengembangan yang merata.',
            'order' => 2,
            'is_published' => true
        ]);

        Visualization::create([
            'topic_id' => $topic1_1->id,
            'visualization_type_id' => $chartTypes['bar'],
            'title' => 'Rata-rata Omzet Bulanan per Sektor UMKM',
            'chart_data' => [
                'labels' => ['Kuliner', 'Fashion', 'Jasa', 'Kerajinan', 'Teknologi', 'Pertanian'],
                'datasets' => [['name' => 'Omzet (Juta Rp)', 'data' => [45, 38, 52, 28, 75, 15]]]
            ],
            'interpretation' => 'Omzet tertinggi di sektor teknologi dan jasa, menunjukkan potensi ekonomi digital dan layanan profesional. Perlu pengembangan untuk sektor pertanian.',
            'order' => 3,
            'is_published' => true
        ]);

        // Topic 1.2: Pendapatan Daerah
        $topic1_2 = Topic::create([
            'riset_id' => $riset1->id,
            'name' => 'Pendapatan Asli Daerah (PAD)',
            'slug' => 'pendapatan-asli-daerah',
            'description' => 'Realisasi pendapatan asli daerah per sektor.',
            'is_published' => true,
            'order' => 2
        ]);

        Visualization::create([
            'topic_id' => $topic1_2->id,
            'visualization_type_id' => $chartTypes['stacked-bar'],
            'title' => 'Target vs Realisasi PAD 2024 (Miliar Rp)',
            'chart_data' => [
                'labels' => ['Pajak Hotel', 'Pajak Restoran', 'Retribusi', 'Pajak Hiburan', 'Pajak Parkir'],
                'datasets' => [
                    ['name' => 'Target', 'data' => [10, 15, 5, 8, 3]],
                    ['name' => 'Realisasi', 'data' => [12, 14, 6, 9, 4]]
                ]
            ],
            'interpretation' => 'Realisasi PAD mencapai 109% dari target (45 dari 41 miliar), dengan performa terbaik di sektor perhotelan. Kesuksesan ini menunjukkan pemulihan ekonomi pasca pandemi.',
            'order' => 1,
            'is_published' => true
        ]);

        Visualization::create([
            'topic_id' => $topic1_2->id,
            'visualization_type_id' => $chartTypes['grouped-bar'],
            'title' => 'Tren Realisasi PAD 2022-2024 per Kuartal',
            'chart_data' => [
                'labels' => ['Q1', 'Q2', 'Q3', 'Q4'],
                'datasets' => [
                    ['name' => '2022', 'data' => [8, 10, 11, 12]],
                    ['name' => '2023', 'data' => [10, 12, 13, 14]],
                    ['name' => '2024', 'data' => [11, 13, 15, 16]]
                ]
            ],
            'interpretation' => 'PAD menunjukkan pertumbuhan yang konsisten dari tahun ke tahun, dengan akselerasi pada kuartal 4. Momentum positif ini perlu dipertahankan melalui insentif usaha.',
            'order' => 2,
            'is_published' => true
        ]);

        Visualization::create([
            'topic_id' => $topic1_2->id,
            'visualization_type_id' => $chartTypes['grouped-bar'],
            'title' => 'Kontribusi PAD per Sektor (2024)',
            'chart_data' => [
                'labels' => ['Sektor Perhotelan', 'Sektor Hiburan', 'Sektor Transportasi', 'Sektor Perdagangan'],
                'datasets' => [
                    ['name' => 'Kontribusi (Miliar)', 'data' => [12, 10, 8, 6]],
                    ['name' => '% dari Total', 'data' => [32, 27, 22, 19]]
                ]
            ],
            'interpretation' => 'Sektor perhotelan menjadi tulang punggung PAD dengan kontribusi 32%, diikuti hiburan (27%) dan transportasi (22%). Diversifikasi sumber PAD perlu ditingkatkan.',
            'order' => 3,
            'is_published' => true
        ]);

        // Topic 1.3: Pariwisata
        $topic1_3 = Topic::create([
            'riset_id' => $riset1->id,
            'name' => 'Sektor Pariwisata',
            'slug' => 'sektor-pariwisata',
            'description' => 'Tren kunjungan wisatawan domestik dan mancanegara.',
            'is_published' => true,
            'order' => 3
        ]);

        Visualization::create([
            'topic_id' => $topic1_3->id,
            'visualization_type_id' => $chartTypes['grouped-bar'],
            'title' => 'Kunjungan Wisatawan 2024 (Ribu Orang)',
            'chart_data' => [
                'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                'datasets' => [
                    ['name' => 'Domestik', 'data' => [50, 45, 48, 70, 75, 85, 95, 90, 65, 60, 55, 58]],
                    ['name' => 'Mancanegara', 'data' => [8, 6, 7, 12, 15, 18, 22, 20, 14, 11, 9, 10]]
                ]
            ],
            'interpretation' => 'Puncak kunjungan terjadi pada Juli (95 ribu domestik, 22 ribu mancanegara), bersamaan dengan musim liburan. Momentum pariwisata menunjukkan pemulihan yang signifikan.',
            'order' => 1,
            'is_published' => true
        ]);

        Visualization::create([
            'topic_id' => $topic1_3->id,
            'visualization_type_id' => $chartTypes['donut'],
            'title' => 'Komposisi Wisatawan Domestik vs Mancanegara',
            'chart_data' => [
                'labels' => ['Wisatawan Domestik', 'Wisatawan Mancanegara'],
                'datasets' => [['name' => 'Persentase Kunjungan', 'data' => [88, 12]]]
            ],
            'interpretation' => 'Wisatawan domestik mendominasi (88%), menunjukkan potensi besar dalam pasar lokal. Pengembangan wisatawan mancanegara tetap strategis untuk diversifikasi pendapatan.',
            'order' => 2,
            'is_published' => true
        ]);

        Visualization::create([
            'topic_id' => $topic1_3->id,
            'visualization_type_id' => $chartTypes['bar'],
            'title' => 'Daya Tarik Wisata Paling Dikunjungi',
            'chart_data' => [
                'labels' => ['Pantai', 'Candi Bersejarah', 'Pasar Tradisional', 'Taman Kota', 'Museum', 'Air Terjun'],
                'datasets' => [['name' => 'Jumlah Kunjungan (Ribu)', 'data' => [280, 150, 120, 95, 60, 45]]]
            ],
            'interpretation' => 'Pantai menjadi destinasi utama dengan 280 ribu kunjungan, diikuti candi bersejarah (150 ribu). Perlu promosi lebih intensif untuk destinasi sekunder.',
            'order' => 3,
            'is_published' => true
        ]);

        // ========================================
        // RISET II: KEPENDUDUKAN
        // ========================================
        $riset2 = Riset::create([
            'name' => 'RISET II: KEPENDUDUKAN',
            'slug' => 'riset-ii-kependudukan',
            'description' => 'Analisis mendalam mengenai demografi, fertilitas, dan mobilitas penduduk.',
            'is_published' => true
        ]);

        // Topic 2.1: Struktur Umur
        $topic2_1 = Topic::create([
            'riset_id' => $riset2->id,
            'name' => 'Struktur Umur Penduduk',
            'slug' => 'struktur-umur-penduduk',
            'description' => 'Komposisi penduduk berdasarkan kelompok umur.',
            'is_published' => true,
            'order' => 1
        ]);

        Visualization::create([
            'topic_id' => $topic2_1->id,
            'visualization_type_id' => $chartTypes['population-pyramid'],
            'title' => 'Piramida Penduduk 2024',
            'chart_data' => [
                'labels' => ['0-4', '5-9', '10-14', '15-19', '20-24', '25-29', '30-34', '35-39', '40-44', '45-49', '50-54', '55-59', '60-64', '65+'],
                'datasets' => [
                    ['name' => 'Laki-laki', 'data' => [-230, -260, -290, -330, -360, -340, -310, -290, -260, -240, -210, -190, -160, -140]],
                    ['name' => 'Perempuan', 'data' => [220, 260, 290, 320, 350, 340, 310, 290, 260, 240, 210, 190, 160, 140]]
                ]
            ],
            'interpretation' => 'Piramida penduduk menunjukkan komposisi penduduk berdasarkan jenis kelamin dan kelompok umur, dengan mayoritas berada pada usia produktif (15-64 tahun) mencapai 67%.',
            'order' => 1,
            'is_published' => true
        ]);

        Visualization::create([
            'topic_id' => $topic2_1->id,
            'visualization_type_id' => $chartTypes['stacked-bar'],
            'title' => 'Perbandingan Penduduk Laki-laki vs Perempuan per Umur',
            'chart_data' => [
                'labels' => ['0-9', '10-19', '20-29', '30-39', '40-49', '50-59', '60-69', '70+'],
                'datasets' => [
                    ['name' => 'Laki-laki', 'data' => [930, 1080, 1400, 1230, 1000, 800, 640, 280]],
                    ['name' => 'Perempuan', 'data' => [900, 1050, 1450, 1280, 1020, 820, 700, 320]]
                ]
            ],
            'interpretation' => 'Rasio penduduk laki-laki dan perempuan hampir seimbang di semua kelompok umur, menunjukkan keseimbangan gender yang baik di wilayah ini.',
            'order' => 2,
            'is_published' => true
        ]);

        Visualization::create([
            'topic_id' => $topic2_1->id,
            'visualization_type_id' => $chartTypes['pie'],
            'title' => 'Kategori Usia Penduduk',
            'chart_data' => [
                'labels' => ['Usia Muda (0-14)', 'Usia Produktif (15-64)', 'Usia Lanjut (65+)'],
                'datasets' => [['name' => 'Persentase', 'data' => [18, 67, 15]]]
            ],
            'interpretation' => 'Proporsi penduduk usia produktif (15-64 tahun) mencapai 67%, yang mengindikasikan potensi bonus demografi yang kuat untuk pembangunan ekonomi.',
            'order' => 3,
            'is_published' => true
        ]);

        // Topic 2.2: Tren Kelahiran & Kematian
        $topic2_2 = Topic::create([
            'riset_id' => $riset2->id,
            'name' => 'Tren Kelahiran dan Kematian',
            'slug' => 'tren-kelahiran-kematian',
            'description' => 'Dinamika angka kelahiran dan kematian dalam lima tahun terakhir.',
            'is_published' => true,
            'order' => 2
        ]);

        Visualization::create([
            'topic_id' => $topic2_2->id,
            'visualization_type_id' => $chartTypes['grouped-bar'],
            'title' => 'Perbandingan TFR Aktual vs Target 2020-2024',
            'chart_data' => [
                'labels' => ['2020', '2021', '2022', '2023', '2024'],
                'datasets' => [
                    ['name' => 'TFR Aktual', 'data' => [2.42, 2.35, 2.18, 2.22, 2.15]],
                    ['name' => 'Target TFR', 'data' => [2.40, 2.35, 2.25, 2.20, 2.10]]
                ]
            ],
            'interpretation' => 'Angka kelahiran total mengalami tren penurunan dan telah mencapai target yang ditetapkan. Pencapaian ini mencerminkan keberhasilan program keluarga berencana dan peningkatan akses pendidikan perempuan.',
            'order' => 1,
            'is_published' => true
        ]);

        Visualization::create([
            'topic_id' => $topic2_2->id,
            'visualization_type_id' => $chartTypes['stacked-bar'],
            'title' => 'Perbandingan Angka Kelahiran dan Kematian',
            'chart_data' => [
                'labels' => ['2020', '2021', '2022', '2023', '2024'],
                'datasets' => [
                    ['name' => 'Angka Kelahiran (per 1000)', 'data' => [24.2, 23.5, 21.8, 22.2, 21.5]],
                    ['name' => 'Angka Kematian (per 1000)', 'data' => [8.2, 8.1, 7.9, 8.0, 8.2]]
                ]
            ],
            'interpretation' => 'Natural increase rate terus berkurang namun tetap positif. Angka kematian relatif stabil, menunjukkan peningkatan kualitas kesehatan masyarakat.',
            'order' => 2,
            'is_published' => true
        ]);

        Visualization::create([
            'topic_id' => $topic2_2->id,
            'visualization_type_id' => $chartTypes['grouped-bar'],
            'title' => 'Angka Kelahiran & Kematian Kasar per Kelompok Usia',
            'chart_data' => [
                'labels' => ['0-14', '15-49', '50+'],
                'datasets' => [
                    ['name' => 'Kelahiran', 'data' => [2, 85, 0]],
                    ['name' => 'Kematian', 'data' => [15, 25, 325]]
                ]
            ],
            'interpretation' => 'Kelahiran terskonsentrasi pada usia produktif (15-49 tahun), sementara kematian meningkat signifikan pada kelompok usia lanjut (50+), menunjukkan pola demografis normal.',
            'order' => 3,
            'is_published' => true
        ]);

        // Topic 2.3: Migrasi Penduduk
        $topic2_3 = Topic::create([
            'riset_id' => $riset2->id,
            'name' => 'Pola Migrasi Penduduk',
            'slug' => 'pola-migrasi',
            'description' => 'Pola pergerakan penduduk masuk dan keluar wilayah.',
            'is_published' => true,
            'order' => 3
        ]);

        Visualization::create([
            'topic_id' => $topic2_3->id,
            'visualization_type_id' => $chartTypes['stacked-bar'],
            'title' => 'Arus Migrasi Masuk vs Keluar (2024)',
            'chart_data' => [
                'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                'datasets' => [
                    ['name' => 'Migrasi Masuk', 'data' => [120, 135, 150, 180, 200, 220, 210, 195, 160, 145, 135, 125]],
                    ['name' => 'Migrasi Keluar', 'data' => [80, 85, 90, 110, 130, 140, 135, 120, 100, 95, 85, 80]]
                ]
            ],
            'interpretation' => 'Net migration menunjukkan trend positif sepanjang tahun, dengan puncak pada Juni-Juli. Pola ini menunjukkan daya tarik wilayah untuk menerima pendatang, khususnya saat musim liburan.',
            'order' => 1,
            'is_published' => true
        ]);

        Visualization::create([
            'topic_id' => $topic2_3->id,
            'visualization_type_id' => $chartTypes['donut'],
            'title' => 'Komposisi Migrasi Berdasarkan Tujuan',
            'chart_data' => [
                'labels' => ['Pekerjaan', 'Pendidikan', 'Keluarga', 'Bisnis', 'Lainnya'],
                'datasets' => [['name' => 'Persentase', 'data' => [42, 25, 18, 10, 5]]]
            ],
            'interpretation' => 'Alasan utama migrasi masuk adalah mencari pekerjaan (42%), diikuti oleh pendidikan (25%) dan alasan keluarga (18%), menunjukkan daya tarik ekonomi wilayah.',
            'order' => 2,
            'is_published' => true
        ]);

        // ========================================
        // RISET V: BIG DATA
        // ========================================
        $riset5 = Riset::create([
            'name' => 'RISET V: BIG DATA',
            'slug' => 'riset-v-big-data',
            'description' => 'Analisis data besar, pola penggunaan teknologi, dan literasi digital masyarakat.',
            'is_published' => true
        ]);

        // Topic 5.1: Literasi Digital & Adopsi Teknologi
        $topic5_1 = Topic::create([
            'riset_id' => $riset5->id,
            'name' => 'Literasi Digital & Adopsi Teknologi',
            'slug' => 'literasi-digital-adopsi-teknologi',
            'description' => 'Tingkat penguasaan teknologi dan penggunaan digital di kalangan masyarakat.',
            'is_published' => true,
            'order' => 1
        ]);

        Visualization::create([
            'topic_id' => $topic5_1->id,
            'visualization_type_id' => $chartTypes['stacked-bar'],
            'title' => 'Penguasaan Platform Digital per Kelompok Usia',
            'chart_data' => [
                'labels' => ['15-24 Tahun', '25-34 Tahun', '35-44 Tahun', '45-54 Tahun', '55+ Tahun'],
                'datasets' => [
                    ['name' => 'Mahir', 'data' => [95, 88, 72, 48, 25]],
                    ['name' => 'Cukup', 'data' => [4, 10, 20, 35, 30]],
                    ['name' => 'Belum Mahir', 'data' => [1, 2, 8, 17, 45]]
                ]
            ],
            'interpretation' => 'Generasi muda (15-24 tahun) menunjukkan penguasaan teknologi tertinggi (95%), sementara kelompok usia lanjut membutuhkan pendampingan lebih intensif. Upaya literasi digital merata menjadi prioritas.',
            'order' => 1,
            'is_published' => true
        ]);

        Visualization::create([
            'topic_id' => $topic5_1->id,
            'visualization_type_id' => $chartTypes['grouped-bar'],
            'title' => 'Penggunaan Perangkat Digital Utama',
            'chart_data' => [
                'labels' => ['Smartphone', 'Laptop', 'Tablet', 'Smart TV', 'Smartwatch'],
                'datasets' => [
                    ['name' => 'Urban', 'data' => [92, 68, 35, 42, 18]],
                    ['name' => 'Rural', 'data' => [45, 22, 8, 12, 3]]
                ]
            ],
            'interpretation' => 'Smartphone mendominasi penggunaan di kedua area (92% urban, 45% rural), mencerminkan aksesibilitas tinggi. Gap digital antara urban dan rural perlu ditutup melalui program inisiatif.',
            'order' => 2,
            'is_published' => true
        ]);

        Visualization::create([
            'topic_id' => $topic5_1->id,
            'visualization_type_id' => $chartTypes['pie'],
            'title' => 'Motivasi Penggunaan Internet Utama',
            'chart_data' => [
                'labels' => ['Hiburan', 'Pekerjaan', 'Edukasi', 'Komunikasi', 'Belanja Online', 'Lainnya'],
                'datasets' => [['name' => 'Persentase', 'data' => [35, 28, 18, 12, 6, 1]]]
            ],
            'interpretation' => 'Hiburan menjadi driver utama penggunaan internet (35%), diikuti kebutuhan pekerjaan (28%) dan edukasi (18%). Ini menunjukkan peluang besar dalam e-commerce dan pembelajaran digital.',
            'order' => 3,
            'is_published' => true
        ]);

        // Topic 5.2: Keamanan & Privacy Data
        $topic5_2 = Topic::create([
            'riset_id' => $riset5->id,
            'name' => 'Keamanan & Privasi Data Digital',
            'slug' => 'keamanan-privasi-data-digital',
            'description' => 'Kesadaran dan praktik keamanan data dalam kehidupan digital masyarakat.',
            'is_published' => true,
            'order' => 2
        ]);

        Visualization::create([
            'topic_id' => $topic5_2->id,
            'visualization_type_id' => $chartTypes['donut'],
            'title' => 'Kesadaran Keamanan Password',
            'chart_data' => [
                'labels' => ['Selalu Ganti Password', 'Jarang Ganti', 'Tidak Pernah Ganti', 'Tidak Tahu Password Penting'],
                'datasets' => [['name' => 'Persentase', 'data' => [28, 35, 22, 15]]]
            ],
            'interpretation' => 'Lebih dari setengah responden (57%) jarang atau tidak pernah mengganti password. Program edukasi keamanan digital sangat diperlukan untuk mengurangi risiko cyber.',
            'order' => 1,
            'is_published' => true
        ]);

        Visualization::create([
            'topic_id' => $topic5_2->id,
            'visualization_type_id' => $chartTypes['stacked-bar'],
            'title' => 'Pengalaman Masalah Keamanan Digital (Pernah Alami %)',
            'chart_data' => [
                'labels' => ['Phishing', 'Malware', 'Account Hacking', 'Data Leak', 'Fraud'],
                'datasets' => [
                    ['name' => 'Pernah Alami', 'data' => [22, 18, 15, 12, 8]],
                    ['name' => 'Belum Pernah', 'data' => [78, 82, 85, 88, 92]]
                ]
            ],
            'interpretation' => 'Phishing menjadi ancaman terbanyak yang dialami (22%), diikuti malware (18%) dan account hacking (15%). Diperlukan peningkatan awareness dan tools proteksi.',
            'order' => 2,
            'is_published' => true
        ]);

        Visualization::create([
            'topic_id' => $topic5_2->id,
            'visualization_type_id' => $chartTypes['bar'],
            'title' => 'Upaya Keamanan yang Dilakukan Pengguna',
            'chart_data' => [
                'labels' => ['Antivirus', 'VPN', 'Two-Factor Auth', 'Backup Data', 'Update OS', 'Enkripsi'],
                'datasets' => [['name' => 'Pengguna Aktif (%)', 'data' => [62, 35, 28, 45, 58, 22]]]
            ],
            'interpretation' => 'Antivirus dan update OS adalah praktik terpopuler (62% & 58%), namun two-factor authentication dan enkripsi masih rendah (28% & 22%). Edukasi tentang zero-trust security perlu diperkuat.',
            'order' => 3,
            'is_published' => true
        ]);

        // Topic 5.3: Dampak Sosial Teknologi Digital
        $topic5_3 = Topic::create([
            'riset_id' => $riset5->id,
            'name' => 'Dampak Sosial & Psikologis Teknologi',
            'slug' => 'dampak-sosial-psikologis-teknologi',
            'description' => 'Pengaruh penggunaan teknologi terhadap kesejahteraan sosial dan mental masyarakat.',
            'is_published' => true,
            'order' => 3
        ]);

        Visualization::create([
            'topic_id' => $topic5_3->id,
            'visualization_type_id' => $chartTypes['grouped-bar'],
            'title' => 'Jam Penggunaan Internet Harian per Segmen',
            'chart_data' => [
                'labels' => ['< 2 Jam', '2-4 Jam', '4-6 Jam', '6-8 Jam', '> 8 Jam'],
                'datasets' => [
                    ['name' => 'Pekerja', 'data' => [8, 22, 35, 28, 7]],
                    ['name' => 'Pelajar', 'data' => [2, 8, 20, 35, 35]],
                    ['name' => 'Non-Produktif', 'data' => [25, 30, 25, 15, 5]]
                ]
            ],
            'interpretation' => 'Pelajar menghabiskan waktu terbanyak di internet (70% lebih dari 6 jam/hari), menunjukkan ketergantungan pada platform digital. Screen time tinggi memerlukan monitoring kesehatan mata.',
            'order' => 1,
            'is_published' => true
        ]);

        Visualization::create([
            'topic_id' => $topic5_3->id,
            'visualization_type_id' => $chartTypes['stacked-bar'],
            'title' => 'Dampak Penggunaan Internet Sesuai Persepsi Pengguna',
            'chart_data' => [
                'labels' => ['Kesehatan Fisik', 'Kesehatan Mental', 'Hubungan Sosial', 'Prestasi/Karir', 'Finansial'],
                'datasets' => [
                    ['name' => 'Positif', 'data' => [15, 32, 42, 58, 45]],
                    ['name' => 'Netral', 'data' => [35, 40, 28, 22, 30]],
                    ['name' => 'Negatif', 'data' => [50, 28, 30, 20, 25]]
                ]
            ],
            'interpretation' => 'Internet dipersepsikan positif untuk prestasi/karir (58%) dan hubungan sosial (42%), namun 50% menganggap negatif untuk kesehatan fisik. Kesadaran health-tech perlu ditingkatkan.',
            'order' => 2,
            'is_published' => true
        ]);

        Visualization::create([
            'topic_id' => $topic5_3->id,
            'visualization_type_id' => $chartTypes['donut'],
            'title' => 'Tingkat Internet Addiction Screening',
            'chart_data' => [
                'labels' => ['Normal', 'Ringan', 'Sedang', 'Berat'],
                'datasets' => [['name' => 'Persentase', 'data' => [52, 28, 15, 5]]]
            ],
            'interpretation' => 'Sekitar 48% pengguna menunjukkan tanda-tanda internet addiction level ringan hingga berat. Program detox digital dan counseling digital wellbeing sangat direkomendasikan.',
            'order' => 3,
            'is_published' => true
        ]);

        $this->command->info('✓ Riset Visualization seeded successfully');
    }
}
