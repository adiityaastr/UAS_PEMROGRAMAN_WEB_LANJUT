<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;

class BookSeeder extends Seeder
{
    public function run()
    {
        $books = [
            [
                'code' => 'B001',
                'title' => 'Laravel untuk Pemula',
                'author' => 'John Doe',
                'publisher' => 'Informatika Press',
                'year' => 2023,
                'edition' => '1',
                'isbn' => '9786020000001',
                'stock' => 5,
            ],
            [
                'code' => 'B002',
                'title' => 'Pemrograman PHP Lanjutan',
                'author' => 'Jane Smith',
                'publisher' => 'Tekno Media',
                'year' => 2022,
                'edition' => '2',
                'isbn' => '9786020000002',
                'stock' => 3,
            ],
            [
                'code' => 'B003',
                'title' => 'Desain Web Modern',
                'author' => 'Alice Jones',
                'publisher' => 'Creative Book',
                'year' => 2021,
                'edition' => '1',
                'isbn' => '9786020000003',
                'stock' => 10,
            ],
            [
                'code' => 'B004',
                'title' => 'Sistem Basis Data',
                'author' => 'Bob Brown',
                'publisher' => 'Database Publisher',
                'year' => 2020,
                'edition' => '3',
                'isbn' => '9786020000004',
                'stock' => 4,
            ],
            [
                'code' => 'B005',
                'title' => 'Clean Code (Terjemahan)',
                'author' => 'Robert C. Martin',
                'publisher' => 'Praktis Coding',
                'year' => 2019,
                'edition' => '1',
                'isbn' => '9786020000005',
                'stock' => 7,
            ],
            [
                'code' => 'B006',
                'title' => 'Algoritma dan Struktur Data',
                'author' => 'Nur Aini',
                'publisher' => 'EduTech',
                'year' => 2022,
                'edition' => '1',
                'isbn' => '9786020000006',
                'stock' => 6,
            ],
            [
                'code' => 'B007',
                'title' => 'Jaringan Komputer',
                'author' => 'Hendri Gunawan',
                'publisher' => 'NetWork ID',
                'year' => 2021,
                'edition' => '2',
                'isbn' => '9786020000007',
                'stock' => 4,
            ],
            [
                'code' => 'B008',
                'title' => 'Kecerdasan Buatan',
                'author' => 'Siti Rahma',
                'publisher' => 'AI Press',
                'year' => 2023,
                'edition' => '1',
                'isbn' => '9786020000008',
                'stock' => 8,
            ],
            [
                'code' => 'B009',
                'title' => 'Manajemen Proyek TI',
                'author' => 'Dedi Prasetyo',
                'publisher' => 'Project House',
                'year' => 2020,
                'edition' => '1',
                'isbn' => '9786020000009',
                'stock' => 5,
            ],
            [
                'code' => 'B010',
                'title' => 'UI/UX Design Handbook',
                'author' => 'Lia Kartika',
                'publisher' => 'Creative Book',
                'year' => 2022,
                'edition' => '1',
                'isbn' => '9786020000010',
                'stock' => 9,
            ],
        ];

        foreach ($books as $book) {
            Book::create($book);
        }
    }
}
