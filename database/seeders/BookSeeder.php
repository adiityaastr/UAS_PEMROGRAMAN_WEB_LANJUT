<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;

class BookSeeder extends Seeder
{
    public function run()
    {
        Book::create(['code' => 'B001', 'title' => 'Laravel for Beginners', 'author' => 'John Doe', 'stock' => 5]);
        Book::create(['code' => 'B002', 'title' => 'Advanced PHP', 'author' => 'Jane Smith', 'stock' => 3]);
        Book::create(['code' => 'B003', 'title' => 'Web Design 101', 'author' => 'Alice Jones', 'stock' => 10]);
        Book::create(['code' => 'B004', 'title' => 'Database Systems', 'author' => 'Bob Brown', 'stock' => 4]);
        Book::create(['code' => 'B005', 'title' => 'Clean Code', 'author' => 'Robert C. Martin', 'stock' => 7]);
    }
}
