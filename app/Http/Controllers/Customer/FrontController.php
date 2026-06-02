<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    // 1. Method untuk memunculkan Landing Page
    public function landing(Request $request)
    {
        // Menyimpan nomor meja dari QR Code (?table=X) ke dalam session
        if ($request->has('table')) {
            session(['table_number' => $request->query('table')]);
            session(['order_type' => 'dine_in']); // Otomatis diset Dine In jika scan meja
        }

        // Mock Data Seblak disesuaikan harganya menjadi 0 (Harga dasar sesuai topping)
        $favorites = [
            (object)[
                'id' => 1,
                'name' => 'Seblak',
                'description' => 'Racikan kerupuk dan bumbu kencur khas. Harga ditentukan dari total topping pilihanmu.',
                'price' => 0, 
                'image' => asset('images/seblak.jpg')
            ],
            (object)[
                'id' => 999,
                'name' => 'Pop Ice Chocolate',
                'description' => 'Minuman es blender segar dengan rasa coklat yang manis.',
                'price' => 5000,
                'image' => asset('images/coklat.jpg')
            ],
            (object)[
                'id' => 998,
                'name' => 'Pop Ice Mangga',
                'description' => 'Sensasi es blender rasa buah mangga segar dan manis.',
                'price' => 5000,
                'image' => asset('images/mangga.jpg')
            ]
        ];

        return view('customer.landing', compact('favorites'));
    }

    // 2. Method untuk memproses pilihan Dine In / Take Away secara Dinamis
    public function selectType(Request $request)
    {
        // Simpan jenis order (dine_in atau take_away) ke session
        session(['order_type' => $request->order_type]);
        
        if ($request->order_type === 'take_away') {
            // Jika bawa pulang, buang data nomor meja dari memori
            session()->forget('table_number');
        } else {
            // SINKRONISASI MEJA: Jika pelanggan input manual nomor meja di form, tangkap di sini
            if ($request->has('table_number') && !empty($request->table_number)) {
                session(['table_number' => $request->table_number]);
            }
        }
        
        return redirect()->route('front.menu');
    }

    // 3. Method untuk halaman menu pemesanan utama
    public function menu()
    {
        $categories = [
            (object)[
                'id' => 1,
                'name' => 'Seblak',
                'slug' => 'seblak',
                'menus' => [
                    (object)['id' => 1, 'name' => 'Seblak Original', 'description' => 'Menu standar kerupuk, makaroni, dan telur.', 'price' => 0, 'image' => asset('images/seblak.jpg')],
                ]
            ],
            (object)[
                'id' => 2,
                'name' => 'Minuman',
                'slug' => 'minuman',
                'menus' => [
                    (object)['id' => 4, 'name' => 'Nutrisari', 'description' => 'Minuman varian buah segar instan kaya vitamin C.', 'price' => 5000, 'image' => 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?w=500'],
                    (object)['id' => 999, 'name' => 'Pop Ice', 'description' => 'Silakan pilih varian rasa.', 'price' => 5000, 'image' => asset('images/coklat.jpg')],
                    (object)['id' => 888, 'name' => 'Good Day', 'description' => 'Silakan pilih varian rasa kopi.', 'price' => 5000, 'image' => '/images/good-day.jpg']
                ]
            ]
        ];

        return view('customer.menu', compact('categories'));
    }

    public function saveCart(Request $request)
    {
        session(['customer_cart' => $request->cart]);
        return response()->json(['status' => 'success']);
    }
}