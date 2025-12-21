# Panduan Penggunaan Peta Choropleth Interaktif

## Cara Menggunakan Fitur Choropleth

### 1. Persiapan Data
- Siapkan file CSV/Excel dengan format:
  ```
  id,nama_daerah,variabel1,variabel2,variabel3,...
  1,Jakarta,100,200,150
  2,Surabaya,90,180,140
  ...
  ```
- Kolom wajib: `id` dan `nama_daerah` (atau `region_name`)
- Dapat menambahkan kolom variabel sebanyak yang diinginkan

### 2. Langkah Upload
1. Pilih "Peta Choropleth" dari dropdown jenis grafik
2. Upload file CSV/Excel di bagian "Upload Data Peta Choropleth"
3. Sistem akan otomatis mendeteksi semua variabel yang tersedia
4. Pilih variabel pertama yang ingin ditampilkan

### 3. Fitur Interaktif
- **Selector Variabel**: Gunakan tombol-tombol variabel untuk mengganti tampilan peta
- **Popup Informasi**: Klik pada marker untuk melihat detail semua variabel
- **Legend**: Menampilkan gradasi warna berdasarkan nilai variabel aktif
- **Info Panel**: Menampilkan informasi jumlah variabel yang tersedia

### 4. Preview dan Publish
- Klik "Preview" untuk melihat peta interaktif
- Ganti variabel menggunakan tombol selector
- Klik "Konfirmasi & Publish" untuk menyimpan visualisasi

## File Test
File `test_choropleth.csv` tersedia di folder `public/` untuk testing dengan data:
- 15 kota di Indonesia
- 5 variabel: populasi, pendapatan, pendidikan, kesehatan, infrastruktur