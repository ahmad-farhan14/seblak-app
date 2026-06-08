<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DemoOrderSeeder extends Seeder
{
    public function run()
    {
        // Menyisipkan data item dummy langsung berformat jumlah ke database
        DB::table('order_items')->insert([
            [
                'id' => 99,
                'order_id' => 1, // Sesuaikan dengan id order induk yang ada di tabel orders kamu
                'menu_id' => 2,   // Sesuaikan dengan id menu seblak kamu
                'qty' => 1,
                'price' => 15000,
                'spicy_level' => 3,
                // Nah, di sini kita kunci datanya agar langsung berformat banyak
                'notes' => json_encode([
                    'soup' => 'Kuah Pedas',
                    'spicy' => 3,
                    'flavor' => '',
                    'temp' => '',
                    'toppings' => '11x Kerupuk oren secentong, 4x Kerupuk jengkol secentong, 2x Mie kering',
                    'notes' => 'Kerupuknya agak lembek ya teh'
                ]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);
    }
}