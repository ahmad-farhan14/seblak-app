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
        $cart = session('customer_cart', []);
        
        if (empty($cart)) {
            return redirect()->back()->with('error', 'Gagal memproses, keranjang kosong.');
        }

        DB::beginTransaction();
        try {
            $order = new Order();
            $order->order_number = 'INV-' . strtoupper(uniqid());
            $order->order_type = session('order_type', 'dine_in');
            $order->table_number = session('table_number');
            $order->status = 'pending'; 
            
            $order->total_price = collect($cart)->reduce(function($sum, $item) {
                return $sum + ($item['price'] * $item['quantity']);
            }, 0);
            
            $order->save();

            foreach ($cart as $item) {
                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->menu_id = $item['menu_id'];
                $orderItem->quantity = $item['quantity'];
                $orderItem->price = $item['price'];
                
                if (isset($item['options'])) {
                    // VALIDASI: Hanya jalankan minimal 3 topping jika item BUKAN Pop Ice
                    if (!str_contains(strtolower($item['name']), 'pop ice')) {
                        if (!isset($item['options']['toppings']) || count($item['options']['toppings']) < 3) {
                            DB::rollback();
                            return redirect()->back()->with('error', 'Gagal memproses. Menu ' . $item['name'] . ' wajib memilih minimal 3 topping.');
                        }
                    }
                    
                    $orderItem->options = json_encode($item['options']);
                }
                
                $orderItem->save();
            }

            DB::commit();
            session()->forget('customer_cart');
            return redirect()->route('order.success', $order->id)->with('success', 'Pesanan berhasil dikirim ke dapur!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}