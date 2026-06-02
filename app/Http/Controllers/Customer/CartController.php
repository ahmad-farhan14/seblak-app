<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function saveCart(Request $request)
    {
        session(['customer_cart' => $request->cart]);
        return response()->json(['status' => 'success']);
    }

    public function checkout()
    {
        $cart = session('customer_cart', []);
        if (empty($cart)) {
            return redirect()->route('landing')->with('error', 'Keranjang belanja Anda masih kosong.');
        }
        return view('customer.checkout', compact('cart'));
    }

    public function processOrder(Request $request)
    {
        $customerName = $request->input('name', 'Pelanggan Anonim');
        $paymentMethod = $request->input('payment_method', 'Tunai');
        $cart = session('customer_cart', []);

        if (empty($cart)) {
            return redirect()->back()->with('error', 'Keranjang belanja kosong.');
        }

        DB::beginTransaction();
        try {
            // 1. Masukkan ke tabel orders
            $order = new Order();
            $order->order_number = 'INV-' . strtoupper(uniqid());
            
            // Mengambil tipe order (dine_in / take_away) secara dinamis
            $orderType = session('order_type', 'dine_in');
            $order->order_type = $orderType;
            
            // PERBAIKAN AKAR: Ambil nomor meja asli dari session. Jika bawa pulang, set jadi 'Take Away'
            if ($orderType === 'take_away') {
                $order->table_number = 'Take Away';
            } else {
                // Jika session meja kosong, beri teks penyelamat 'Dine In (Tanpa Meja)' bukan angka 4 lagi!
                $order->table_number = session('table_number') ?? 'Dine In';
            }
            
            $order->status = 'pending';
            $order->notes = 'Nama Pelanggan: ' . $customerName . ' | Pembayaran: ' . $paymentMethod;

            $totalAmount = 0;
            foreach ($cart as $item) {
                $totalAmount += ($item['price'] * ($item['quantity'] ?? 1));
            }
            $order->total_price = $totalAmount;
            $order->save();

            // 2. Masukkan ke tabel order_items dengan id menu yang sah
            foreach ($cart as $item) {
                $options = $item['options'] ?? [];
                
                OrderItem::create([
                    'order_id'    => $order->id,
                    'menu_id'     => (int)$item['menu_id'], // Mengikat foreign key menu asli
                    'qty'         => $item['quantity'] ?? 1,
                    'price'       => $item['price'],
                    'soup'        => $options['soup'] ?? null,
                    'spicy_level' => isset($options['spicy']) ? (int)$options['spicy'] : 0,
                    'notes'       => json_encode($options) // Menyimpan opsi rasa/topping
                ]);
            }

            DB::commit();

            session(['payment_method' => $paymentMethod]);
            session(['customer_name' => $customerName]);
            session()->forget('customer_cart');

            return redirect()->route('order.success');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal Memproses Pesanan: ' . $e->getMessage());
        }
    }

    public function successPage()
    {
        $paymentMethod = session('payment_method', 'Tunai');
        $customerName = session('customer_name', 'Pelanggan');
        return view('customer.success', compact('paymentMethod', 'customerName'));
    }
}