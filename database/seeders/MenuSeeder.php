<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Bersihkan data lama demi menghindari duplikat
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('menus')->truncate();
        DB::table('categories')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Isi data tabel categories
        DB::table('categories')->insert([
            [
                'id' => 1, 
                'name' => 'Makanan', 
                'slug' => 'makanan', 
                'created_at' => now(), 
                'updated_at' => now()
            ],
            [
                'id' => 2, 
                'name' => 'Minuman', 
                'slug' => 'minuman', 
                'created_at' => now(), 
                'updated_at' => now()
            ],
        ]);

        // 3. Isi data menus (Sudah diperbaiki jalurnya mengarah ke folder images/)
        DB::table('menus')->insert([
            [
                'id' => 1,
                'category_id' => 1,
                'name' => 'Seblak Spesial',
                'slug' => 'seblak-spesial',
                'price' => 15000,
                'image' => 'images/seblak.jpg',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 2,
                'category_id' => 1,
                'name' => 'Seblak Kuah Jeletot',
                'slug' => 'seblak-kuah-jeletot',
                'price' => 15000,
                'image' => 'images/seblak2.jpg',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 3,
                'category_id' => 2,
                'name' => 'Good Day Ice',
                'slug' => 'good-day-ice',
                'price' => 5000,
                'image' => 'images/goodday.jpeg',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 4,
                'category_id' => 2,
                'name' => 'Pop Ice Chocolate',
                'slug' => 'pop-ice-chocolate',
                'price' => 5000,
                'image' => 'images/popicecklt.jpeg',
                'created_at' => now(),
                'updated_at' => now()
            ], 
            [
                'id' => 5,
                'category_id' => 2,
                'name' => 'Pop Ice Mangga',
                'slug' => 'pop-ice-mangga',
                'price' => 5000,
                'image' => 'images/popicemgg.jpeg',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 6,
                'category_id' => 2,
                'name' => 'Nutrisari Dingin',
                'slug' => 'nutrisari-dingin',
                'price' => 5000,
                'image' => 'images/nutrisari.jpeg',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}