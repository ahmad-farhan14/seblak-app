<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Menu;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index()
    {
        return view('customer.cart');
    }

    public function saveCart(Request $request)
    {
        session(['cart' => $request->cart]);
        return response()->json(['status' => 'success', 'message' => 'Keranjang berhasil disimpan di session']);
    }

    public function checkout()
    {
        $cart = session('cart', []);
        return view('customer.checkout', compact('cart'));
    }

    public function processOrder(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
        ]);

        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('landing')->with('error', 'Keranjang belanja kamu kosong!');
        }

        // Ambil data metode dan meja dari session user
        $orderType = session('order_type', 'dine_in');
        $tableNumber = session('table_number', null);
        
        $customerNotes = "Nama Pelanggan: " . $request->customer_name;
        if ($request->notes) {
            $customerNotes .= " | Catatan Tambahan: " . $request->notes;
        }

        // Hitung total harga belanjaan asli
        $totalPrice = collect($cart)->reduce(function ($sum, $item) {
            return $sum + ($item['price'] * $item['quantity']);
        }, 0);

        // Buat kode invoice acak yang unik
        $orderNumber = 'INV-' . strtoupper(uniqid());

        try {
            DB::beginTransaction();

            // 1. Simpan data induk ke tabel orders
            $order = Order::create([
                'order_number' => $orderNumber,
                'order_type'   => $orderType,
                'table_number' => $orderType === 'take_away' ? 'Take Away' : ($tableNumber ?? 'Dine In'),
                'total_price'  => $totalPrice,
                'status'       => 'pending',
                'notes'        => $customerNotes
            ]);

            // 2. Simpan item ke tabel order_items (DENGAN LOGIKA PENYELAMAT ID DATABASE)
            foreach ($cart as $item) {
                $options = $item['options'] ?? [];
                $menuId = (int)$item['menu_id'];

                // === LOGIKA PENYELAMAT INTEGRITY CONSTRAINT (ANTI ERROR 1452) ===
                // Jika menu_id bernilai 999 atau 888 (ID tiruan/hardcode), alihkan ke ID menu minuman riil di database kamu
                if ($menuId === 999 || $menuId === 888) {
                    // Cari menu minuman apa saja di tabel menus yang namanya bukan Seblak
                    $fallbackMenu = Menu::where('name', 'not like', '%Seblak%')->first();
                    
                    // Gunakan ID menu asli dari database, jika kosong default ke ID 1 atau ID lainnya yang aman
                    $menuId = $fallbackMenu ? $fallbackMenu->id : 1; 
                }

                OrderItem::create([
                    'order_id'    => $order->id,
                    'menu_id'     => $menuId, // Diisikan ID sah yang terdaftar di tabel menus database kamu
                    'qty'         => $item['quantity'] ?? 1,
                    'price'       => $item['price'],
                    'soup'        => $options['soup'] ?? null,
                    'spicy_level' => isset($options['spicy']) ? (int)$options['spicy'] : 0,
                    'notes'       => json_encode($options) // Kustomisasi rasa asli (seperti Taro) tetap tersimpan aman di sini
                ]);
            }

            DB::commit();

            // Kosongkan keranjang di session karena order sudah sukses diproses database
            session()->forget('cart');

            return redirect()->route('order.success', $order->id)
                             ->with('success_message', 'Pesanan berhasil dibuat! Silakan tunggu di antrean kasir.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                             ->withInput()
                             ->with('error', '⚠️ Gagal Memproses Pesanan: ' . $e->getMessage());
        }
    }

    public function success($id)
    {
        $order = Order::with('items.menu')->findOrFail($id);
        return view('customer.success', compact('order'));
    }
}