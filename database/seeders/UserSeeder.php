<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Admin
        User::create([
            'name' => 'Admin Perpustakaan',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'aktif',
        ]);

        // Petugas
        $petugas = [
            [
                'name' => 'Petugas Utama',
                'email' => 'petugas@example.com',
            ],
            [
                'name' => 'Petugas Layanan Sirkulasi',
                'email' => 'petugas2@example.com',
            ],
        ];

        foreach ($petugas as $index => $data) {
            User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('password'),
                'role' => 'petugas',
                'status' => 'aktif',
                'kode_unik' => 'PTG' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
            ]);
        }

        // Members (Mahasiswa)
        $members = [
            [
                'name' => 'Andi Pratama',
                'email' => 'andi.pratama@example.com',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '2004-01-15',
                'kode_unik' => 'MHS240001',
                'program_studi' => 'Informatika',
                'semester' => 2,
                'tahun_masuk' => 2024,
                'nomor_telepon' => '081234567890',
                'alamat' => 'Jl. Merdeka No. 1, Bandung',
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@example.com',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '2003-11-02',
                'kode_unik' => 'MHS230002',
                'program_studi' => 'Sistem Informasi',
                'semester' => 4,
                'tahun_masuk' => 2023,
                'nomor_telepon' => '081298765432',
                'alamat' => 'Jl. Sudirman No. 10, Jakarta',
            ],
            [
                'name' => 'Citra Lestari',
                'email' => 'citra.lestari@example.com',
                'tempat_lahir' => 'Yogyakarta',
                'tanggal_lahir' => '2004-05-20',
                'kode_unik' => 'MHS240003',
                'program_studi' => 'Teknik Komputer',
                'semester' => 2,
                'tahun_masuk' => 2024,
                'nomor_telepon' => '081377788899',
                'alamat' => 'Jl. Kaliurang Km 5, Yogyakarta',
            ],
            [
                'name' => 'Dewi Anggraini',
                'email' => 'dewi.anggraini@example.com',
                'tempat_lahir' => 'Surabaya',
                'tanggal_lahir' => '2002-09-08',
                'kode_unik' => 'MHS220004',
                'program_studi' => 'Manajemen',
                'semester' => 6,
                'tahun_masuk' => 2022,
                'nomor_telepon' => '081355566677',
                'alamat' => 'Jl. Pemuda No. 20, Surabaya',
            ],
            [
                'name' => 'Eko Wahyudi',
                'email' => 'eko.wahyudi@example.com',
                'tempat_lahir' => 'Semarang',
                'tanggal_lahir' => '2001-03-30',
                'kode_unik' => 'MHS210005',
                'program_studi' => 'Akuntansi',
                'semester' => 8,
                'tahun_masuk' => 2021,
                'nomor_telepon' => '081344455566',
                'alamat' => 'Jl. Pahlawan No. 5, Semarang',
            ],
        ];

        foreach ($members as $member) {
            User::create([
                'name' => $member['name'],
                'email' => $member['email'],
                'password' => Hash::make('password'),
                'role' => 'member',
                'foto' => null,
                'tempat_lahir' => $member['tempat_lahir'],
                'tanggal_lahir' => $member['tanggal_lahir'],
                'kode_unik' => $member['kode_unik'],
                'program_studi' => $member['program_studi'],
                'semester' => $member['semester'],
                'tahun_masuk' => $member['tahun_masuk'],
                'status' => 'aktif',
                'nomor_telepon' => $member['nomor_telepon'],
                'alamat' => $member['alamat'],
            ]);
        }
    }
}
