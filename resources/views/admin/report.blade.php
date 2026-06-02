<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

    {{-- Navbar --}}
    <nav class="bg-white shadow px-6 py-4 flex justify-between items-center">
        <h1 class="text-xl font-bold text-gray-800">🍜 Seblak App - Laporan</h1>
        <div class="flex gap-4">
            <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:underline">Dashboard</a>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button class="text-red-500 hover:underline">Logout</button>
            </form>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-4 py-8">

        <h2 class="text-2xl font-bold text-gray-700 mb-6">📊 Laporan Penjualan</h2>

        {{-- Filter Tanggal --}}
        <form method="GET" action="{{ route('admin.report') }}"
              class="bg-white rounded-xl shadow p-4 mb-6 flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm text-gray-600 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}"
                       class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}"
                       class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <button type="submit"
                    class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-blue-700 transition">
                Filter
            </button>
            <a href="{{ route('admin.report') }}"
               class="bg-gray-200 text-gray-700 px-5 py-2 rounded-lg text-sm hover:bg-gray-300 transition">
                Reset
            </a>
        </form>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow p-5">
                <p class="text-sm text-gray-500">Total Order Selesai</p>
                <p class="text-3xl font-bold text-blue-600 mt-1">{{ $totalOrder }}</p>
            </div>
            <div class="bg-white rounded-xl shadow p-5">
                <p class="text-sm text-gray-500">Total Pendapatan</p>
                <p class="text-3xl font-bold text-green-600 mt-1">
                    Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                </p>
            </div>
        </div>

        {{-- Tabel Order --}}
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">No. Order</th>
                        <th class="px-4 py-3">Tipe</th>
                        <th class="px-4 py-3">Meja</th>
                        <th class="px-4 py-3">Items</th>
                        <th class="px-4 py-3">Total</th>
                        <th class="px-4 py-3">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($orders as $index => $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-400">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $order->order_number }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs
                                {{ $order->order_type === 'dine_in' ? 'bg-blue-100 text-blue-700' : 'bg-orange-100 text-orange-700' }}">
                                {{ $order->order_type === 'dine_in' ? 'Dine In' : 'Take Away' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">{{ $order->table_number ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-600">
                            @foreach ($order->items as $item)
                                <div>{{ $item->quantity }}x {{ $item->menu->name ?? 'Item' }}</div>
                            @endforeach
                        </td>
                        <td class="px-4 py-3 font-semibold text-green-600">
                            Rp {{ number_format($order->total_price, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-gray-500">
                            {{ $order->created_at->format('d/m/Y H:i') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                            Belum ada data laporan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</body>
</html>