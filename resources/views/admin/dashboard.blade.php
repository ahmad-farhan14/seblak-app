@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 py-8 px-4">
    <div class="max-w-6xl mx-auto">

        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
            <div class="flex items-center space-x-4">
                <div class="w-14 h-14 bg-red-100 text-red-600 rounded-2xl flex items-center justify-center text-2xl font-black">
                    🖥️
                </div>
                <div>
                    <h2 class="text-2xl font-black text-gray-800">Monitor Antrean Kasir</h2>
                    <p class="text-sm text-gray-400 mt-0.5">Memantau dan memproses pesanan seblak secara real-time</p>
                </div>
            </div>
            <div class="mt-4 md:mt-0">
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold px-5 py-3 rounded-2xl transition-all text-sm active:scale-[0.98]">
                        🚪 Keluar Sistem
                    </button>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 text-sm text-green-700 bg-green-50 rounded-2xl border border-green-100 font-bold">
                ✅ {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($orders as $order)
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden flex flex-col justify-between">

                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <span class="text-xs font-mono bg-gray-100 text-gray-600 px-3 py-1 rounded-full font-bold">
                                    {{ $order['order_number'] }}
                                </span>
                                <h3 class="text-lg font-black text-gray-800 mt-2">
                                    {{ $order['customer_name'] }}
                                </h3>
                            </div>
                            <span class="text-xs font-bold px-3 py-1.5 rounded-xl {{ $order['status'] === 'Diproses' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700' }}">
                                ⏱️ {{ $order['status'] }}
                            </span>
                        </div>

                        <div class="border-t border-dashed border-gray-100 my-4"></div>

                        <div class="space-y-4">
                            @foreach($order['items'] as $item)
                                <div class="flex items-start space-x-3">
                                    <div class="flex-1 space-y-0.5">
                                        <p class="font-bold text-sm text-gray-800">
                                            {{ $item['name'] }}
                                            <span class="text-red-600 ml-1">x{{ $item['quantity'] }}</span>
                                        </p>
                                        <p class="text-xs text-gray-400 font-medium leading-relaxed">
                                            @if(!empty($item['custom']['toppings']))
                                                Opsi: {{ implode(', ', $item['custom']['toppings']) }}
                                            @else
                                                <span class="text-gray-300">Original</span>
                                            @endif
                                        </p>
                                    </div>
                                    <span class="text-sm font-bold text-gray-600">
                                        Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="p-6 bg-gray-50 border-t border-gray-100">
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Tagihan</span>
                            <span class="text-lg font-black text-red-600">
                                Rp {{ number_format($order['total_price'], 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="flex space-x-3">
                            @if($order['status'] === 'Pending')
                                <form action="{{ route('admin.order.update', $order['id']) }}" method="POST" class="w-full">
                                    @csrf
                                    <input type="hidden" name="action" value="proses">
                                    <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-bold py-3 px-4 rounded-xl text-sm transition-all shadow-sm">
                                        👨‍🍳 Proses Masak
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.order.update', $order['id']) }}" method="POST" class="w-full">
                                    @csrf
                                    <input type="hidden" name="action" value="selesai">
                                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-xl text-sm transition-all shadow-sm">
                                        ✅ Selesai Saji
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                </div>
            @empty
                <div class="col-span-full bg-white rounded-3xl p-12 text-center border border-gray-100 shadow-sm">
                    <div class="text-4xl mb-3">🛌</div>
                    <h3 class="text-lg font-bold text-gray-700">Belum Ada Antrean Masuk</h3>
                    <p class="text-sm text-gray-400 mt-1">Pesanan dari aplikasi pelanggan akan muncul otomatis di sini.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            <a href="{{ route('admin.report') }}" class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-5 py-3 rounded-2xl text-sm transition-all">
                📊 Lihat Laporan
            </a>
        </div>

    </div>
</div>
@endsection