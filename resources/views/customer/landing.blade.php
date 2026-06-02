@extends('layouts.app')

@section('content')
<div x-data="{ openModal: false }" class="w-full">

    <section id="hero" class="relative bg-linear-to-br from-gray-900 to-red-950 text-white py-20 px-4 overflow-hidden w-full">
        <div class="absolute inset-0 opacity-20 bg-[radial-gradient(#f59e0b_1px,transparent_1px)] bg-size-[16px_16px]"></div>
        <div class="max-w-4xl mx-auto text-center relative z-10 space-y-6">
            
            @if(session()->has('table_number'))
                <span class="inline-flex items-center bg-amber-500/20 text-amber-300 px-4 py-1.5 rounded-full text-xs font-semibold uppercase tracking-wider border border-amber-500/30">
                    📍 Anda Berada di Meja Nomor {{ session('table_number') }}
                </span>
            @endif

            <h1 class="text-4xl sm:text-6xl font-extrabold tracking-tight leading-none">
                Sensasi Pedas Seblak Autentik yang <span class="text-amber-400 underline decoration-wavy decoration-red-500">Bikin Nagih</span>
            </h1>
            <p class="text-gray-300 text-base sm:text-xl max-w-2xl mx-auto font-light">
                Pilih topping sesukamu, tentukan level pedasmu. Dibuat langsung dari wajan tradisional untuk kepuasan maksimal.
            </p>
            
            <div class="pt-4">
                <button @click="openModal = true" class="inline-block bg-red-600 hover:bg-red-700 text-white font-bold px-8 py-4 rounded-full text-lg shadow-lg shadow-red-600/30 transition transform hover:-translate-y-0.5 cursor-pointer">
                    Pesan Sekarang
                </button>
            </div>
        </div>
    </section>

    <section id="menu-favorit" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 w-full">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-black text-gray-900">🔥 Menu Terfavorit</h2>
            <p class="text-gray-500 mt-2">Paling banyak dipesan oleh pelanggan setia kami</p>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-6">
            @foreach($favorites as $item)
                <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition border border-gray-100 group flex flex-col justify-between">
                    <div>
                        <div class="relative overflow-hidden aspect-4/3 bg-gray-50 flex items-center justify-center p-4">
                            <img src="{{ $item->image }}" class="max-w-full max-h-full object-contain group-hover:scale-105 transition duration-300" alt="{{ $item->name }}">
                        </div>
                        <div class="p-5 space-y-2">
                            <div class="flex items-center justify-between">
                                <h3 class="font-bold text-lg text-gray-800 group-hover:text-red-600 transition">{{ $item->name }}</h3>
                                <span class="text-[10px] bg-amber-50 text-amber-700 font-bold px-2 py-0.5 rounded uppercase tracking-wide">Terlaris</span>
                            </div>
                            <p class="text-gray-400 text-xs line-clamp-2 font-light leading-relaxed">{{ $item->description }}</p>
                        </div>
                    </div>

                    <div class="p-5 pt-0">
                        <button @click="openModal = true" class="w-full bg-gray-50 hover:bg-red-50 text-gray-700 hover:text-red-600 border border-gray-100 hover:border-red-200 font-bold text-xs py-2.5 rounded-xl transition text-center cursor-pointer">
                            Lihat Detail Menu
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <div x-show="openModal" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs" x-transition x-cloak>
        <div @click.away="openModal = false" class="bg-white rounded-3xl p-6 sm:p-8 max-w-sm w-full text-gray-900 shadow-2xl relative">
            <h3 class="text-xl font-bold text-center mb-6">Pilih Tipe Pesanan</h3>
            <form action="{{ route('order.select-type') }}" method="POST" class="space-y-4">
                @csrf
                <label class="flex items-center p-4 border-2 border-gray-100 rounded-2xl cursor-pointer hover:border-red-500 transition group">
                    <input type="radio" name="order_type" value="dine_in" checked class="accent-red-600 h-5 w-5">
                    <div class="ml-4 text-left">
                        <span class="block font-bold text-gray-800">Makan di Sini (Dine In)</span>
                        <small class="text-gray-400">
                            @if(session()->has('table_number')) Terdeteksi Meja {{ session('table_number') }} @endif
                        </small>
                    </div>
                </label>

                <label class="flex items-center p-4 border-2 border-gray-100 rounded-2xl cursor-pointer hover:border-red-500 transition group">
                    <input type="radio" name="order_type" value="take_away" class="accent-red-600 h-5 w-5">
                    <div class="ml-4 text-left">
                        <span class="block font-bold text-gray-800">Bawa Pulang (Take Away)</span>
                    </div>
                </label>

                <button type="submit" class="w-full bg-red-600 text-white py-3 rounded-xl font-bold hover:bg-red-700 transition mt-4 shadow-md cursor-pointer">
                    Lanjutkan ke Menu
                </button>
            </form>
        </div>
    </div>

</div>
@endsection