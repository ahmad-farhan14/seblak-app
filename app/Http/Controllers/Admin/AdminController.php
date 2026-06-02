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
                
                // Ambil nama menu dasar dari database
                $dbMenuName = $dbItem->menu ? $dbItem->menu->name : 'Menu';
                $realMenuName = $dbMenuName;
                
                $toppingsList = [];

                // PROTECTION LOGIC: Jika opsi flavor mengandung varian jeruk/mango/nipis, paksa namanya jadi Nutrisari
                if (isset($options['flavor']) && !empty($options['flavor'])) {
                    $flv = strtolower($options['flavor']);
                    if (str_contains($flv, 'peras') || str_contains($flv, 'orange') || str_contains($flv, 'mango') || str_contains($flv, 'mangga') || str_contains($flv, 'nipis')) {
                        $realMenuName = 'Nutrisari';
                    }
                }

                // A. KUSTOMISASI SEBLAK
                if (str_contains(strtolower($realMenuName), 'seblak')) {
                    $spicyLevel = isset($options['spicy']) ? (int)$options['spicy'] : $dbItem->spicy_level;
                    if ($spicyLevel > 0) {
                        $toppingsList[] = "🌶️ Level " . $spicyLevel;
                    }
                }

                // B. KUSTOMISASI MINUMAN (MAPPING RASA RESMI)
                if (isset($options['flavor']) && !empty($options['flavor'])) {
                    $flavorInput = strtolower($options['flavor']);
                    $cleanFlavor = '';

                    if (str_contains($flavorInput, 'peras') || str_contains($flavorInput, 'jeruk_peras')) {
                        $cleanFlavor = "Jeruk Peras";
                    } elseif (str_contains($flavorInput, 'american') || str_contains($flavorInput, 'sweet_orange')) {
                        $cleanFlavor = "American Sweet Orange";
                    } elseif (str_contains($flavorInput, 'mango') || str_contains($flavorInput, 'mangga')) {
                        $cleanFlavor = "NutriSari Sweet Mango";
                    } elseif (str_contains($flavorInput, 'nipis')) {
                        $cleanFlavor = "NutriSari Jeruk Nipis";
                    } else {
                        $cleanFlavor = ucwords(str_replace(['_', '-'], ' ', $options['flavor']));
                    }

                    if ($cleanFlavor !== 'Original') {
                        $toppingsList[] = "Rasa: " . $cleanFlavor;
                    }
                }

                // C. TOPPING TAMBAHAN
                if (!empty($options['toppings']) && is_array($options['toppings'])) {
                    foreach ($options['toppings'] as $topping) {
                        $toppingsList[] = $topping['name'] ?? $topping;
                    }
                }

                $items[] = [
                    'name' => $realMenuName,
                    'quantity' => $dbItem->qty,
                    'qty' => $dbItem->qty,
                    'price' => $dbItem->price,
                    'notes' => $dbItem->notes,
                    'custom' => [
                        'level' => isset($options['spicy']) ? (int)$options['spicy'] : 0,
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
            
            // Perbaikan penomoran meja dinamis untuk monitor kasir
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

    // === METHOD REPORT (FIXED: SUDAH MENDUKUNG FILTER RENTANG TANGGAL & RESET) ===
    public function report(Request $request)
    {
        if (!session()->has('is_kasir')) { 
            return redirect()->route('admin.login'); 
        }

        // 1. Inisialisasi query dasar untuk order yang berstatus selesai
        $query = Order::with('items.menu')->where('status', 'completed');

        // 2. Tangkap input rentang tanggal dari request form blade
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // 3. Eksekusi filter jika kasir memilih tanggal awal dan akhir
        // Jika kasir menekan tombol RESET, parameter di atas kosong dan otomatis menampilkan seluruh data
        if (!empty($startDate) && !empty($endDate)) {
            $query->whereBetween('created_at', [
                $startDate . ' 00:00:00', 
                $endDate . ' 23:59:59'
            ]);
        }

        // 4. Ambil data akhir diurutkan dari yang terbaru
        $orders = $query->orderBy('created_at', 'desc')->get();
        
        // 5. Hitung variabel akumulasi ringkasan untuk Summary Cards
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