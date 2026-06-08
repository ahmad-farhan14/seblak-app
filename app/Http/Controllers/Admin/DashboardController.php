<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order; // <--- WAJIB dipanggil agar Laravel tahu tabel mana yang mau diupdate

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman utama monitor antrean kasir
     */
    public function index()
    {
        // Ambil semua order yang belum selesai (Pending atau Diproses) untuk dipajang di dashboard
        // Gunakan transform/map untuk memastikan data siap pakai di blade
        $orders = Order::with('items.menu')
            ->whereIn('status', ['Pending', 'Diproses', 'pending', 'processing', 'diproses'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_name' => explode('|', $order->notes)[0] ?? 'Pelanggan', // Ambil nama depan jika notes digabung
                    'status' => $order->status,
                    'notes' => $order->notes,
                    'table_display' => $order->table_number === 'Take Away' ? '🥡 Bawa Pulang' : '🍽️ Meja #' . $order->table_number,
                    'total_price' => $order->total_price,
                    'items' => $order->items->map(function ($item) {
                        return [
                            'name' => $item->menu->name ?? 'Menu',
                            'qty' => $item->qty,
                            'price' => $item->price,
                            'notes' => $item->notes, // JSON string opsi/suhu
                        ];
                    })->toArray()
                ];
            });

        return view('admin.dashboard', compact('orders'));
    }

    /**
     * Memproses perubahan status antrean dari tombol kasir (Bypass Manual)
     */
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        // Normalisasi aksi tombol kasir
        if ($request->action === 'proses') {
            // Ubah status menjadi Diproses (Sesuai dengan string penampung di dashboard baru kita)
            $order->status = 'Diproses'; 
        } elseif ($request->action === 'selesai') {
            // Ubah status menjadi Selesai agar masuk ke dalam rekap Laporan Penjualan (report)
            $order->status = 'Selesai'; 
        }
        
        $order->save();
        
        return redirect()->back()->with('success', 'Status pesanan #' . $order->order_number . ' berhasil diperbarui!');
    }
}