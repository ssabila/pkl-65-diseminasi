# Aplikasi Diseminasi Riset & Visualisasi Data

## 📋 Deskripsi Singkat

Aplikasi **Diseminasi Riset & Visualisasi Data** adalah platform web modern yang dirancang untuk mengelola, mendistribusikan, dan memvisualisasikan hasil penelitian secara interaktif. Aplikasi ini memungkinkan pengguna untuk membuat berbagai jenis visualisasi data termasuk grafik, peta choropleth, dan dashboard analitik yang dapat disesuaikan.

Fitur utama:
- 📊 Berbagai jenis visualisasi data (charts, peta interaktif, dll)
- 🔐 Sistem autentikasi dan manajemen pengguna
- 📄 Manajemen dokumen penelitian
- 🗺️ Visualisasi peta choropleth interaktif
- 📈 Dashboard analitik dengan data real-time
- 🔍 Search dan filtering data
- 📱 UI responsif dengan Tailwind CSS
- 🎨 Dark mode support

---

## 🚀 Cara Implementasi

### Prerequisites (Persyaratan Awal)

Sebelum memulai, pastikan Anda telah menginstall:
- **PHP 8.2+**
- **Node.js 18+** dan npm/yarn
- **Composer**
- **Database** (SQLite, MySQL, atau PostgreSQL)

### Langkah-Langkah Instalasi

#### 1. Clone Repository dan Setup Environment
```bash
# Clone project
git clone <repository-url>
cd PKL-65-Diseminasi

# Copy file environment
cp .env.example .env

# Generate application key
php artisan key:generate
```

#### 2. Install Dependencies

**Backend (PHP/Laravel):**
```bash
composer install
```

**Frontend (Node.js/Vue):**
```bash
npm install
# atau jika menggunakan yarn
yarn install
```

#### 3. Setup Database

```bash
# Jalankan migration
php artisan migrate

# (Optional) Jalankan seeder untuk data awal
php artisan db:seed
```

#### 4. Build Assets Frontend

```bash
# Development
npm run dev

# Production
npm run build
```

#### 5. Jalankan Aplikasi

**Terminal 1 - Development Server:**
```bash
php artisan serve
```

Aplikasi akan berjalan di `http://localhost:8000`

**Terminal 2 - Vite Development Server:**
```bash
npm run dev
```

Vite akan serve assets di `http://localhost:5173`

---

## 🛠️ Teknologi & Tools yang Digunakan

### Backend Framework & Libraries

| Teknologi | Versi | Kegunaan |
|-----------|-------|----------|
| **Laravel** | ^12.0 | Framework web PHP utama |
| **Inertia.js** | ^2.0 | Server-side rendering untuk Vue components |
| **Laravel Sanctum** | ^4.0 | API authentication & token management |
| **Laravel Scout** | ^10.15 | Full-text search dengan Typesense |
| **Laravel Fortify** | ^1.23 | Autentikasi & user management |
| **Spatie Permission** | ^6.10 | Role-based access control (RBAC) |
| **Spatie Backup** | ^9.1 | Automated database & file backups |
| **Spatie Health** | ^1.34 | Application health monitoring |
| **Owen Auditing** | ^14.0 | Audit trail & activity logging |
| **Intervention Image** | ^3.11 | Image manipulation & processing |
| **Maatwebsite Excel** | ^3.1 | Excel file import/export |
| **Typesense** | ^5.1 | Advanced search engine |
| **Algolia Search** | ^5.30.0 | Full-text search alternative |

### Frontend Framework & Libraries

| Teknologi | Versi | Kegunaan |
|-----------|-------|----------|
| **Vue 3** | - | Progressive JavaScript framework |
| **Vite** | ^6.2.0 | Next-gen frontend build tool |
| **Tailwind CSS** | ^4.0.1 | Utility-first CSS framework |
| **TailwindCSS PostCSS** | ^4.0.1 | PostCSS plugin untuk Tailwind |
| **TailwindCSS Forms** | ^0.5.7 | Styled form components |
| **Heroicons Vue** | ^2.1.5 | Beautiful Vue icon library |
| **Leaflet** | ^1.9.4 | Interactive maps library |
| **Vue-Leaflet** | ^0.10.1 | Vue wrapper untuk Leaflet |
| **Leaflet.heat** | ^0.2.0 | Heatmap layer untuk peta |
| **ApexCharts** | ^4.7.0 | Modern charting library |
| **TanStack Vue Table** | ^8.20.5 | Headless table component |
| **FilePond** | ^4.32.7 | Drag & drop file upload |
| **html2canvas** | ^1.4.1 | Screenshot dan export functionality |
| **dotenv** | ^16.4.5 | Environment variable management |
| **Axios** | ^1.6.4 | HTTP client untuk API calls |
| **AOS** | ^2.3.4 | Animate on scroll library |

