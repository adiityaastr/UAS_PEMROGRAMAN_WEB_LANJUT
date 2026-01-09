-- ============================================
-- DATABASE SQL LENGKAP
-- Sistem Perpustakaan
-- ============================================

-- Hapus database jika sudah ada (opsional, hati-hati!)
-- DROP DATABASE IF EXISTS perpustakaan;

-- Buat database baru (opsional, sesuaikan dengan nama database Anda)
-- CREATE DATABASE perpustakaan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE perpustakaan;

-- ============================================
-- TABEL USERS
-- ============================================
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `tempat_lahir` varchar(255) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `kode_unik` varchar(255) DEFAULT NULL,
  `program_studi` varchar(255) DEFAULT NULL,
  `semester` int(11) DEFAULT NULL,
  `tahun_masuk` int(11) DEFAULT NULL,
  `status` enum('aktif','non-aktif') NOT NULL DEFAULT 'aktif',
  `nomor_telepon` varchar(255) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'petugas',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_kode_unik_unique` (`kode_unik`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABEL PASSWORD RESET TOKENS
-- ============================================
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABEL SESSIONS
-- ============================================
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABEL CACHE
-- ============================================
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABEL JOBS
-- ============================================
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABEL BOOKS
-- ============================================
CREATE TABLE IF NOT EXISTS `books` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `publisher` varchar(255) DEFAULT NULL,
  `year` year(4) DEFAULT NULL,
  `edition` varchar(255) DEFAULT NULL,
  `isbn` varchar(255) DEFAULT NULL,
  `stock` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `books_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABEL LOANS
-- ============================================
CREATE TABLE IF NOT EXISTS `loans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `book_id` bigint(20) unsigned NOT NULL,
  `loan_date` date NOT NULL,
  `return_date` date NOT NULL,
  `actual_return_date` date DEFAULT NULL,
  `status` enum('borrowed','returned') NOT NULL DEFAULT 'borrowed',
  `fine` decimal(10,2) NOT NULL DEFAULT 0.00,
  `renewal_count` int(11) NOT NULL DEFAULT 0,
  `renewed_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `loans_user_id_foreign` (`user_id`),
  KEY `loans_book_id_foreign` (`book_id`),
  CONSTRAINT `loans_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `loans_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABEL FINE PAYMENTS
-- ============================================
CREATE TABLE IF NOT EXISTS `fine_payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `loan_id` bigint(20) unsigned NOT NULL,
  `paid_by` bigint(20) unsigned NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_method` enum('cash','qris') NOT NULL DEFAULT 'cash',
  `status` enum('pending','paid','cancelled') NOT NULL DEFAULT 'paid',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fine_payments_loan_id_foreign` (`loan_id`),
  KEY `fine_payments_paid_by_foreign` (`paid_by`),
  CONSTRAINT `fine_payments_loan_id_foreign` FOREIGN KEY (`loan_id`) REFERENCES `loans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fine_payments_paid_by_foreign` FOREIGN KEY (`paid_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABEL MIGRATIONS
-- ============================================
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- DATA SEEDER: USERS
-- ============================================
-- Password untuk semua user: 'password' (sudah di-hash dengan bcrypt)

