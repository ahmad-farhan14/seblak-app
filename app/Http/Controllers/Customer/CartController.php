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
        $request->withHtmlSecure = true; // Flag proteksi teks
        $request->validate([
            'customer_name'  => 'required|string|max:255',
            'payment_method' => 'required|string|in:tunai,qris'
        ]);

        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('landing')->with('error', 'Keranjang belanja kamu kosong!');
        }

        $orderType = session('order_type', 'dine_in');
        $tableNumber = session('table_number', null);
        
        // Gabungkan Informasi Nama Pelanggan, Metode Pembayaran, dan Catatan Tambahan ke kolom notes
        $paymentLabel = $request->payment_method === 'qris' ? 'QRIS (Non-Tunai)' : 'Tunai (Cash)';
        $customerNotes = "Nama Pelanggan: " . $request->customer_name . " | Pembayaran: " . $paymentLabel;
        
        if ($request->notes) {
            $customerNotes .= " | Catatan Tambahan: " . $request->notes;
        }

        $totalPrice = collect($cart)->reduce(function ($sum, $item) {
            return $sum + ($item['price'] * $item['quantity']);
        }, 0);

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

            // 2. Simpan item ke tabel order_items
            foreach ($cart as $item) {
                $options = $item['options'] ?? [];
                $menuId = (int)$item['menu_id'];

                if (isset($options['flavor']) && !empty($options['flavor'])) {
                    $flv = strtolower($options['flavor']);
                    
                    $isBuah = (str_contains($flv, 'peras') || str_contains($flv, 'orange') || str_contains($flv, 'mango') || str_contains($flv, 'mangga') || str_contains($flv, 'nipis'));
                    $isKopi = (str_contains($flv, 'cappuccino') || str_contains($flv, 'mocacinno') || str_contains($flv, 'moka') || str_contains($flv, 'vanilla') || str_contains($flv, 'latte') || str_contains($flv, 'coolin') || str_contains($flv, 'nut'));
                    $isPopIceFlavor = (str_contains($flv, 'taro') || str_contains($flv, 'avocado') || str_contains($flv, 'permen') || str_contains($flv, 'bubble') || str_contains($flv, 'chocolate') || str_contains($flv, 'strawberry') || str_contains($flv, 'blue'));

                    if ($isBuah) {
                        $matchedMenu = Menu::where('name', 'like', '%Nutrisari%')->first();
                    } elseif ($isKopi) {
                        $matchedMenu = Menu::where('name', 'like', '%Good Day%')->first();
                    } elseif ($isPopIceFlavor) {
                        $matchedMenu = Menu::where('name', 'like', '%Pop Ice%')->first();
                        $options['temp'] = 'Ice';
                    } else {
                        $matchedMenu = null;
                    }

                    if ($matchedMenu) {
                        $menuId = $matchedMenu->id;
                    }
                }

                if ($menuId === 999 || $menuId === 888) {
                    $fallbackMenu = Menu::where('name', 'not like', '%Seblak%')->first();
                    $menuId = $fallbackMenu ? $fallbackMenu->id : 1; 
                }

                OrderItem::create([
                    'order_id'    => $order->id,
                    'menu_id'     => $menuId, 
                    'qty'         => $item['quantity'] ?? 1,
                    'price'       => $item['price'],
                    'soup'        => $options['soup'] ?? null,
                    'spicy_level' => isset($options['spicy']) ? (int)$options['spicy'] : 0,
                    'notes'       => json_encode($options) 
                ]);
            }

            DB::commit();

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