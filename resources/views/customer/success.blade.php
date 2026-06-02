@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white p-8 rounded-3xl border border-gray-100 shadow-xs text-center">
        
        <div class="w-16 h-16 bg-green-50 text-green-600 rounded-2xl flex items-center justify-center text-3xl font-bold mx-auto mb-6 shadow-sm">
            🎉
        </div>

        <h2 class="text-2xl font-black text-gray-900 tracking-tight">Pesanan Berhasil Dibuat!</h2>
        <p class="text-sm text-gray-400 mt-2">Silakan tunjukkan nomor nota ini ke kasir untuk pembayaran atau menunggu hidangan Anda.</p>

        <div class="bg-gray-50 rounded-2xl p-4 my-6 border border-gray-100 space-y-3 text-left">
            <div class="flex justify-between items-center text-xs">
                <span class="text-gray-400 font-bold uppercase tracking-wider">No. Invoice</span>
                <span class="font-mono font-black text-gray-700 bg-gray-200 px-2 py-0.5 rounded">
                    {{ $order->order_number }}
                </span>
            </div>
            <div class="flex justify-between items-center text-xs">
                <span class="text-gray-400 font-bold uppercase tracking-wider">Tipe Order</span>
                <span class="font-black text-gray-800">
                    {{ $order->order_type === 'dine_in' ? '🍽️ Makan Di Sini' : '🥡 Bawa Pulang' }}
                </span>
            </div>
            <div class="flex justify-between items-center text-xs">
                <span class="text-gray-400 font-bold uppercase tracking-wider">Lokasi / Meja</span>
                <span class="font-black text-orange-600">
                    {{ $order->table_number }}
                </span>
            </div>
            <div class="border-t border-dashed border-gray-200 my-2"></div>
            <div class="flex justify-between items-center text-sm">
                <span class="text-gray-900 font-black">Total Tagihan</span>
                <span class="font-black text-red-600 text-base">
                    Rp {{ number_format($order->total_price, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <a href="{{ route('landing') }}" class="block w-full bg-red-600 hover:bg-red-700 text-white font-black py-4 px-4 rounded-xl text-xs uppercase tracking-wider transition-all duration-200 active:scale-[0.98] shadow-md shadow-red-600/10 cursor-pointer text-center">
            🏠 Kembali ke Beranda
        </a>

    </div>
</div>
@endsection