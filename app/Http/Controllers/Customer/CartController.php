<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Exception;

class CartController extends Controller
{
    /**
     * 1. MENAMPILKAN HALAMAN CHECKOUT PEMBELI
     */
    public function checkout()
    {
        $cart = Session::get('cart', []);

        if (empty($cart)) {
            return redirect()->route('front.menu')->with('error', 'Keranjang belanja kamu masih kosong!');
        }

        return view('customer.checkout', compact('cart'));
    }

    /**
     * 2. SYNC REKAP KERANJANG DARI JAVASCRIPT KE SESSION LARAVEL
     */
    public function saveCart(Request $request)
    {
        $cart = $request->input('cart', []);
        Session::put('cart', $cart);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Keranjang belanja berhasil disinkronkan ke server!',
            'count' => count($cart)
        ]);
    }

    /**
     * 3. PROSES CHECKOUT INDUK & TRANSAKSI (TUNAI ATAU MIDTRANS QRIS)
     */
    public function processCheckout(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'payment_method' => 'required|in:tunai,qris',
        ]);

        $cart = Session::get('cart', []);

        if (empty($cart)) {
            if ($request->ajax()) {
                return response()->json(['message' => 'Keranjang belanja kamu kosong.'], 400);
            }
            return redirect()->back()->with('error', 'Keranjang belanja kosong.');
        }

        $totalPrice = collect($cart)->reduce(function ($sum, $item) {
            return $sum + ($item['price'] * ($item['quantity'] ?? 1));
        }, 0);

        $orderNumber = 'INV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

        $tableNumber = Session::get('table_number', 'Dine In');
        $orderType = Session::get('order_type') === 'take_away' ? 'Take Away' : 'Dine In';
        $globalNotes = $request->customer_name . " | Tipe: " . $orderType;
        if ($request->notes) {
            $globalNotes .= " | Catatan Kasir: " . $request->notes;
        }

        DB::beginTransaction();

        try {
            $order = new Order();
            $order->order_number = $orderNumber;
            $order->table_number = Session::get('order_type') === 'take_away' ? 'Take Away' : $tableNumber;
            $order->total_price = $totalPrice;
            $order->status = 'Pending'; 
            $order->notes = $globalNotes;
            $order->save();

            foreach ($cart as $id => $item) {
                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                
                $menuId = $item['menu_id'] ?? null;
                if (!$menuId || !\App\Models\Menu::where('id', $menuId)->exists()) {
                    $fallbackMenu = \App\Models\Menu::where('name', 'like', '%' . ($item['name'] ?? '') . '%')->first();
                    $menuId = $fallbackMenu ? $fallbackMenu->id : \App\Models\Menu::first()->id;
                }
                
                $orderItem->menu_id = $menuId;
                $orderItem->qty = $item['quantity'] ?? 1;
                $orderItem->price = $item['price'];

                // --- SAKTI GUARD: RE-FORMAT DATA TOPPING DARI ALPINE.JS KE STRING QUANTITY ---
                $options = $item['options'] ?? [];
                if (!empty($options['toppings']) && is_array($options['toppings'])) {
                    $formattedToppings = [];
                    foreach ($options['toppings'] as $topObj) {
                        $topName = $topObj['name'] ?? null;
                        $topQty = isset($topObj['qty']) ? (int)$topObj['qty'] : 1;
                        
                        if ($topName) {
                            // Satukan objek menjadi format string "4x Kerupuk jengkol secentong"
                            $formattedToppings[] = $topQty . 'x ' . $topName;
                        }
                    }
                    // Ubah array data topping menjadi sebaris string terpisah koma
                    $options['toppings'] = implode(', ', $formattedToppings);
                }

                $orderItem->notes = json_encode($options);
                $orderItem->save();
            }

            if ($request->payment_method === 'qris') {
                \Midtrans\Config::$serverKey = config('midtrans.server_key');
                \Midtrans\Config::$isProduction = config('midtrans.is_production');
                \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
                \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

                $params = [
                    'transaction_details' => [
                        'order_id' => $orderNumber,
                        'gross_amount' => (int) $totalPrice,
                    ],
                    'customer_details' => [
                        'first_name' => $request->customer_name,
                    ],
                    'enabled_payments' => ['gopay', 'qris', 'shopeepay'],
                ];

                $snapToken = \Midtrans\Snap::getSnapToken($params);

                Session::forget('cart'); 
                DB::commit();

                return response()->json([
                    'snap_token' => $snapToken,
                    'order_id' => $order->id,
                    'order_number' => $orderNumber
                ]);
            }

            Session::forget('cart'); 
            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'redirect_url' => route('order.success', $order->id),
                    'message' => 'Pesanan tunai berhasil disimpan!'
                ]);
            }

            return redirect()->route('order.success', $order->id);

        } catch (Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json(['message' => 'Gagal memproses pesanan: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * 4. HALAMAN NOTIFIKASI SUKSES STRUK NOTA
     */
    public function success($id)
    {
        $order = Order::findOrFail($id);
        return view('customer.order-success', compact('order'));
    }
}