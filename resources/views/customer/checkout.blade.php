@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <h2 class="text-2xl font-black text-gray-900 mb-6">Ringkasan Pesanan</h2>

    @if(session('error'))
        <div class="mb-6 p-4 text-sm text-red-700 bg-red-50 rounded-2xl border border-red-100 font-bold">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('cart.process') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Rincian Pesanan --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-xs">
            <h3 class="font-black text-gray-800 mb-4">🧾 Rincian Pesanan</h3>
            <div class="space-y-3">
                @php $total = 0; @endphp
                @foreach($cart as $item)
                    @php
                        $subtotal = $item['price'] * ($item['quantity'] ?? 1);
                        $total += $subtotal;
                    @endphp
                    <div class="flex justify-between items-start py-3 border-b border-gray-100 last:border-0">
                        <div class="flex-1">
                            <p class="font-bold text-gray-800">
                                {{ $item['name'] }}
                                <span class="text-red-500 ml-1">x{{ $item['quantity'] ?? 1 }}</span>
                            </p>
                            @if(!empty($item['options']))
                                <p class="text-xs text-gray-400 mt-0.5">
                                    @if(!empty($item['options']['soup']))
                                        Kuah: {{ $item['options']['soup'] }}
                                    @endif
                                    @if(!empty($item['options']['spicy']))
                                        · Pedas: {{ $item['options']['spicy'] }}
                                    @endif
                                </p>
                            @endif
                        </div>
                        <span class="font-bold text-gray-700 ml-4">
                            Rp {{ number_format($subtotal, 0, ',', '.') }}
                        </span>
                    </div>
                @endforeach
            </div>

            <div class="flex justify-between items-center mt-4 pt-4 border-t-2 border-dashed border-gray-200">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Tagihan</span>
                <span class="text-xl font-black text-red-600">Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Nama Pelanggan --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-xs">
            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Nama Pelanggan*</label>
            <input type="text" name="name" placeholder="Masukkan Nama Kamu"
                   class="w-full p-4 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-red-500" required>
        </div>

        {{-- Metode Pembayaran --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-xs">
            <h3 class="font-black text-gray-800 mb-4">Pilih Metode Pembayaran</h3>
            <div class="grid grid-cols-2 gap-4">
                <label class="p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-red-500 block relative">
                    <input type="radio" value="QRIS" name="payment_method" checked class="mr-2 accent-red-600">
                    <span class="font-bold text-gray-800">QRIS (Scan)</span>
                </label>
                <label class="p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-red-500 block relative">
                    <input type="radio" value="Tunai" name="payment_method" class="mr-2 accent-red-600">
                    <span class="font-bold text-gray-800">Tunai (Cash)</span>
                </label>
            </div>
        </div>

        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-4 rounded-2xl shadow-lg shadow-red-600/10 transition-all active:scale-[0.99]">
            Selesaikan Pembayaran
        </button>
    </form>
</div>
@endsection