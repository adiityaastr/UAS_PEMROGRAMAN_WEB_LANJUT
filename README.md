# Sistem Perpustakaan Kampus

Sistem manajemen perpustakaan berbasis web yang dibangun dengan Laravel 12 untuk mengelola peminjaman buku, anggota perpustakaan, pembayaran denda, dan laporan peminjaman di lingkungan kampus.

## 📋 Daftar Isi

- [Fitur Utama](#-fitur-utama)
- [Teknologi yang Digunakan](#-teknologi-yang-digunakan)
- [Persyaratan Sistem](#-persyaratan-sistem)
- [Instalasi](#-instalasi)
- [Konfigurasi](#-konfigurasi)
- [Struktur Database](#-struktur-database)
- [Role dan Permission](#-role-dan-permission)
- [Cara Penggunaan](#-cara-penggunaan)
- [API Endpoints](#-api-endpoints)
- [Command Line Tools](#-command-line-tools)
- [Pengembangan](#-pengembangan)

## ✨ Fitur Utama

### 1. **Manajemen Buku**
- ✅ CRUD lengkap untuk data buku
- ✅ Kode buku otomatis (format: B001, B002, dst.)
- ✅ Informasi lengkap: judul, penulis, penerbit, tahun terbit, edisi, ISBN
- ✅ Manajemen stok buku (tambah/kurangi stok)
- ✅ Validasi stok minimal 2 buku saat penambahan

### 2. **Manajemen Anggota (Member)**
- ✅ CRUD lengkap untuk data anggota
- ✅ Kode unik otomatis (format: YYMMDDXXX - 9 digit)
- ✅ Data lengkap: foto profil, tempat/tanggal lahir, program studi, semester, tahun masuk, status aktif/non-aktif, nomor telepon, alamat
- ✅ Upload foto profil anggota
- ✅ 35+ pilihan program studi

### 3. **Manajemen Peminjaman**
- ✅ Peminjaman buku (maksimal 4 buku per anggota)
- ✅ Pengembalian buku dengan validasi denda
- ✅ Perpanjangan peminjaman (maksimal 1 kali, tidak boleh telat/tidak boleh ada denda)
- ✅ Pencarian peminjaman berdasarkan nama, NIM, atau judul buku
- ✅ Filter berdasarkan status (Dipinjam/Dikembalikan)
- ✅ Perhitungan denda otomatis (Rp 2.000/hari keterlambatan)
- ✅ Validasi: Member telat harus bayar denda dulu sebelum bisa mengembalikan buku

### 4. **Pembayaran Denda**
- ✅ Daftar peminjaman dengan denda belum dibayar
- ✅ Pencatatan pembayaran denda (Tunai/QRIS)
- ✅ Riwayat pembayaran denda
- ✅ Pembayaran bisa dicicil (partial payment)
- ✅ Validasi: Member telat harus lunas denda sebelum bisa mengembalikan buku

### 5. **Laporan Peminjaman**
- ✅ Laporan peminjaman per periode (bulanan)
- ✅ Statistik lengkap: total peminjaman, masih dipinjam, sudah dikembalikan, terlambat, total denda
- ✅ Statistik harian
- ✅ Buku terpopuler (Top 5)
- ✅ Detail peminjaman dengan informasi lengkap
- ✅ Fitur cetak laporan (print-friendly)

### 6. **Dashboard**
- ✅ Overview statistik: total peminjaman, total buku, denda tertunda, peminjaman terlambat
- ✅ 6 chart interaktif:
  - Peminjaman per bulan (6 bulan terakhir)
  - Status peminjaman (pie chart)
  - Buku terpopuler (Top 5)
  - Anggota per program studi (Top 8)
  - Peminjaman per hari dalam seminggu
  - Buku dengan stok terendah (Top 6)
- ✅ Buku terpopuler
- ✅ Peminjaman terbaru

### 7. **Autentikasi & Keamanan**
- ✅ Login/Logout
- ✅ Role-based access control (Admin, Petugas, Member)
- ✅ Middleware untuk proteksi route
- ✅ Password hashing dengan bcrypt

## 🛠 Teknologi yang Digunakan

### Backend
- **PHP 8.2+**
- **Laravel 12.0**
- **SQLite** (default) / MySQL / PostgreSQL

### Frontend
- **Blade Templating Engine**
- **Tailwind CSS 4.0**
- **Vite 7.0** (build tool)
- **JavaScript (Vanilla)**

### Tools & Libraries
- **Carbon** (date manipulation)
- **Laravel Tinker** (interactive shell)

## 📦 Persyaratan Sistem

- PHP >= 8.2
- Composer
- Node.js >= 18.x dan npm
- SQLite 3 (default) atau MySQL 5.7+ / PostgreSQL 10+
- Extension PHP yang diperlukan:
  - BCMath
  - Ctype
  - Fileinfo
  - JSON
  - Mbstring
  - OpenSSL
  - PDO
  - Tokenizer
  - XML

## 🚀 Instalasi

### 1. Clone Repository
```bash
git clone <repository-url>
cd UAS_PEMROGRAMAN_WEB_LANJUT
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 3. Setup Environment
```bash
# Copy file .env.example ke .env
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Konfigurasi Database

**Untuk SQLite (default):**
```bash
# Buat file database SQLite
touch database/database.sqlite
```

**Untuk MySQL/PostgreSQL:**
Edit file `.env` dan sesuaikan konfigurasi database:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database
DB_USERNAME=username
DB_PASSWORD=password
```

### 5. Jalankan Migration & Seeder
```bash
# Jalankan migration
php artisan migrate

# Jalankan seeder untuk data awal
php artisan db:seed
```

### 6. Setup Storage Link (untuk upload foto)
```bash
php artisan storage:link
```

### 7. Build Assets
```bash
# Development
npm run dev

# Production
npm run build
```

### 8. Jalankan Server
```bash
php artisan serve
```

Aplikasi akan berjalan di `http://127.0.0.1:8000`

## ⚙️ Konfigurasi

### File `.env`
Pastikan konfigurasi berikut sudah benar:

```env
APP_NAME="Sistem Perpustakaan Kampus"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=sqlite
DB_DATABASE=/path/to/database.sqlite

# Atau untuk MySQL
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=perpustakaan
# DB_USERNAME=root
# DB_PASSWORD=
```

## 🗄 Struktur Database

### Tabel `users`
- **Role**: admin, petugas, member
- **Field Member**: foto, tempat_lahir, tanggal_lahir, kode_unik (9 digit), program_studi, semester, tahun_masuk, status (aktif/non-aktif), nomor_telepon, alamat

### Tabel `books`
- Kode buku (unique), judul, penulis, penerbit, tahun, edisi, ISBN, stok

### Tabel `loans`
- Foreign key ke `users` dan `books`
- Tanggal pinjam, tanggal kembali (jadwal), tanggal kembali aktual
- Status: borrowed, returned
- Denda, renewal_count, renewed_at

### Tabel `fine_payments`
- Foreign key ke `loans` dan `users` (paid_by)
- Jumlah pembayaran, tanggal pembayaran, metode pembayaran (cash/qris), status, catatan

## 👥 Role dan Permission

### **Admin**
- ✅ Akses penuh ke semua fitur
- ✅ Manajemen buku (CRUD)
- ✅ Manajemen anggota (CRUD)
- ✅ Manajemen peminjaman (create, return, renew)
- ✅ Pembayaran denda
- ✅ Lihat laporan

### **Petugas**
- ✅ Manajemen anggota (CRUD)
- ✅ Manajemen peminjaman (create, return, renew)
- ✅ Pembayaran denda
- ✅ Lihat laporan
- ❌ Tidak bisa mengelola buku

### **Member**
- ❌ Tidak ada akses ke sistem (hanya data di database)

## 📖 Cara Penggunaan

### Login
1. Buka `http://127.0.0.1:8000`
2. Login dengan kredensial default:
   - **Admin**: `admin@example.com` / `password`
   - **Petugas**: `petugas@example.com` / `password`

### Menambah Buku (Admin)
1. Masuk ke menu **Buku** → **Tambah Buku**
2. Isi form: judul, penulis, penerbit, tahun, edisi, ISBN, stok
3. Kode buku akan otomatis dibuat (B001, B002, dst.)

### Menambah Anggota (Admin/Petugas)
1. Masuk ke menu **Anggota** → **Tambah Anggota**
2. Isi data lengkap anggota
3. Upload foto profil (opsional)
4. Kode unik akan otomatis dibuat (format: YYMMDDXXX)

### Peminjaman Buku (Petugas/Admin)
1. Masuk ke menu **Peminjaman** → **Pinjam Baru**
2. Pilih anggota dan buku (maksimal 4 buku)
3. Set tanggal pinjam dan tanggal kembali
4. Sistem akan mengurangi stok buku otomatis

### Pengembalian Buku (Petugas/Admin)
1. Masuk ke menu **Peminjaman**
2. Cari peminjaman yang ingin dikembalikan
3. Klik tombol **Kembalikan**
4. **Catatan**: Jika member telat dan masih ada denda, harus bayar denda dulu di menu **Pembayaran Denda**

### Perpanjangan Peminjaman (Petugas/Admin)
1. Di halaman **Peminjaman**, klik tombol **Perpanjang**
2. Pilih durasi perpanjangan (1-7 hari)
3. **Syarat**: 
   - Belum pernah diperpanjang
   - Tidak telat
   - Tidak ada denda yang belum dibayar

### Pembayaran Denda (Petugas/Admin)
1. Masuk ke menu **Pembayaran Denda**
2. Pilih peminjaman dengan denda belum dibayar
3. Klik **Bayar Denda**
4. Isi jumlah pembayaran, tanggal, metode (Tunai/QRIS)
5. Pembayaran bisa dicicil (partial payment)

### Laporan Peminjaman
1. Masuk ke menu **Laporan Peminjaman**
2. Pilih periode (bulan/tahun)
3. Klik **Filter**
4. Klik **Cetak Laporan** untuk print

## 🔌 API Endpoints

### Public API
- `GET /api/books/preview-code` - Preview kode buku yang akan dibuat
- `GET /api/user-loans/{userId}` - Cek jumlah peminjaman aktif user

## 🖥 Command Line Tools

### Update Denda Otomatis
```bash
php artisan loans:update-fines
```
Command ini akan mengupdate denda untuk semua peminjaman yang terlambat. Disarankan dijadwalkan di cron job untuk berjalan setiap hari.

### Tinker (Interactive Shell)
```bash
php artisan tinker
```
Untuk debugging dan testing di command line.

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

## 🧪 Seeder Data

Project ini dilengkapi dengan seeder untuk data awal:

### UserSeeder
- 1 Admin: `admin@example.com` / `password`
- 2 Petugas: `petugas@example.com`, `petugas2@example.com` / `password`
- 5 Member dengan data lengkap

### BookSeeder
- 10 buku contoh dengan data lengkap

### Menjalankan Seeder
```bash
# Jalankan semua seeder
php artisan db:seed

# Atau reset database dan jalankan seeder
php artisan migrate:fresh --seed

# Jalankan seeder tertentu
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=BookSeeder
```

## 🔒 Aturan Bisnis Penting

### Peminjaman
- ✅ Maksimal 4 buku per anggota secara bersamaan
- ✅ Stok buku otomatis berkurang saat peminjaman
- ✅ Stok buku otomatis bertambah saat pengembalian

### Perpanjangan
- ✅ Maksimal 1 kali perpanjangan per peminjaman
- ✅ Durasi perpanjangan: 1-7 hari
- ❌ Tidak bisa diperpanjang jika:
  - Sudah pernah diperpanjang
  - Peminjaman telat
  - Ada denda yang belum dibayar

### Pengembalian
- ❌ Tidak bisa mengembalikan jika:
  - Peminjaman telat DAN masih ada denda yang belum dibayar
- ✅ Denda otomatis dihitung saat pengembalian (jika telat)

### Denda
- ✅ Denda: Rp 2.000 per hari keterlambatan
- ✅ Denda dihitung otomatis setiap hari
- ✅ Pembayaran denda bisa dicicil
- ✅ Denda harus lunas sebelum bisa mengembalikan buku (jika telat)

## 🎨 Fitur UI/UX

- ✅ Design modern dengan Tailwind CSS
- ✅ Responsive design (mobile-friendly)
- ✅ Glass morphism effect
- ✅ Smooth animations
- ✅ Print-friendly untuk laporan
- ✅ Search dan filter yang powerful
- ✅ Highlight hasil pencarian
- ✅ Badge untuk status dan informasi penting
- ✅ Modal untuk konfirmasi dan form

## 📝 Catatan Pengembangan

### File Penting
- **Controllers**: `app/Http/Controllers/`
- **Models**: `app/Models/`
- **Views**: `resources/views/`
- **Routes**: `routes/web.php`
- **Migrations**: `database/migrations/`
- **Seeders**: `database/seeders/`

### Gate & Permission
Gate didefinisikan di `app/Providers/AppServiceProvider.php`:
- `isAdmin` - Hanya admin
- `isPetugas` - Hanya petugas
- `isPetugasOrAdmin` - Admin dan petugas

### Model Relationships
- `User` hasMany `Loan`
- `User` hasMany `FinePayment` (as paid_by)
- `Book` hasMany `Loan`
- `Loan` belongsTo `User` dan `Book`
- `Loan` hasMany `FinePayment`
- `FinePayment` belongsTo `Loan` dan `User` (paid_by)

## 🐛 Troubleshooting

### Error: "Failed opening required vendor/autoload.php"
```bash
composer install
```

### Error: "SQLSTATE[HY000] [14] unable to open database file"
Pastikan file `database/database.sqlite` sudah dibuat:
```bash
touch database/database.sqlite
chmod 664 database/database.sqlite
```

### Foto tidak muncul
Pastikan storage link sudah dibuat:
```bash
php artisan storage:link
```

### Error saat migrate
Pastikan database sudah dikonfigurasi dengan benar di `.env`

## 📄 License

MIT License

## 👨‍💻 Author

Dibuat untuk UAS Pemrograman Web Lanjut

---

**Catatan**: Project ini menggunakan Laravel 12 dengan PHP 8.2+. Pastikan environment development Anda sudah sesuai dengan persyaratan sistem.
