<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('items')->orderBy('created_at', 'desc')->get();
        return view('admin.orders.index', compact('orders'));
    }

    // API: return latest pending orders for polling
    public function latest(Request $request)
    {
        $since = $request->query('since');
        $query = Order::with('items')->where('status', 'pending');
        if ($since) {
            $query->where('created_at', '>' , $since);
        }
        $orders = $query->orderBy('created_at', 'desc')->get();
        return response()->json(['orders' => $orders]);
    }

    public function show(Order $order)
    {
        $order->load('items');
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate(['status' => 'required|in:pending,processing,completed,cancelled']);
        $order->status = $request->status;
        $order->save();
        return redirect()->back()->with('success', 'Status diperbarui.');
    }
}

