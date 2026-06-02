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

        // 2. Isi data tabel categories (Sudah aman pakai slug)
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

        // 3. PERBAIKAN: Isi data menus TANPA kolom is_available agar bebas dari error
        DB::table('menus')->insert([
            [
                'id' => 1,
                'category_id' => 1,
                'name' => 'Seblak Spesial',
                'slug' => 'seblak-spesial',
                'price' => 15000,
                'image' => 'seblak.jpg',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 2,
                'category_id' => 1,
                'name' => 'Seblak Kuah Jeletot',
                'slug' => 'seblak-kuah-jeletot',
                'price' => 15000,
                'image' => 'seblak2.jpg',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 3,
                'category_id' => 2,
                'name' => 'Es Teh Manis',
                'slug' => 'es-teh-manis',
                'price' => 5000,
                'image' => 'esteh.jpg',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 4,
                'category_id' => 2,
                'name' => 'Vanilla Latte',
                'slug' => 'vanilla-latte',
                'price' => 5000,
                'image' => 'vanilla.jpg',
                'created_at' => now(),
                'updated_at' => now()
            ], // ID 4 andalan kita
            [
                'id' => 5,
                'category_id' => 2,
                'name' => 'Air Mineral',
                'slug' => 'air-mineral',
                'price' => 4000,
                'image' => 'air.jpg',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}