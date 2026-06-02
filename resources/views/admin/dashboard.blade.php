@extends('layouts.app')

@section('content')
<div class="w-full min-h-screen bg-gray-50/50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between bg-white p-6 rounded-3xl border border-gray-100 shadow-xs gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight flex items-center gap-2">
                    👨‍🍳 Monitor Antrean Kasir
                </h1>
                <p class="text-sm text-gray-400 mt-1">Kelola dan proses pesanan pelanggan Seblak App secara real-time</p>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.report') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-black px-5 py-3 rounded-2xl text-sm transition-all shadow-sm shadow-indigo-600/10 cursor-pointer">
                    📊 Lihat Laporan Penjualan
                </a>
                <form action="{{ route('admin.logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-gray-100 hover:bg-red-50 text-gray-500 hover:text-red-600 font-bold px-4 py-3 rounded-2xl text-sm transition-all cursor-pointer">
                        Keluar
                    </button>
                </form>
            </div>
        </div>

        @if(count($orders) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($orders as $order)
                    <div class="bg-white rounded-3xl shadow-xs border border-gray-100 overflow-hidden flex flex-col justify-between p-6 transition-all duration-300 hover:shadow-md hover:border-gray-200">
                        
                        <div class="flex justify-between items-start mb-4 gap-2">
                            <div class="min-w-0">
                                <span class="inline-block text-[10px] font-mono bg-gray-100 text-gray-600 px-2.5 py-1 rounded-md font-black tracking-wider uppercase mb-2">
                                    #{{ $order['order_number'] }}
                                </span>
                                <h3 class="text-lg font-black text-gray-800 truncate leading-snug">
                                    {{ $order['customer_name'] }}
                                </h3>
                                <p class="text-xs font-bold text-orange-600 mt-0.5 flex items-center gap-1">
                                    📍 {{ $order['table_display'] }}
                                </p>
                            </div>
                            
                            <span class="text-[11px] font-black px-3 py-1.5 rounded-xl flex-shrink-0 transition-colors {{ $order['status'] === 'Pending' ? 'bg-amber-50 text-amber-700 border border-amber-100' : 'bg-blue-50 text-blue-700 border border-blue-100' }}">
                                ⏱️ {{ $order['status'] }}
                            </span>
                        </div>

                        <div class="border-t border-dashed border-gray-100 my-2"></div>

                        <div class="space-y-4 my-3 flex-1">
                            @foreach($order['items'] as $item)
                                <div class="flex items-start justify-between gap-3 bg-gray-50/50 p-3 rounded-2xl border border-gray-100/50">
                                    <div class="flex-1 min-w-0 space-y-0.5">
                                        <p class="font-bold text-sm text-gray-800 tracking-tight truncate">
                                            {{ $item['name'] }} 
                                            <span class="text-red-600 ml-1 font-black">x{{ $item['quantity'] }}</span>
                                        </p>
                                        
                                        <p class="text-xs text-gray-400 font-medium leading-relaxed">
                                            @if(!empty($item['custom']['toppings']))
                                                <span class="text-gray-500 font-bold">Opsi:</span> {{ is_array($item['custom']['toppings']) ? implode(', ', $item['custom']['toppings']) : $item['custom']['toppings'] }}
                                            @else
                                                <span class="text-gray-300 italic">Original / Tanpa Opsi</span>
                                            @endif
                                        </p>
                                    </div>
                                    <span class="text-xs font-black text-gray-700 whitespace-nowrap pt-0.5">
                                        Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        <div class="border-t border-gray-100 my-2"></div>

                        <div class="flex justify-between items-center mb-5 pt-1">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Tagihan</span>
                            <span class="text-lg font-black text-red-600 tracking-tight">
                                Rp {{ number_format($order['total_price'], 0, ',', '.') }}
                            </span>
                        </div>

                        <form action="{{ route('admin.order.update', $order['id']) }}" method="POST" class="w-full">
                            @csrf
                            <input type="hidden" name="action" value="{{ $order['status'] === 'Pending' ? 'proses' : 'selesai' }}">
                            
                            <button type="submit" class="w-full text-white font-black py-3.5 px-4 rounded-xl text-xs uppercase tracking-wider transition-all duration-200 shadow-xs cursor-pointer flex items-center justify-center gap-1.5 {{ $order['status'] === 'Pending' ? 'bg-amber-500 hover:bg-amber-600 active:scale-[0.98]' : 'bg-green-600 hover:bg-green-700 active:scale-[0.98]' }}">
                                @if($order['status'] === 'Pending')
                                    <span>👨‍🍳 Mulai Proses Masak</span>
                                @else
                                    <span>✅ Pesanan Selesai Saji</span>
                                @endif
                            </button>
                        </form>

                    </div>
                @endforeach
            </div>
        @else
            <div class="w-full bg-white border border-gray-100 rounded-3xl p-16 text-center shadow-xs flex flex-col items-center justify-center">
                <div class="w-16 h-16 bg-green-50 text-green-600 rounded-2xl flex items-center justify-center text-2xl font-bold mb-4">
                    🎉
                </div>
                <h3 class="text-base font-black text-gray-800">Semua Antrean Bersih!</h3>
                <p class="text-sm text-gray-400 mt-1 max-w-sm">Saat ini tidak ada pesanan pending atau dalam proses memasak di dapur.</p>
            </div>
        @endif

    </div>
</div>
@endsection