### Development Tools

| Tool | Versi | Kegunaan |
|------|-------|----------|
| **ESLint** | ^9.36.0 | JavaScript linting & code quality |
| **ESLint Vue Plugin** | ^10.5.0 | ESLint rules untuk Vue |
| **Prettier** | - | Code formatter |
| **PostCSS** | ^8.5.1 | CSS transformation tool |
| **Autoprefixer** | ^10.4.20 | Vendor prefixes otomatis |
| **Terser** | ^5.39.2 | JavaScript minifier |
| **Pest** | ^3.8 | Testing framework PHP |
| **PHPUnit** | - | Unit testing framework |
| **Faker** | ^1.9.1 | Generate fake data untuk testing |

### Project Structure & Config Tools

| File/Folder | Kegunaan |
|-------------|----------|
| **vite.config.js** | Konfigurasi Vite build tool |
| **tailwind.config.js** | Konfigurasi Tailwind CSS |
| **postcss.config.js** | Konfigurasi PostCSS processors |
| **eslint.config.js** | ESLint rules & configuration |
| **.env** | Environment variables configuration |

---

## 📁 Struktur Folder Utama

```
project/
├── app/
│   ├── Http/
│   │   ├── Controllers/        # Request handlers
│   │   │   ├── Admin/
│   │   │   ├── Auth/
│   │   │   ├── DashboardController.php
│   │   │   ├── DataController.php
│   │   │   └── PageController.php
│   │   ├── Middleware/         # HTTP middleware
│   │   └── Requests/           # Form request validation
│   ├── Models/
│   │   ├── Riset.php           # Research model
│   │   ├── Topic.php           # Topic/category model
│   │   ├── Visualization.php   # Visualization config
│   │   ├── Document.php        # Research documents
│   │   ├── User.php            # User model
│   │   └── LoginHistory.php    # Login tracking
│   ├── Services/               # Business logic services
│   ├── Traits/                 # Reusable class traits
│   └── Providers/              # Service providers
├── routes/
│   ├── web.php                 # Web routes
│   ├── api.php                 # API routes
│   └── console.php             # Artisan commands
├── resources/
│   ├── js/
│   │   ├── Pages/              # Vue page components
│   │   ├── Components/         # Reusable Vue components
│   │   ├── Layouts/            # Layout components
│   │   ├── app.js              # Main app entry
│   │   └── darkMode.js         # Dark mode functionality
│   ├── css/
│   │   └── app.css             # Global styles
│   └── views/                  # Blade templates (if any)
├── database/
│   ├── migrations/             # Database schema
│   ├── seeders/                # Data seeders
│   └── factories/              # Model factories for testing
├── public/
│   ├── geojson/                # GeoJSON data untuk peta
│   ├── sample-data/            # Sample data files
│   ├── test_choropleth.csv     # Test choropleth data
│   └── build/                  # Production assets (generated)
├── config/
│   ├── app.php                 # App configuration
│   ├── database.php            # Database config
│   ├── auth.php                # Authentication config
│   ├── permission.php          # Permission & role config
│   └── (lainnya)
├── storage/
│   ├── app/                    # User file uploads
│   ├── logs/                   # Application logs
│   └── framework/              # Cached files
├── tests/
│   ├── Feature/                # Feature tests
│   └── Unit/                   # Unit tests
├── vendor/                     # PHP dependencies (generated)
├── node_modules/               # NPM dependencies (generated)
└── bootstrap/                  # Application bootstrap
```

---

## 📊 Fitur Utama Aplikasi

### 1. Manajemen Riset
- Create, Read, Update, Delete penelitian
- Kategorisasi per topik
- Publish/draft management
- Audit trail untuk setiap perubahan

### 2. Visualisasi Data
Mendukung berbagai jenis visualisasi:
- **Charts**: Line, Bar, Pie, Area, Scatter
- **Maps**: Choropleth maps dengan data dinamis
- **Heatmaps**: Untuk visualisasi kepadatan data
- **Dashboards**: Custom dashboard dengan multiple charts

