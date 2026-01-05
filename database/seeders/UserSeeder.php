<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Petugas User',
            'email' => 'petugas@example.com',
            'password' => Hash::make('password'),
            'role' => 'petugas',
        ]);

        // Members
        User::create([
            'name' => 'Member One',
            'email' => 'member1@example.com',
            'password' => Hash::make('password'),
            'role' => 'member',
        ]);

        User::create([
            'name' => 'Member Two',
            'email' => 'member2@example.com',
            'password' => Hash::make('password'),
            'role' => 'member',
        ]);
    }
}
