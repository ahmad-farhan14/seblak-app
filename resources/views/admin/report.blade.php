@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 py-8 px-4">
    <div class="max-w-6xl mx-auto">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
            <div class="flex items-center space-x-4">
                <div class="w-14 h-14 bg-indigo-100 text-indigo-600 rounded-2xl flex items-center justify-center text-2xl font-black">
                    📊
                </div>
                <div>
                    <h2 class="text-2xl font-black text-gray-800">Laporan Penjualan</h2>
                    <p class="text-sm text-gray-400 mt-0.5">Rekap pesanan yang telah selesai</p>
                </div>
            </div>
            <div class="mt-4 md:mt-0 flex gap-3">
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold px-5 py-3 rounded-2xl transition-all text-sm active:scale-[0.98]">
                    🖥️ Dashboard
                </a>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold px-5 py-3 rounded-2xl transition-all text-sm active:scale-[0.98]">
                        🚪 Keluar Sistem
                    </button>
                </form>
            </div>
        </div>

        {{-- Filter Tanggal --}}
        <form method="GET" action="{{ route('admin.report') }}"
              class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 mb-6 flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}"
                       class="border border-gray-200 bg-gray-50 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}"
                       class="border border-gray-200 bg-gray-50 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-6 py-3 rounded-2xl text-sm transition-all active:scale-[0.98]">
                🔍 Filter
            </button>
            <a href="{{ route('admin.report') }}"
               class="bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold px-6 py-3 rounded-2xl text-sm transition-all active:scale-[0.98]">
                Reset
            </a>
        </form>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Order Selesai</p>
                <p class="text-3xl font-black text-indigo-600 mt-2">{{ $totalOrder }}</p>
            </div>
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Pendapatan</p>
                <p class="text-3xl font-black text-green-600 mt-2">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
            </div>
        </div>

        {{-- Tabel Order --}}
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            @if($orders->isEmpty())
                <div class="p-12 text-center">
                    <div class="text-4xl mb-3">🗂️</div>
                    <h3 class="text-lg font-bold text-gray-700">Belum Ada Data Laporan</h3>
                    <p class="text-sm text-gray-400 mt-1">Order yang sudah selesai akan muncul di sini.</p>
                </div>
            @else
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50/50">
                            <th class="px-4 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">#</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">No. Order</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Tipe</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Meja</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Items</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 bg-white">
                        @foreach ($orders as $index => $order)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-4 text-gray-400 font-bold text-center">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-xs font-mono bg-gray-100 text-gray-600 px-3 py-1 rounded-full font-bold">
                                    {{ $order->order_number }}
                                </span>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-xs font-bold
                                    {{ $order->order_type === 'dine_in' ? 'bg-blue-100 text-blue-700' : 'bg-orange-100 text-orange-700' }}">
                                    {{ $order->order_type === 'dine_in' ? '🍽️ Dine In' : '🥡 Take Away' }}
                                </span>
                            </td>
                            
                            <td class="px-6 py-4 font-bold text-gray-700 whitespace-nowrap">
                                @if($order->order_type === 'take_away' || $order->table_number === 'Take Away')
                                    <span class="text-gray-400 font-bold uppercase tracking-wider text-xs">Take Away</span>
                                @else
                                    <span class="text-gray-800">
                                        Meja #{{ str_replace(['Meja', '#', ' ', ')'], '', $order->table_number ?: 'Dine In') }}
                                    </span>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 text-gray-600 space-y-3 min-w-50">
                                @foreach ($order->items as $item)
                                    @php
                                        $options = json_decode($item->notes, true) ?? [];
                                        $dbMenuName = $item->menu ? $item->menu->name : 'Menu';
                                        
                                        $flavorInput = isset($options['flavor']) ? strtolower($options['flavor']) : '';
                                        $tempInput = isset($options['temp']) ? $options['temp'] : 'Ice'; // Ambil data suhu, default Ice
                                        $flavorDisplay = '';
                                        $realMenuName = $dbMenuName;

                                        if (!empty($flavorInput)) {
                                            $isBuah = (str_contains($flavorInput, 'peras') || str_contains($flavorInput, 'orange') || str_contains($flavorInput, 'mango') || str_contains($flavorInput, 'mangga') || str_contains($flavorInput, 'nipis'));
                                            $isKopi = (str_contains($flavorInput, 'cappuccino') || str_contains($flavorInput, 'mocacinno') || str_contains($flavorInput, 'moka') || str_contains($flavorInput, 'vanilla'));
                                            $isPopIceFlavor = (str_contains($flavorInput, 'taro') || str_contains($flavorInput, 'avocado') || str_contains($flavorInput, 'permen') || str_contains($flavorInput, 'bubble') || str_contains($flavorInput, 'chocolate') || str_contains($flavorInput, 'coklat'));

                                            if ($isBuah) {
                                                $realMenuName = 'Nutrisari';
                                            } elseif ($isKopi) {
                                                $realMenuName = 'Good Day';
                                            } elseif ($isPopIceFlavor) {
                                                $realMenuName = 'Pop Ice';
                                                $tempInput = 'Ice'; // Proteksi mutlak: Pop Ice di laporan selalu Ice
                                            }

                                            if (str_contains($flavorInput, 'peras')) $flavorDisplay = "Jeruk Peras";
                                            elseif (str_contains($flavorInput, 'american')) $flavorDisplay = "American Sweet Orange";
                                            elseif (str_contains($flavorInput, 'mango') || str_contains($flavorInput, 'mangga')) $flavorDisplay = "Sweet Mango";
                                            elseif (str_contains($flavorInput, 'nipis')) $flavorDisplay = "Jeruk Nipis";
                                            elseif (str_contains($flavorInput, 'mocacinno')) $flavorDisplay = "Mocacinno";
                                            elseif (str_contains($flavorInput, 'cappuccino')) $flavorDisplay = "Cappuccino";
                                            elseif (str_contains($flavorInput, 'chocolate') || str_contains($flavorInput, 'coklat')) $flavorDisplay = "Chocolate";
                                            else $flavorDisplay = ucwords(str_replace(['_', '-'], ' ', $options['flavor']));
                                            
                                            // Tempelkan status suhu di samping rasa di tabel rekap laporan (Contoh: Mocacinno (Hot))
                                            if (!empty($tempInput)) {
                                                $flavorDisplay .= " (" . $tempInput . ")";
                                            }
                                        }

                                        $extras = [];
                                        if (str_contains(strtolower($realMenuName), 'seblak') && isset($options['spicy']) && (int)$options['spicy'] > 0) {
                                            $extras[] = "🌶️ Level " . $options['spicy'];
                                        }
                                        if (!empty($options['toppings']) && is_array($options['toppings'])) {
                                            foreach ($options['toppings'] as $top) {
                                                $extras[] = $top['name'] ?? $top;
                                            }
                                        }

                                        $perMenuNote = $options['notes'] ?? '';
                                    @endphp

                                    <div class="text-sm leading-tight pb-1">
                                        <span class="font-bold text-gray-800">{{ $realMenuName }}</span>
                                        <span class="text-red-500 font-black ml-0.5">x{{ $item->qty }}</span>
                                        
                                        @if(!empty($flavorDisplay))
                                            <span class="block text-[11px] text-gray-400 font-medium mt-0.5">Varian: {{ $flavorDisplay }}</span>
                                        @endif

                                        @if(!empty($extras))
                                            <span class="block text-[11px] text-gray-400 font-medium mt-0.5">Opsi: {{ implode(', ', $extras) }}</span>
                                        @endif

                                        @if(!empty($perMenuNote))
                                            <span class="block text-[11px] text-orange-600 font-bold mt-0.5 bg-orange-50/60 px-1.5 py-0.5 rounded inline-block">
                                                📝 Catatan: "{{ $perMenuNote }}"
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                            </td>
                            
                            <td class="px-6 py-4 font-black text-green-600 whitespace-nowrap text-base">
                                Rp {{ number_format($order->total_price, 0, ',', '.') }}
                            </td>
                            
                            <td class="px-6 py-4 text-gray-400 font-medium whitespace-nowrap text-xs">
                                {{ $order->created_at->format('d/m/Y H:i') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

    </div>
</div>
@endsection