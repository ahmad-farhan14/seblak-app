@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 py-8 px-4">
    <div class="max-w-6xl mx-auto">

        {{-- HEADER DASHBOARD --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
            <div class="flex items-center space-x-4">
                <div class="w-14 h-14 bg-orange-100 text-orange-600 rounded-2xl flex items-center justify-center text-2xl font-black">
                    🧑‍🍳
                </div>
                <div>
                    <h2 class="text-2xl font-black text-gray-800">Monitor Antrean Kasir</h2>
                    <p class="text-sm text-gray-400 mt-0.5">Kelola dan proses pesanan pelanggan Seblak App secara real-time</p>
                </div>
            </div>
            <div class="mt-4 md:mt-0 flex gap-3">
                <a href="{{ route('admin.report') }}" class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-5 py-3 rounded-2xl transition-all text-sm active:scale-[0.98] shadow-md shadow-indigo-600/10">
                    📊 Lihat Laporan Penjualan
                </a>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center bg-gray-200 hover:bg-gray-300 text-gray-600 font-bold px-5 py-3 rounded-2xl transition-all text-sm active:scale-[0.99] cursor-pointer">
                        Keluar
                    </button>
                </form>
            </div>
        </div>

        {{-- DAFTAR KARTU ANTREAN (GRID LAYOUT) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="antrean-container">
            @forelse ($orders as $order)
                @php
                    // Ambil status dan ubah jadi lowercase untuk keamanan pengecekan logika warna badge
                    $currentStatus = strtolower($order['status'] ?? 'pending');
                    $isStatusDiproses = in_array($currentStatus, ['diproses', 'processing', 'process']);
                    $isStatusPending = in_array($currentStatus, ['pending', 'menunggu']);
                @endphp

                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden flex flex-col justify-between p-6 space-y-4">
                    
                    {{-- Bagian Atas: Invoice & Status Banner --}}
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-mono font-bold bg-gray-100 text-gray-500 px-2.5 py-1 rounded-md">
                            #{{ $order['order_number'] }}
                        </span>
                        <span class="text-xs font-black px-2.5 py-1 rounded-md 
                            {{ $isStatusDiproses ? 'bg-amber-50 text-amber-700 border border-amber-100' : 'bg-red-50 text-red-700 border border-red-100' }}">
                            ⚡ {{ $isStatusDiproses ? 'Diproses' : 'Pending' }}
                        </span>
                    </div>

                    {{-- Data Identitas Pelanggan & Indikator Pembayaran --}}
                    <div>
                        <div class="flex items-start justify-between gap-2">
                            <h3 class="text-xl font-black text-gray-800 capitalize tracking-tight truncate max-w-[65%]">
                                {{ $order['customer_name'] }}
                            </h3>
                            
                            {{-- LOGIKA DETEKSI METODE PEMBAYARAN DARI NOTES INDUK --}}
                            @php
                                $orderNotes = strtolower($order['notes'] ?? '');
                                $isQris = str_contains($orderNotes, 'qris');
                                $isTunai = str_contains($orderNotes, 'tunai') || str_contains($orderNotes, 'cash');
                            @endphp

                            @if($isQris)
                                <span class="shrink-0 text-[10px] font-extrabold bg-green-100 text-green-700 border border-green-200 px-2.5 py-1 rounded-xl shadow-2xs flex items-center gap-1 {{ $isStatusPending ? 'animate-pulse' : '' }}">
                                    📱 QRIS
                                </span>
                            @elseif($isTunai)
                                <span class="shrink-0 text-[10px] font-extrabold bg-blue-100 text-blue-700 border border-blue-200 px-2.5 py-1 rounded-xl shadow-2xs flex items-center gap-1">
                                    💵 TUNAI
                                </span>
                            @else
                                <span class="shrink-0 text-[10px] font-extrabold bg-gray-100 text-gray-600 border border-gray-200 px-2.5 py-1 rounded-xl shadow-2xs">
                                    💵 Cash
                                </span>
                            @endif
                        </div>

                        <p class="text-xs text-red-500 font-bold mt-1.5 flex items-center gap-1">
                            📍 {{ $order['table_display'] }}
                        </p>
                    </div>

                    <div class="border-t border-gray-100 my-1"></div>

                    {{-- DAFTAR ITEM MAKANAN / MINUMAN --}}
                    <div class="divide-y divide-gray-50 flex-1">
                        @foreach($order['items'] as $item)
                            @php
                                $itemNotes = is_string($item['notes']) ? json_decode($item['notes'], true) : ($item['notes'] ?? []);
                                $flavorInput = isset($itemNotes['flavor']) ? strtolower($itemNotes['flavor']) : '';
                                $tempInput = isset($itemNotes['temp']) ? $itemNotes['temp'] : ''; 

                                $allToppingsText = isset($item['custom']['toppings']) ? strtolower(implode(' ', $item['custom']['toppings'])) : '';
                                $currentName = $item['name'] ?? 'Menu';

                                if (!empty($flavorInput) || !empty($allToppingsText)) {
                                    $fullFlavorText = $flavorInput . ' ' . $allToppingsText;

                                    $isNutrisari = (str_contains($fullFlavorText, 'peras') || str_contains($fullFlavorText, 'orange') || str_contains($fullFlavorText, 'mango') || str_contains($fullFlavorText, 'mangga') || str_contains($fullFlavorText, 'nipis'));
                                    $isGoodDay   = (str_contains($fullFlavorText, 'cappuccino') || str_contains($fullFlavorText, 'mocacinno') || str_contains($fullFlavorText, 'moka') || str_contains($fullFlavorText, 'latte') || str_contains($fullFlavorText, 'coolin') || str_contains($fullFlavorText, 'carrebian'));
                                    $isPopIce    = (str_contains($fullFlavorText, 'taro') || str_contains($fullFlavorText, 'avocado') || str_contains($fullFlavorText, 'permen') || str_contains($fullFlavorText, 'bubble') || str_contains($fullFlavorText, 'chocolate') || str_contains($fullFlavorText, 'coklat') || str_contains($fullFlavorText, 'strawberry') || str_contains($fullFlavorText, 'blue'));

                                    if ($isNutrisari) {
                                        $currentName = 'Nutrisari';
                                    } elseif ($isGoodDay) {
                                        $currentName = 'Good Day';
                                    } elseif ($isPopIce) {
                                        $currentName = 'Pop Ice';
                                        $tempInput = 'Ice';
                                    }
                                }

                                $finalToppings = $item['custom']['toppings'] ?? [];
                                if (!empty($tempInput)) {
                                    array_unshift($finalToppings, "Suhu: " . $tempInput); 
                                }

                                $perMenuNote = $itemNotes['notes'] ?? '';
                            @endphp

                            {{-- Render Baris Menu Pesanan --}}
                            <div class="py-3 flex justify-between items-start gap-4">
                                <div class="min-w-0 flex-1">
                                    <h4 class="font-bold text-sm text-gray-800 truncate">
                                        {{ $currentName }} <span class="text-red-600 font-black ml-0.5">x{{ $item['qty'] }}</span>
                                    </h4>
                                    
                                    @if(!empty($finalToppings))
                                        <p class="text-[11px] text-gray-400 font-medium mt-0.5 leading-tight">
                                            Opsi: {{ is_array($finalToppings) ? implode(', ', $finalToppings) : $finalToppings }}
                                        </p>
                                    @endif

                                    @if(!empty($perMenuNote))
                                        <p class="text-[11px] text-orange-600 bg-orange-50 inline-block px-2 py-0.5 rounded-md font-bold mt-1">
                                            📝 Catatan: "{{ $perMenuNote }}"
                                        </p>
                                    @endif
                                </div>
                                <span class="text-xs font-bold text-gray-600 whitespace-nowrap pt-0.5">
                                    Rp {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t border-gray-100 my-1"></div>

                    {{-- Informasi Total Belanja & Aksi Tombol Kasir --}}
                    <div class="space-y-4 pt-2">
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-gray-400 font-bold uppercase tracking-wider">Total Tagihan</span>
                            <span class="font-black text-red-600 text-base">
                                Rp {{ number_format($order['total_price'], 0, ',', '.') }}
                            </span>
                        </div>

                        {{-- TAMPILKAN CATATAN TRANSAKSI REKAP GLOBAL --}}
                        @if(!empty($order['notes']))
                            @php
                                $cleanNotesText = str_replace('Nama Pelanggan: ' . $order['customer_name'] . ' | ', '', $order['notes']);
                            @endphp
                            <p class="text-[10px] text-gray-400 font-medium leading-tight bg-gray-50 p-2 rounded-xl border border-gray-100">
                                ℹ️ {{ $cleanNotesText }}
                            </p>
                        @endif

                        <form action="{{ route('admin.order.update', $order['id']) }}" method="POST" class="w-full">
                            @csrf
                            @if ($isStatusPending)
                                <input type="hidden" name="action" value="proses">
                                <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-black py-3.5 px-4 rounded-2xl text-xs uppercase tracking-wider transition duration-200 active:scale-[0.99] cursor-pointer shadow-md shadow-orange-500/10">
                                    🧑‍🍳 Mulai Proses Masak
                                </button>
                            @else
                                <input type="hidden" name="action" value="selesai">
                                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-black py-3.5 px-4 rounded-2xl text-xs uppercase tracking-wider transition duration-200 active:scale-[0.99] cursor-pointer shadow-md shadow-green-600/10">
                                    ✅ Selesai & Bayar
                                </button>
                            @endif
                        </form>
                    </div>

                </div>
            @empty
                <div class="col-span-full bg-white rounded-3xl p-12 border border-gray-100 text-center shadow-xs">
                    <span class="text-4xl block mb-3">😴</span>
                    <h3 class="text-base font-bold text-gray-700">Belum Ada Antrean Masuk</h3>
                    <p class="text-xs text-gray-400 mt-1">Pesanan pembeli yang baru dibuat akan otomatis muncul di sini.</p>
                </div>
            @endforelse
        </div>

    </div>
</div>

{{-- JAVASCRIPT AUTO REFRESH POLLING ELEMEN CONTAINER --}}
<script>
    setInterval(function() {
        fetch(window.location.href)
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContent = doc.getElementById('antrean-container').innerHTML;
                document.getElementById('antrean-container').innerHTML = newContent;
            })
            .catch(err => console.warn('Gagal memuat otomatis data antrean:', err));
    }, 5000); // Mengecek ke database setiap 5 detik
</script>
@endsection