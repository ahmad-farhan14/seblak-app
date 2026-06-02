@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <h2 class="text-2xl font-black text-gray-900 mb-6">Ringkasan Pesanan Anda</h2>

    <!-- INFO METODE PEMESANAN (Dine In / Take Away) -->
    <div class="mb-6 bg-orange-50 border border-orange-100 rounded-2xl p-4 flex items-center space-x-3 shadow-xs">
        <div class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center flex-shrink-0 text-orange-600">
            @if(session('order_type') === 'take_away')
                <!-- Icon Kantong Belanja untuk Take Away -->
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            @else
                <!-- Icon Meja untuk Dine In -->
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            @endif
        </div>
        <div>
            <span class="block text-[10px] text-orange-400 font-bold uppercase tracking-wider">Metode Pemesanan</span>
            <h4 class="text-sm font-black text-gray-800">
                {{ session('order_type') === 'take_away' ? 'Bawa Pulang (Take Away)' : 'Makan di Sini (Dine In)' }}
                @if(session('order_type') === 'dine_in' && session()->has('table_number'))
                    <span class="ml-1.5 px-2 py-0.5 bg-orange-200 text-orange-800 rounded-md text-xs font-black">Meja #{{ session('table_number') }}</span>
                @endif
            </h4>
        </div>
    </div>

    <!-- DETAIL ITEM KERANJANG -->
    <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-6 shadow-xs">
        @foreach($cart as $item)
            <div class="flex justify-between items-start border-b border-gray-100 pb-4">
                <div>
                    <h4 class="font-bold text-gray-800 text-base">{{ $item['name'] }}</h4>
                    <span class="text-xs text-gray-400 block mt-0.5">Jumlah: {{ $item['quantity'] }}x</span>

                    @if(isset($item['options']))
                        <div class="mt-2 p-3 bg-gray-50 rounded-xl space-y-1">
                            
                            <!-- KONDISIONAL RASA: Jika menu adalah Pop Ice atau Good Day -->
                            @if(str_contains(strtolower($item['name']), 'pop ice') || str_contains(strtolower($item['name']), 'good day'))
                                <p class="text-xs text-gray-600"><span class="font-bold">Rasa:</span> {{ $item['options']['soup'] }}</p>
                            @else
                                <!-- Jika menu adalah Seblak -->
                                <p class="text-xs text-gray-600"><span class="font-bold">Kuah:</span> {{ strtoupper($item['options']['soup']) }}</p>
                                <p class="text-xs text-gray-600"><span class="font-bold">Pedas:</span> Level {{ $item['options']['spicy'] }}</p>
                            @endif
                            
                            <!-- DETAIL TOPPING (Hanya muncul jika seblak memiliki topping) -->
                            @if(!empty($item['options']['toppings']))
                                <div class="text-xs text-gray-500 pt-1">
                                    <span class="font-bold block text-gray-600">Topping Pilihan:</span>
                                    <ul class="list-disc list-inside pl-1 mt-0.5 space-y-0.5">
                                        @foreach($item['options']['toppings'] as $topping)
                                            <li>{{ $topping['name'] }} (Rp {{ number_format($topping['price'], 0, ',', '.') }})</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
                <span class="font-black text-gray-900 text-sm">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
            </div>
        @endforeach

        <!-- TOTAL AKHIR -->
        <div class="pt-2 flex justify-between items-center text-gray-900">
            <span class="font-bold text-base">Total Pembayaran</span>
            <span class="font-black text-xl text-red-600">
                Rp {{ number_format(collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']), 0, ',', '.') }}
            </span>
        </div>
    </div>

    <!-- TOMBOL SUBMIT -->
    <form action="{{ route('cart.process') }}" method="POST" class="mt-6">
        @csrf
        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-4 rounded-2xl transition shadow-lg text-center tracking-wide block">
            Konfirmasi & Kirim ke Kasir
        </button>
    </form>
</div>
@endsection