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
        }

        // Mock Data (Data Palsu) Menu Favorit agar web tidak kosong
        $favorites = [
            (object)[
                'id' => 1,
                'name' => 'Seblak',
                'description' => 'Kerupuk, bakso, sosis, dumpling keju, dan ceker ayam dengan kuah jeletot.',
                'price' => 25000,
                'image' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500'
            ]
        ];

        return view('customer.landing', compact('favorites'));
    }

    // 2. Method untuk memproses pilihan Dine In / Take Away
    public function selectType(Request $request)
    {
        session(['order_type' => $request->order_type]);
        
        if ($request->order_type === 'take_away') {
            session()->forget('table_number');
        }
        
        return redirect()->route('front.menu');
    }

    // 3. Method untuk halaman menu pemesanan utama
    public function menu()
    {
        // Mock Data Kategori dan Produk Menu Seblak
        $categories = [
            (object)[
                'id' => 1,
                'name' => 'Seblak',
                'slug' => 'seblak',
                'menus' => [
                    (object)['id' => 1, 'name' => 'Seblak Original', 'description' => 'Menu standar kerupuk, makaroni, dan telur.', 'price' => 15000, 'image' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500'],
                ]
            ],
            (object)[
                'id' => 2,
                'name' => 'Minuman',
                'slug' => 'minuman',
                'menus' => [
                    (object)['id' => 3, 'name' => 'Es Teh Manis', 'description' => 'Segar dan manis pas untuk meredakan pedas.', 'price' => 5000, 'image' => 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=500'],
                    (object)['id' => 4, 'name' => 'Es Jeruk Peras', 'description' => 'Dari jeruk peras asli, kaya vitamin C.', 'price' => 7000, 'image' => 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?w=500']
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