@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 py-12 px-4 flex items-center justify-center">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl border border-gray-200/50 overflow-hidden">
        
        {{-- HEADER BANNER NOTA (WARNA ORANGE KHAS SEBLAK APP) --}}
        <div class="bg-linear-to-br from-orange-500 to-red-600 p-8 text-center text-white relative">
            <div class="w-16 h-16 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center mx-auto text-3xl mb-3 shadow-sm border border-white/10 animate-bounce">
                🎉
            </div>
            <h2 class="text-2xl font-black tracking-tight">Pesanan Diterima!</h2>
            <p class="text-xs text-orange-100/90 mt-1 font-medium">Silakan lakukan pembayaran atau tunggu makananmu dimasak</p>
            
            {{-- Efek Sobekan Nota Tradisional Kasir --}}
            <div class="absolute bottom-0 left-0 right-0 h-2 bg-white" style="clip-path: polygon(0% 100%, 2.5% 0%, 5% 100%, 7.5% 0%, 10% 100%, 12.5% 0%, 15% 100%, 17.5% 0%, 20% 100%, 22.5% 0%, 25% 100%, 27.5% 0%, 30% 100%, 32.5% 0%, 35% 100%, 37.5% 0%, 40% 100%, 42.5% 0%, 45% 100%, 47.5% 0%, 50% 100%, 52.5% 0%, 55% 100%, 57.5% 0%, 60% 100%, 62.5% 0%, 65% 100%, 67.5% 0%, 70% 100%, 72.5% 0%, 75% 100%, 77.5% 0%, 80% 100%, 82.5% 0%, 85% 100%, 87.5% 0%, 90% 100%, 92.5% 0%, 95% 100%, 97.5% 0%, 100% 100%);"></div>
        </div>

        {{-- KONTEN UTAMA STRUK --}}
        <div class="p-6 space-y-6 bg-white">
            
            {{-- BLOK NAMA PELANGGAN & LOKASI PEMESANAN --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100 text-left">
                    <span class="block text-[9px] text-gray-400 font-bold uppercase tracking-wider mb-1">Nama Pelanggan</span>
                    <span class="text-sm font-black text-gray-800 truncate block">
                        @php
                        $rawName = explode('|', $order->notes)[0] ?? '';
                        $customerName = trim(preg_replace('/^nama pelanggan\s*:\s*/i', '', $rawName)) ?: 'Pelanggan';
                    @endphp
                    {{ $customerName }}
                    </span>
                </div>
                <div class="bg-red-50 p-4 rounded-2xl border border-red-100 text-left">
                    <span class="block text-[9px] text-red-400 font-bold uppercase tracking-wider mb-1">📍 Meja / Lokasi</span>
                    <span class="text-sm font-black text-red-600 truncate block">
                        {{ $order->table_number }}
                    </span>
                </div>
            </div>

            {{-- Metadata Transaksi Nomor Invoice --}}
            <div class="grid grid-cols-2 gap-4 text-xs border-b border-gray-100 pb-4 font-semibold">
                <div>
                    <span class="block text-[10px] text-gray-400 font-bold uppercase tracking-wider">No. Invoice</span>
                    <span class="font-mono font-black text-gray-700 bg-gray-50 px-2 py-1 rounded-md mt-1 inline-block border border-gray-100">
                        #{{ $order->order_number }}
                    </span>
                </div>
                <div class="text-right">
                    <span class="block text-[10px] text-gray-400 font-bold uppercase tracking-wider">Tanggal Order</span>
                    <span class="font-black text-gray-600 bg-gray-50 px-2.5 py-1 rounded-md mt-1 inline-block border border-gray-100 text-[11px]">
                        🕐 {{ $order->created_at->format('d M Y, H:i') }}
                    </span>
                </div>
            </div>

            {{-- Daftar Item Belanjaan --}}
            <div class="space-y-3">
                <span class="block text-[10px] text-gray-400 font-bold uppercase tracking-wider">Rincian Menu Jajanan</span>
                
                <div class="divide-y divide-gray-100 bg-gray-50/50 rounded-2xl p-4 border border-gray-100">
                    @foreach($order->items as $item)
                        @php
                            $options = json_decode($item->notes, true) ?? [];
                        @endphp
                        <div class="py-3 flex justify-between items-start gap-4 {{ $loop->first ? 'pt-0' : '' }} {{ $loop->last ? 'pb-0' : '' }}">
                            <div class="min-w-0 flex-1">
                                <h4 class="font-bold text-sm text-gray-800 tracking-tight">
                                    {{ $item->menu->name ?? 'Menu Seblak' }} 
                                    <span class="text-red-600 font-black ml-1">x{{ $item->qty }}</span>
                                </h4>
                                
                                {{-- Kustomisasi Topping / Rasa --}}
                                @if(!empty($options['toppings']))
                                    <p class="text-[11px] text-gray-400 font-medium mt-0.5 leading-relaxed">
                                        Topping: {{ collect($options['toppings'])->pluck('name')->implode(', ') }}
                                    </p>
                                @endif
                                @if(!empty($options['flavor']))
                                    <p class="text-[11px] text-gray-400 font-medium mt-0.5 leading-relaxed">
                                        Rasa: <span class="text-orange-600 font-bold">{{ $options['flavor'] }}</span> {{ isset($options['temp']) ? '('.$options['temp'].')' : '' }}
                                    </p>
                                @endif
                                @if(!empty($options['notes']))
                                    <p class="text-[10px] text-amber-700 bg-amber-50 inline-block px-2 py-0.5 rounded-md font-bold mt-1">
                                        📝 Catatan: "{{ $options['notes'] }}"
                                    </p>
                                @endif
                            </div>
                            <div class="text-right pt-0.5">
                                @if($item->qty > 1)
                                    <p class="text-[10px] text-gray-400 font-medium whitespace-nowrap">
                                        {{ $item->qty }}x Rp {{ number_format($item->price, 0, ',', '.') }}
                                    </p>
                                @endif
                                <span class="text-sm font-black text-gray-700 whitespace-nowrap">
                                    Rp {{ number_format($item->price * $item->qty, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Garis Pembatas Putus-putus Khas Struk Kasir --}}
            <div class="border-t-2 border-dashed border-gray-200 my-2"></div>

            {{-- Subtotal & Status --}}
            <div class="space-y-2 text-xs">
                <div class="flex justify-between items-center text-gray-500">
                    <span class="font-semibold">Subtotal</span>
                    <span class="font-bold text-gray-700">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center text-gray-500">
                    <span class="font-semibold">Biaya Layanan</span>
                    <span class="font-bold text-green-600">Gratis</span>
                </div>
            </div>

            {{-- Total Pembayaran Final --}}
            <div class="p-4 bg-orange-50/40 rounded-2xl border border-orange-100/50">
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-500 font-bold uppercase tracking-wider">Total Tagihan</span>
                    <span class="text-2xl font-black text-red-600 tracking-tight">
                        Rp {{ number_format($order->total_price, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            {{-- Info Cara Pembayaran --}}
            <div class="bg-blue-50/60 border border-blue-100 rounded-2xl p-4 space-y-1">
                <p class="text-[10px] text-blue-400 font-bold uppercase tracking-wider mb-2">ℹ️ Informasi Pembayaran</p>
                <p class="text-xs text-blue-700 font-semibold">Silakan tunjukkan struk ini ke kasir untuk melakukan pembayaran.</p>
                <p class="text-[11px] text-blue-500 font-medium">Pesananmu akan dimasak setelah pembayaran dikonfirmasi.</p>
            </div>

            {{-- Tombol Navigasi Kembali --}}
            <div class="pt-2 space-y-3">
                <a href="{{ route('front.menu') }}" class="block w-full bg-gray-900 hover:bg-gray-800 text-white font-black py-4 px-4 rounded-2xl text-center text-xs uppercase tracking-wider transition-all duration-200 shadow-lg shadow-gray-900/10 active:scale-[0.98] cursor-pointer">
                    🏠 Kembali ke Menu Utama
                </a>
                <p class="text-[11px] text-center text-gray-400 font-semibold tracking-wide">
                    ❤ Terima kasih sudah jajan di Seblak WSP!
                </p>
            </div>

        </div>
    </div>
</div>
@endsection