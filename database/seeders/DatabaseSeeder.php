<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Panggil MenuSeeder agar data minuman baru disuntikkan ke database
        $this->call([
            MenuSeeder::class,
            // Jika ada seeder lain (seperti UserSeeder dsb), daftarkan juga di sini
        ]);
    }
}