### 3. Peta Choropleth Interaktif
- Upload data CSV/Excel
- Auto-detect variabel dari data
- Interactive legend & tooltips
- Export as image (html2canvas)
- Multiple variable visualization
- Lihat [CHOROPLETH_GUIDE.md](CHOROPLETH_GUIDE.md) untuk detail lengkap

### 4. File Management
- Upload dokumen penelitian
- Drag & drop upload dengan FilePond
- File validation (tipe & ukuran)
- Download & sharing

### 5. User Authentication & Authorization
- Login/Register dengan email
- Role-based access control (Admin, Editor, Viewer)
- Activity logging
- Login history tracking

### 6. Search & Filter
- Full-text search dengan Typesense
- Advanced filtering options
- Algolia integration (alternative)

### 7. Dashboard Analytics
- Real-time data metrics
- Customizable widgets
- Data export (Excel, CSV)
- Chart export (PNG/JPG)

---

## 🔧 Perintah Artisan Penting

```bash
# Database
php artisan migrate              # Run migrations
php artisan migrate:rollback     # Rollback migrations
php artisan db:seed              # Run seeders
php artisan tinker               # Interactive PHP shell

# Cache & Config
php artisan cache:clear          # Clear application cache
php artisan config:cache         # Cache config
php artisan view:cache           # Cache views

# Search (Typesense)
php artisan scout:import         # Import models to Typesense
php artisan scout:flush          # Flush Typesense index

# Backup
php artisan backup:run           # Run backup
php artisan backup:list          # List backups

# Development
php artisan serve                # Start dev server
php artisan make:model Name      # Create new model
php artisan make:controller NameController   # Create controller
php artisan make:migration create_table      # Create migration
```

---

## 📝 NPM Scripts

```bash
# Development
npm run dev              # Start Vite dev server (hot reload)
npm run build            # Build for production
npm run lint             # Run ESLint for code quality
npm run format           # Format code dengan Prettier
```

---

## 🌍 Environment Variables (.env)

Beberapa environment variables penting:

```env
# App
APP_NAME="Diseminasi Riset"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=sqlite
DB_DATABASE=/path/to/database.sqlite

# Fortify
FORTIFY_GUARD=web
FORTIFY_HOME=/dashboard

# Typesense (Search)
TYPESENSE_HOST=localhost
TYPESENSE_PORT=8108
TYPESENSE_PROTOCOL=http
TYPESENSE_API_KEY=xyz

# Algolia (Alternative Search)
ALGOLIA_APP_ID=
ALGOLIA_SECRET=

# Email
MAIL_MAILER=log
MAIL_FROM_ADDRESS=noreply@example.com

# File Upload
FILESYSTEM_DISK=local
```

---

## 📚 Dokumentasi Tambahan

- **Panduan Choropleth**: Lihat [CHOROPLETH_GUIDE.md](CHOROPLETH_GUIDE.md)
- **Contributing**: Lihat [CONTRIBUTING.md](CONTRIBUTING.md)
- **License**: [LICENSE.md](LICENSE.md)
- **Security**: [SECURITY.md](SECURITY.md)

---

## 🧪 Testing

Aplikasi menggunakan **Pest** dan **PHPUnit** untuk testing:

```bash
# Run tests
./vendor/bin/pest

# Run specific test file
./vendor/bin/pest tests/Feature/DashboardTest.php

# Run with coverage
./vendor/bin/pest --coverage
```

---

## 📦 Deployment

### Build untuk Production
```bash
# Frontend
npm run build

# Clear cache
php artisan optimize

# Backup database
php artisan backup:run
```

### Environment untuk Production
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
```

---

## 🤝 Kontribusi

Untuk berkontribusi pada project ini:
1. Fork repository
2. Buat feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

Lihat [CONTRIBUTING.md](CONTRIBUTING.md) untuk detail lebih lanjut.

---

## 📄 License

Project ini dilisensikan di bawah **MIT License** - lihat file [LICENSE.md](LICENSE.md) untuk detail.

---

## 🆘 Troubleshooting

### Port sudah terpakai
```bash
php artisan serve --port=8001
```

### Build assets gagal
```bash
npm run dev
# Jika masih error, coba:
rm -rf node_modules
npm install
npm run dev
```

### Database error
```bash
php artisan migrate:fresh --seed
```

### Cache issues
```bash
php artisan cache:clear
php artisan view:clear
php artisan config:clear
```

---

## 📞 Support

Untuk pertanyaan atau masalah, hubungi tim development atau buat issue di repository.

---

**Last Updated**: December 2025
**Version**: 1.0
**Author**: PKL-65 Team