-- Admin
INSERT INTO `users` (`name`, `email`, `password`, `role`, `status`, `created_at`, `updated_at`) VALUES
('Admin Perpustakaan', 'admin@example.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'aktif', NOW(), NOW());

-- Petugas
INSERT INTO `users` (`name`, `email`, `password`, `role`, `status`, `kode_unik`, `created_at`, `updated_at`) VALUES
('Petugas Utama', 'petugas@example.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'petugas', 'aktif', 'PTG001', NOW(), NOW()),
('Petugas Layanan Sirkulasi', 'petugas2@example.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'petugas', 'aktif', 'PTG002', NOW(), NOW());

-- Members (Mahasiswa)
INSERT INTO `users` (`name`, `email`, `password`, `role`, `foto`, `tempat_lahir`, `tanggal_lahir`, `kode_unik`, `program_studi`, `semester`, `tahun_masuk`, `status`, `nomor_telepon`, `alamat`, `created_at`, `updated_at`) VALUES
('Andi Pratama', 'andi.pratama@example.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'member', NULL, 'Bandung', '2004-01-15', 'MHS240001', 'Informatika', 2, 2024, 'aktif', '081234567890', 'Jl. Merdeka No. 1, Bandung', NOW(), NOW()),
('Budi Santoso', 'budi.santoso@example.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'member', NULL, 'Jakarta', '2003-11-02', 'MHS230002', 'Sistem Informasi', 4, 2023, 'aktif', '081298765432', 'Jl. Sudirman No. 10, Jakarta', NOW(), NOW()),
('Citra Lestari', 'citra.lestari@example.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'member', NULL, 'Yogyakarta', '2004-05-20', 'MHS240003', 'Teknik Komputer', 2, 2024, 'aktif', '081377788899', 'Jl. Kaliurang Km 5, Yogyakarta', NOW(), NOW()),
('Dewi Anggraini', 'dewi.anggraini@example.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'member', NULL, 'Surabaya', '2002-09-08', 'MHS220004', 'Manajemen', 6, 2022, 'aktif', '081355566677', 'Jl. Pemuda No. 20, Surabaya', NOW(), NOW()),
('Eko Wahyudi', 'eko.wahyudi@example.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'member', NULL, 'Semarang', '2001-03-30', 'MHS210005', 'Akuntansi', 8, 2021, 'aktif', '081344455566', 'Jl. Pahlawan No. 5, Semarang', NOW(), NOW());

-- ============================================
-- DATA SEEDER: BOOKS
-- ============================================
INSERT INTO `books` (`code`, `title`, `author`, `publisher`, `year`, `edition`, `isbn`, `stock`, `created_at`, `updated_at`) VALUES
('B001', 'Laravel untuk Pemula', 'John Doe', 'Informatika Press', 2023, '1', '9786020000001', 5, NOW(), NOW()),
('B002', 'Pemrograman PHP Lanjutan', 'Jane Smith', 'Tekno Media', 2022, '2', '9786020000002', 3, NOW(), NOW()),
('B003', 'Desain Web Modern', 'Alice Jones', 'Creative Book', 2021, '1', '9786020000003', 10, NOW(), NOW()),
('B004', 'Sistem Basis Data', 'Bob Brown', 'Database Publisher', 2020, '3', '9786020000004', 4, NOW(), NOW()),
('B005', 'Clean Code (Terjemahan)', 'Robert C. Martin', 'Praktis Coding', 2019, '1', '9786020000005', 7, NOW(), NOW()),
('B006', 'Algoritma dan Struktur Data', 'Nur Aini', 'EduTech', 2022, '1', '9786020000006', 6, NOW(), NOW()),
('B007', 'Jaringan Komputer', 'Hendri Gunawan', 'NetWork ID', 2021, '2', '9786020000007', 4, NOW(), NOW()),
('B008', 'Kecerdasan Buatan', 'Siti Rahma', 'AI Press', 2023, '1', '9786020000008', 8, NOW(), NOW()),
('B009', 'Manajemen Proyek TI', 'Dedi Prasetyo', 'Project House', 2020, '1', '9786020000009', 5, NOW(), NOW()),
('B010', 'UI/UX Design Handbook', 'Lia Kartika', 'Creative Book', 2022, '1', '9786020000010', 9, NOW(), NOW());

-- ============================================
-- CATATAN PENTING
-- ============================================
-- 1. Password default untuk semua user: 'password'
--    (Hash bcrypt: $2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi)
-- 2. Untuk production, pastikan untuk mengubah password default
-- 3. File ini dapat dijalankan langsung di MySQL/MariaDB
-- 4. Pastikan database sudah dibuat terlebih dahulu atau uncomment bagian CREATE DATABASE
-- 5. Foreign key constraints sudah diset dengan ON DELETE CASCADE
-- ============================================
