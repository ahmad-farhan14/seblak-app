<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);

        $credentials = [
            'email'    => 'kasir@seblakwsp.com', // sesuaikan dengan email di tabel users
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        return back()->with('error', 'Kode akses salah.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }

    public function dashboard()
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        $orders = Order::with('items.menu')
            ->whereIn('status', ['pending', 'processing'])
            ->orderBy('created_at', 'asc')
            ->get();

        $formattedOrders = $orders->map(function ($order) {
            return [
                'id'            => $order->id,
                'order_number'  => $order->order_number,
                'customer_name' => $order->table_number ? 'Meja ' . $order->table_number : 'Take Away',
                'status'        => $order->status === 'pending' ? 'Pending' : 'Diproses',
                'total_price'   => $order->total_price,
                'items'         => $order->items->map(function ($item) {
                    return [
                        'name'     => $item->menu ? $item->menu->name : 'Menu tidak ditemukan',
                        'quantity' => $item->qty,
                        'price'    => $item->price,
                        'custom'   => [
                            'toppings' => array_filter([
                                $item->soup        ? 'Kuah: ' . $item->soup         : null,
                                $item->spicy_level ? 'Pedas: ' . $item->spicy_level : null,
                                $item->notes       ? 'Catatan: ' . $item->notes     : null,
                            ]),
                        ],
                    ];
                })->toArray(),
            ];
        })->toArray();

        return view('admin.dashboard', ['orders' => $formattedOrders]);
    }

    public function updateStatus(Request $request, $id)
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        $order = Order::findOrFail($id);

        if ($request->action === 'proses') {
            $order->status = 'processing';
        } elseif ($request->action === 'selesai') {
            $order->status = 'completed';
        }

        $order->save();

        return redirect()->route('admin.dashboard')->with('success', 'Status order berhasil diperbarui.');
    }

    public function report(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        $query = Order::with('items.menu')->where('status', 'completed');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date   . ' 23:59:59',
            ]);
        }

        $orders          = $query->orderBy('created_at', 'desc')->get();
        $totalPendapatan = $orders->sum('total_price');
        $totalOrder      = $orders->count();

        return view('admin.report', compact('orders', 'totalPendapatan', 'totalOrder'));
    }
}