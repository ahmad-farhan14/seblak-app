<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class AdminController extends Controller
{
    public function showLogin()
    {
        if (session()->has('is_kasir')) { 
            return redirect()->route('admin.dashboard'); 
        }
        return view('admin.login');
    }

    public function login(Request $request)
    {
        if ($request->password === 'kasirseblak123') {
            session(['is_kasir' => true]);
            return redirect()->route('admin.dashboard');
        }
        return redirect()->back()->with('error', 'Kode Akses Kasir Salah!');
    }

    public function dashboard()
    {
        if (!session()->has('is_kasir')) { 
            return redirect()->route('admin.login'); 
        }

        $ordersData = Order::with('items.menu')
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->orderBy('created_at', 'desc')
            ->get();

        $orders = [];

        foreach ($ordersData as $order) {
            $items = [];
            
            foreach ($order->items as $dbItem) {
                $options = json_decode($dbItem->notes, true) ?? [];
                $dbMenuName = $dbItem->menu ? $dbItem->menu->name : 'Menu';
                
                $toppingsList = [];
                $seblakBasics = []; 
                $cleanFlavor = '';

                // 1. Ambil & Bersihkan Input Varian Rasa Minuman
                $flavorInput = isset($options['flavor']) ? strtolower($options['flavor']) : '';

                if (!empty($flavorInput)) {
                    $isBuah = (str_contains($flavorInput, 'peras') || str_contains($flavorInput, 'orange') || str_contains($flavorInput, 'mango') || str_contains($flavorInput, 'mangga') || str_contains($flavorInput, 'nipis'));
                    $isKopi = (str_contains($flavorInput, 'cappuccino') || str_contains($flavorInput, 'mocacinno') || str_contains($flavorInput, 'moka') || str_contains($flavorInput, 'vanilla'));
                    $isPopIceFlavor = (str_contains($flavorInput, 'taro') || str_contains($flavorInput, 'avocado') || str_contains($flavorInput, 'permen') || str_contains($flavorInput, 'bubble') || str_contains($flavorInput, 'chocolate') || str_contains($flavorInput, 'coklat'));

                    if ($isBuah) {
                        $realMenuName = 'Nutrisari';
                    } elseif ($isKopi) {
                        $realMenuName = 'Good Day';
                    } elseif ($isPopIceFlavor) {
                        $realMenuName = 'Pop Ice';
                    } else {
                        $realMenuName = $dbMenuName;
                    }

                    if (str_contains($flavorInput, 'peras')) $cleanFlavor = "Jeruk Peras";
                    elseif (str_contains($flavorInput, 'american') || str_contains($flavorInput, 'sweet_orange')) $cleanFlavor = "American Sweet Orange";
                    elseif (str_contains($flavorInput, 'mango') || str_contains($flavorInput, 'mangga')) $cleanFlavor = "Sweet Mango";
                    elseif (str_contains($flavorInput, 'nipis')) $cleanFlavor = "Jeruk Nipis";
                    elseif (str_contains($flavorInput, 'mocacinno')) $cleanFlavor = "Mocacinno";
                    elseif (str_contains($flavorInput, 'cappuccino')) $cleanFlavor = "Cappuccino";
                    elseif (str_contains($flavorInput, 'chocolate') || str_contains($flavorInput, 'coklat')) $cleanFlavor = "Chocolate";
                    else $cleanFlavor = ucwords(str_replace(['_', '-'], ' ', $options['flavor']));

                    if ($cleanFlavor !== 'Original') {
                        $seblakBasics[] = "Rasa: " . $cleanFlavor;
                    }
                } else {
                    $realMenuName = $dbMenuName;
                }

                // 2. AMBIL DATA KUAH & PEDAS SEBLAK (Masuk ke array basics)
                if (str_contains(strtolower($realMenuName), 'seblak')) {
                    $soupName = !empty($options['soup']) ? $options['soup'] : (!empty($dbItem->soup) ? $dbItem->soup : '');
                    if (!empty($soupName)) {
                        $seblakBasics[] = "Kuah: " . $soupName;
                    }

                    $spicyLevel = isset($options['spicy']) && $options['spicy'] !== '' ? (int)$options['spicy'] : (int)$dbItem->spicy_level;
                    if ($spicyLevel > 0) {
                        $seblakBasics[] = "Level " . $spicyLevel;
                    }
                }

                // 3. AMBIL DATA TOPPING TAMBAHAN
                if (!empty($options['toppings'])) {
                    if (is_array($options['toppings'])) {
                        foreach ($options['toppings'] as $topping) {
                            $toppingsList[] = $topping['name'] ?? $topping;
                        }
                    } elseif (is_string($options['toppings'])) {
                        $explodedToppings = explode(',', $options['toppings']);
                        foreach ($explodedToppings as $topStr) {
                            $toppingsList[] = trim($topStr);
                        }
                    }
                }

                $items[] = [
                    'name' => $realMenuName,
                    'quantity' => $dbItem->qty,
                    'qty' => $dbItem->qty,
                    'price' => $dbItem->price,
                    'notes' => $dbItem->notes,
                    'custom' => [
                        'basics' => $seblakBasics, 
                        'toppings' => $toppingsList 
                    ]
                ];
            }

            $customerName = 'Pelanggan';
            if (preg_match('/Nama Pelanggan:\s*([^|]+)/', $order->notes, $matches)) {
                $customerName = trim($matches[1]);
            }

            $orderArray = $order->toArray();
            $orderArray['customer_name'] = $customerName;
            $orderArray['items'] = $items;
            
            if ($order->order_type === 'take_away') {
                $orderArray['table_display'] = 'Take Away';
            } else {
                $orderArray['table_display'] = ($order->table_number && $order->table_number !== 'Dine In') ? 'Meja #' . $order->table_number : 'Meja Dine In';
            }

            $orderArray['status'] = ($order->status === 'processing') ? 'Diproses' : 'Pending';
            $orders[] = $orderArray;
        }

        return view('admin.dashboard', compact('orders'));
    }

    public function updateStatus($id, Request $request)
    {
        $order = Order::findOrFail($id);
        if ($request->action === 'proses') {
            $order->status = 'processing';
            $order->save();
        } elseif ($request->action === 'selesai') {
            $order->status = 'completed';
            $order->save();
        }
        return redirect()->route('admin.dashboard');
    }

    public function report(Request $request)
    {
        if (!session()->has('is_kasir')) { 
            return redirect()->route('admin.login'); 
        }

        $query = Order::with('items.menu')->where('status', 'completed');

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if (!empty($startDate) && !empty($endDate)) {
            $query->whereBetween('created_at', [
                $startDate . ' 00:00:00', 
                $endDate . ' 23:59:59'
            ]);
        }

        $orders = $query->orderBy('created_at', 'desc')->get();
        
        $totalOrder = $orders->count(); 
        $totalPendapatan = $orders->sum('total_price');

        return view('admin.report', compact('orders', 'totalOrder', 'totalPendapatan'));
    }

    public function logout()
    {
        session()->forget('is_kasir');
        return redirect()->route('admin.login');
    }
}