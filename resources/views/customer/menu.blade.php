@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 pb-32" x-data="menuSystem()">
    
    <div class="sticky top-16 z-40 bg-white/95 backdrop-blur-md border-b border-gray-100 shadow-xs overflow-x-auto whitespace-nowrap scrollbar-none">
        <div class="max-w-7xl mx-auto px-4 py-3.5 flex space-x-3">
            @foreach($categories as $cat)
                <a href="#cat-{{ $cat->id }}" 
                   class="inline-flex items-center justify-center px-5 py-2.5 rounded-full text-xs sm:text-sm font-bold tracking-wide transition-all duration-200 {{ $cat->slug === 'seblak' ? 'bg-red-600 text-white shadow-md shadow-red-600/20' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                   {{ $cat->name }}
                </a>
            @endforeach
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 mt-6">
        <div class="bg-white border border-gray-100 rounded-2xl p-4 flex items-center justify-between shadow-xs">
            <div class="flex items-center space-x-3 min-w-0">
                <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <span class="block text-[10px] text-gray-400 font-bold uppercase tracking-wider">Metode Pemesanan</span>
                    <h4 class="text-sm font-black text-gray-800 truncate">
                        {{ session('order_type') === 'dine_in' ? 'Makan di Sini (Dine In)' : 'Bawa Pulang (Take Away)' }}
                        @if(session()->has('table_number'))
                            <span class="ml-1.5 px-2 py-0.5 bg-orange-100 text-orange-700 rounded-md text-xs font-black">Meja #{{ session('table_number') }}</span>
                        @endif
                    </h4>
                </div>
            </div>
            <a href="{{ route('landing') }}" class="text-xs font-bold text-red-600 hover:text-red-700 bg-red-50 px-3.5 py-2 rounded-xl transition flex-shrink-0">Ubah</a>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 mt-8 space-y-12">
        @foreach($categories as $cat)
            <section id="cat-{{ $cat->id }}" class="scroll-mt-32">
                <div class="flex items-center space-x-2 mb-5">
                    <div class="w-1 h-5 bg-red-600 rounded-full"></div>
                    <h2 class="text-lg font-black text-gray-800 tracking-tight uppercase">{{ $cat->name }}</h2>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($cat->menus as $menu)
                        <div class="bg-white rounded-2xl p-4 border border-gray-100 hover:border-red-100 shadow-xs hover:shadow-lg transition-all duration-300 flex flex-row items-center justify-between gap-4 h-36">
                            
                            <div class="flex-1 min-w-0 flex flex-col justify-between h-full py-1">
                                <div class="space-y-0.5">
                                    <h3 class="font-bold text-sm sm:text-base text-gray-900 truncate tracking-tight">{{ $menu->name }}</h3>
                                    <p class="text-gray-400 text-xs font-light line-clamp-2 leading-tight pr-1">{{ $menu->description }}</p>
                                </div>
                                
                                <div class="flex items-center justify-between pt-2">
                                    <div>
                                        @if($cat->slug === 'seblak')
                                            <span class="text-xs font-medium text-gray-400 block">Harga dasar:</span>
                                            <span class="text-xs font-black text-green-600 uppercase tracking-wider bg-green-50 px-1.5 py-0.5 rounded">Rp 0</span>
                                        @else
                                            <span class="text-sm sm:text-base font-black text-gray-900">Rp {{ number_format($menu->price, 0, ',', '.') }}</span>
                                        @endif
                                    </div>
                                    
                                    <div>
                                        <button @click="openCustomizeModal({{ json_encode($menu) }})" class="bg-red-600 hover:bg-red-700 text-white font-black text-sm w-8 h-8 rounded-xl flex items-center justify-center transition cursor-pointer">
                                            +
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="w-24 h-24 sm:w-28 sm:h-28 flex-shrink-0 rounded-xl overflow-hidden bg-gray-50 border border-gray-100 shadow-inner">
                                <img src="{{ $menu->image }}" class="w-full h-full object-cover">
                            </div>

                        </div>
                    @endforeach

                    <!-- SISIPAN MANUAL MENU MINUMAN BARU TANPA DATABASE -->
                    @if($cat->slug === 'minuman' || $cat->id == 2)
                        <!-- 1. POP ICE -->
                        <div class="bg-white rounded-2xl p-4 border border-gray-100 hover:border-red-100 shadow-xs hover:shadow-lg transition-all duration-300 flex flex-row items-center justify-between gap-4 h-36">
                            <div class="flex-1 min-w-0 flex flex-col justify-between h-full py-1">
                                <div class="space-y-0.5">
                                    <h3 class="font-bold text-sm sm:text-base text-gray-900 truncate tracking-tight">Pop Ice</h3>
                                    <p class="text-gray-400 text-xs font-light line-clamp-2 leading-tight pr-1">Minuman es blender segar dengan berbagai varian rasa favorit.</p>
                                </div>
                                <div class="flex items-center justify-between pt-2">
                                    <div>
                                        <span class="text-xs font-medium text-gray-400 block">Harga dasar:</span>
                                        <span class="text-sm sm:text-base font-black text-gray-900">Rp 5.000</span>
                                    </div>
                                    <div>
                                        <button @click="openCustomizeModal({id: 999, name: 'Pop Ice', price: 5000, description: 'Silakan pilih varian rasa.'})" class="bg-red-600 hover:bg-red-700 text-white font-bold text-sm w-8 h-8 rounded-xl flex items-center justify-center transition cursor-pointer">
                                            +
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="w-24 h-24 sm:w-28 sm:h-28 flex-shrink-0 rounded-xl overflow-hidden bg-gray-50 border border-gray-100 shadow-inner">
                                <img src="/images/pop-ice.jpg" class="w-full h-full object-cover" onerror="this.src='https://placehold.co/150x150?text=Pop+Ice'">
                            </div>
                        </div>

                        <!-- 2. GOOD DAY -->
                        <div class="bg-white rounded-2xl p-4 border border-gray-100 hover:border-red-100 shadow-xs hover:shadow-lg transition-all duration-300 flex flex-row items-center justify-between gap-4 h-36">
                            <div class="flex-1 min-w-0 flex flex-col justify-between h-full py-1">
                                <div class="space-y-0.5">
                                    <h3 class="font-bold text-sm sm:text-base text-gray-900 truncate tracking-tight">Good Day</h3>
                                    <p class="text-gray-400 text-xs font-light line-clamp-2 leading-tight pr-1">Kopi instan segar beraroma khas, nikmat disajikan dingin maupun hangat.</p>
                                </div>
                                <div class="flex items-center justify-between pt-2">
                                    <div>
                                        <span class="text-xs font-medium text-gray-400 block">Harga dasar:</span>
                                        <span class="text-sm sm:text-base font-black text-gray-900">Rp 5.000</span>
                                    </div>
                                    <div>
                                        <button @click="openCustomizeModal({id: 888, name: 'Good Day', price: 5000, description: 'Silakan pilih varian rasa kopi.'})" class="bg-red-600 hover:bg-red-700 text-white font-bold text-sm w-8 h-8 rounded-xl flex items-center justify-center transition cursor-pointer">
                                            +
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="w-24 h-24 sm:w-28 sm:h-28 flex-shrink-0 rounded-xl overflow-hidden bg-gray-50 border border-gray-100 shadow-inner">
                                <img src="/images/good-day.jpg" class="w-full h-full object-cover" onerror="this.src='https://placehold.co/150x150?text=Good+Day'">
                            </div>
                        </div>
                    @endif

                </div>
            </section>
        @endforeach
    </div>

    <!-- Sticky Cart Summary -->
    <div x-show="cartCount > 0" class="fixed bottom-6 left-4 right-4 md:left-auto md:right-6 z-50 w-auto md:w-80" style="display: none;" x-transition>
        <div class="bg-gray-900 text-white rounded-2xl p-4 shadow-xl flex items-center justify-between gap-4 border border-white/10">
            <div class="flex items-center space-x-3 min-w-0">
                <div class="w-10 h-10 bg-red-600 rounded-xl flex items-center justify-center relative flex-shrink-0 shadow-md shadow-red-600/30">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <span class="absolute -top-1 -right-1 bg-white text-gray-900 font-black text-[9px] w-4 h-4 rounded-full flex items-center justify-center border border-gray-900" x-text="cartCount"></span>
                </div>
                <div class="min-w-0">
                    <span class="block text-[10px] text-gray-400 font-medium truncate">Total Sementara</span>
                    <span class="text-sm font-black text-red-400 whitespace-nowrap" x-text="formatPrice(cartTotal)"></span>
                </div>
            </div>
            <button @click="window.location.href='{{ route('cart.checkout') }}'" 
                    class="bg-red-600 hover:bg-red-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition uppercase tracking-wider flex-shrink-0 shadow-sm cursor-pointer">
                Check Out
            </button>
        </div>
    </div>

    <!-- Customization Modal -->
    <div x-show="modalOpen" class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs p-2 sm:p-4 flex items-end sm:items-center justify-center" x-transition style="display: none;">
        <div class="bg-white w-full max-w-xl rounded-t-3xl sm:rounded-2xl h-[85vh] sm:max-h-[80vh] flex flex-col shadow-2xl relative" @click.away="modalOpen = false">
            
            <div class="flex justify-between items-start border-b border-gray-100 p-5 bg-white rounded-t-3xl sm:rounded-t-2xl">
                <div class="min-w-0">
                    <h3 class="text-base sm:text-lg font-bold text-gray-900 truncate" x-text="selectedMenu.name"></h3>
                    <p class="text-gray-400 text-xs mt-0.5 font-light truncate" x-text="selectedMenu.description"></p>
                </div>
                <button @click="modalOpen = false" class="text-gray-400 hover:text-gray-600 text-2xl font-semibold p-1 cursor-pointer leading-none">&times;</button>
            </div>

            <div class="overflow-y-auto p-4 sm:p-6 space-y-5 flex-1 bg-gray-50/50">
                
                <!-- LAYOUT 1: SEBLAK (LENGKAP 16 TOPPING MANDIRI DENGAN GAMBAR) -->
                <template x-if="strtoupper(selectedMenu.name) !== 'POP ICE' && strtoupper(selectedMenu.name) !== 'GOOD DAY'">
                    <div class="space-y-5">
                        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-xs">
                            <span class="block font-bold text-gray-800 text-xs sm:text-sm mb-3">Pilihan Kuah <span class="text-red-500">*</span></span>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="flex items-center justify-center p-3 border rounded-xl cursor-pointer font-bold text-xs sm:text-sm text-center transition" :class="soup === 'pedas gurih' ? 'border-red-500 bg-red-50 text-red-600' : 'border-gray-200 text-gray-600 bg-white'">
                                    <input type="radio" name="soup_type" value="pedas gurih" x-model="soup" class="sr-only"> Pedas Gurih
                                </label>
                                <label class="flex items-center justify-center p-3 border rounded-xl cursor-pointer font-bold text-xs sm:text-sm text-center transition" :class="soup === 'pedas manis' ? 'border-red-500 bg-red-50 text-red-600' : 'border-gray-200 text-gray-600 bg-white'">
                                    <input type="radio" name="soup_type" value="pedas manis" x-model="soup" class="sr-only"> Pedas Manis
                                </label>
                            </div>
                        </div>

                        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-xs">
                            <div class="flex justify-between font-bold text-gray-800 text-xs sm:text-sm mb-3">
                                <span>Level Pedas</span>
                                <span class="text-red-600 bg-red-50 px-2.5 py-0.5 rounded-full text-xs font-black" x-text="'Level ' + spicy"></span>
                            </div>
                            <input type="range" min="0" max="5" x-model="spicy" class="w-full h-1.5 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-red-600 focus:outline-none">
                            <div class="flex justify-between text-[10px] text-gray-400 px-1 mt-1 font-medium">
                                <span>0 (Ori)</span><span>1</span><span>2</span><span>3</span><span>4</span><span>5 (Mampus)</span>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden">
                            <div class="p-4 border-b border-gray-50 bg-white flex justify-between items-center">
                                <div>
                                    <span class="block font-bold text-gray-800 text-xs sm:text-sm">Toppings</span>
                                    <span class="text-[11px] text-gray-400 font-light mt-0.5 block">Wajib pilih minimal 3 topping</span>
                                </div>
                                <span :class="selectedToppings.length >= 3 ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600'" class="text-[10px] sm:text-xs font-bold px-2 py-0.5 rounded-md" x-text="selectedToppings.length >= 3 ? selectedToppings.length + ' Terpilih' : 'Minimal 3 Topping'"></span>
                            </div>
                            
                            <div class="divide-y divide-gray-50 max-h-[260px] overflow-y-auto">
                                <!-- 1. Kerupuk Jaat -->
                                <label class="flex items-center justify-between p-3.5 cursor-pointer hover:bg-gray-50/50 transition select-none">
                                    <div class="flex items-center space-x-2.5 min-w-0">
                                        <div class="w-9 h-9 rounded-xl overflow-hidden bg-gray-100 border border-gray-100 flex-shrink-0 shadow-inner">
                                            <img src="/images/toppings/kerupuk-jaat.jpg" class="w-full h-full object-cover" onerror="this.src='https://placehold.co/50x50?text=KP'">
                                        </div>
                                        <span class="text-xs sm:text-sm font-semibold text-gray-700 truncate">Kerupuk Jaat <span class="text-[10px] text-gray-400 font-normal">(Secentong)</span></span>
                                    </div>
                                    <div class="flex items-center space-x-3 flex-shrink-0">
                                        <span class="text-xs text-gray-400">Rp 1.000</span>
                                        <input type="checkbox" :checked="selectedToppings.some(t => t.id === 1)" @change="toggleTopping(1, 1000, 'Kerupuk Jaat')" class="w-4 h-4 text-red-600 rounded border-gray-300 accent-red-600">
                                    </div>
                                </label>

                                <!-- 2. Kerupuk Mawar (Biasa) -->
                                <label class="flex items-center justify-between p-3.5 cursor-pointer hover:bg-gray-50/50 transition select-none">
                                    <div class="flex items-center space-x-2.5 min-w-0">
                                        <div class="w-9 h-9 rounded-xl overflow-hidden bg-gray-100 border border-gray-100 flex-shrink-0 shadow-inner">
                                            <img src="/images/toppings/kerupuk-mawar.jpg" class="w-full h-full object-cover" onerror="this.src='https://placehold.co/50x50?text=KP'">
                                        </div>
                                        <span class="text-xs sm:text-sm font-semibold text-gray-700 truncate">Kerupuk Mawar <span class="text-[10px] text-gray-400 font-normal">(Secentong)</span></span>
                                    </div>
                                    <div class="flex items-center space-x-3 flex-shrink-0">
                                        <span class="text-xs text-gray-400">Rp 1.000</span>
                                        <input type="checkbox" :checked="selectedToppings.some(t => t.id === 2)" @change="toggleTopping(2, 1000, 'Kerupuk Mawar')" class="w-4 h-4 text-red-600 rounded border-gray-300 accent-red-600">
                                    </div>
                                </label>

                                <!-- 3. Makaroni Spiral -->
                                <label class="flex items-center justify-between p-3.5 cursor-pointer hover:bg-gray-50/50 transition select-none">
                                    <div class="flex items-center space-x-2.5 min-w-0">
                                        <div class="w-9 h-9 rounded-xl overflow-hidden bg-gray-100 border border-gray-100 flex-shrink-0 shadow-inner">
                                            <img src="/images/toppings/makaroni-spiral.jpg" class="w-full h-full object-cover" onerror="this.src='https://placehold.co/50x50?text=MK'">
                                        </div>
                                        <span class="text-xs sm:text-sm font-semibold text-gray-700 truncate">Makaroni Spiral <span class="text-[10px] text-gray-400 font-normal">(Secentong)</span></span>
                                    </div>
                                    <div class="flex items-center space-x-3 flex-shrink-0">
                                        <span class="text-xs text-gray-400">Rp 1.000</span>
                                        <input type="checkbox" :checked="selectedToppings.some(t => t.id === 3)" @change="toggleTopping(3, 1000, 'Makaroni Spiral')" class="w-4 h-4 text-red-600 rounded border-gray-300 accent-red-600">
                                    </div>
                                </label>

                                <!-- 4. Kwetiau -->
                                <label class="flex items-center justify-between p-3.5 cursor-pointer hover:bg-gray-50/50 transition select-none">
                                    <div class="flex items-center space-x-2.5 min-w-0">
                                        <div class="w-9 h-9 rounded-xl overflow-hidden bg-gray-100 border border-gray-100 flex-shrink-0 shadow-inner">
                                            <img src="/images/toppings/kwetiau.jpg" class="w-full h-full object-cover" onerror="this.src='https://placehold.co/50x50?text=KW'">
                                        </div>
                                        <span class="text-xs sm:text-sm font-semibold text-gray-700 truncate">Kwetiau <span class="text-[10px] text-gray-400 font-normal">(Secentong)</span></span>
                                    </div>
                                    <div class="flex items-center space-x-3 flex-shrink-0">
                                        <span class="text-xs text-gray-400">Rp 2.000</span>
                                        <input type="checkbox" :checked="selectedToppings.some(t => t.id === 4)" @change="toggleTopping(4, 2000, 'Kwetiau')" class="w-4 h-4 text-red-600 rounded border-gray-300 accent-red-600">
                                    </div>
                                </label>

                                <!-- 5. Kerupuk Jengkol -->
                                <label class="flex items-center justify-between p-3.5 cursor-pointer hover:bg-gray-50/50 transition select-none">
                                    <div class="flex items-center space-x-2.5 min-w-0">
                                        <div class="w-9 h-9 rounded-xl overflow-hidden bg-gray-100 border border-gray-100 flex-shrink-0 shadow-inner">
                                            <img src="/images/toppings/kerupuk-jengkol.jpg" class="w-full h-full object-cover" onerror="this.src='https://placehold.co/50x50?text=KP'">
                                        </div>
                                        <span class="text-xs sm:text-sm font-semibold text-gray-700 truncate">Kerupuk Jengkol <span class="text-[10px] text-gray-400 font-normal">(Secentong)</span></span>
                                    </div>
                                    <div class="flex items-center space-x-3 flex-shrink-0">
                                        <span class="text-xs text-gray-400">Rp 1.000</span>
                                        <input type="checkbox" :checked="selectedToppings.some(t => t.id === 5)" @change="toggleTopping(5, 1000, 'Kerupuk Jengkol')" class="w-4 h-4 text-red-600 rounded border-gray-300 accent-red-600">
                                    </div>
                                </label>

                                <!-- 6. Makaroni Hitam -->
                                <label class="flex items-center justify-between p-3.5 cursor-pointer hover:bg-gray-50/50 transition select-none">
                                    <div class="flex items-center space-x-2.5 min-w-0">
                                        <div class="w-9 h-9 rounded-xl overflow-hidden bg-gray-100 border border-gray-100 flex-shrink-0 shadow-inner">
                                            <img src="/images/toppings/makaroni-hitam.jpg" class="w-full h-full object-cover" onerror="this.src='https://placehold.co/50x50?text=MK'">
                                        </div>
                                        <span class="text-xs sm:text-sm font-semibold text-gray-700 truncate">Makaroni Hitam <span class="text-[10px] text-gray-400 font-normal">(Secentong)</span></span>
                                    </div>
                                    <div class="flex items-center space-x-3 flex-shrink-0">
                                        <span class="text-xs text-gray-400">Rp 1.000</span>
                                        <input type="checkbox" :checked="selectedToppings.some(t => t.id === 6)" @change="toggleTopping(6, 1000, 'Makaroni Hitam')" class="w-4 h-4 text-red-600 rounded border-gray-300 accent-red-600">
                                    </div>
                                </label>

                                <!-- 7. Makaroni Kuning -->
                                <label class="flex items-center justify-between p-3.5 cursor-pointer hover:bg-gray-50/50 transition select-none">
                                    <div class="flex items-center space-x-2.5 min-w-0">
                                        <div class="w-9 h-9 rounded-xl overflow-hidden bg-gray-100 border border-gray-100 flex-shrink-0 shadow-inner">
                                            <img src="/images/toppings/makaroni-kuning.jpg" class="w-full h-full object-cover" onerror="this.src='https://placehold.co/50x50?text=MK'">
                                        </div>
                                        <span class="text-xs sm:text-sm font-semibold text-gray-700 truncate">Makaroni Kuning <span class="text-[10px] text-gray-400 font-normal">(Secentong)</span></span>
                                    </div>
                                    <div class="flex items-center space-x-3 flex-shrink-0">
                                        <span class="text-xs text-gray-400">Rp 1.000</span>
                                        <input type="checkbox" :checked="selectedToppings.some(t => t.id === 7)" @change="toggleTopping(7, 1000, 'Makaroni Kuning')" class="w-4 h-4 text-red-600 rounded border-gray-300 accent-red-600">
                                    </div>
                                </label>

                                <!-- 8. Mie Kering -->
                                <label class="flex items-center justify-between p-3.5 cursor-pointer hover:bg-gray-50/50 transition select-none">
                                    <div class="flex items-center space-x-2.5 min-w-0">
                                        <div class="w-9 h-9 rounded-xl overflow-hidden bg-gray-100 border border-gray-100 flex-shrink-0 shadow-inner">
                                            <img src="/images/toppings/mie-kering.jpg" class="w-full h-full object-cover" onerror="this.src='https://placehold.co/50x50?text=MIE'">
                                        </div>
                                        <span class="text-xs sm:text-sm font-semibold text-gray-700 truncate">Mie Kering</span>
                                    </div>
                                    <div class="flex items-center space-x-3 flex-shrink-0">
                                        <span class="text-xs text-gray-400">Rp 2.000</span>
                                        <input type="checkbox" :checked="selectedToppings.some(t => t.id === 8)" @change="toggleTopping(8, 2000, 'Mie Kering')" class="w-4 h-4 text-red-600 rounded border-gray-300 accent-red-600">
                                    </div>
                                </label>

                                <!-- 9. Kerupuk Bawang -->
                                <label class="flex items-center justify-between p-3.5 cursor-pointer hover:bg-gray-50/50 transition select-none">
                                    <div class="flex items-center space-x-2.5 min-w-0">
                                        <div class="w-9 h-9 rounded-xl overflow-hidden bg-gray-100 border border-gray-100 flex-shrink-0 shadow-inner">
                                            <img src="/images/toppings/kerupuk-bawang.jpg" class="w-full h-full object-cover" onerror="this.src='https://placehold.co/50x50?text=KP'">
                                        </div>
                                        <span class="text-xs sm:text-sm font-semibold text-gray-700 truncate">Kerupuk Bawang <span class="text-[10px] text-gray-400 font-normal">(Secentong)</span></span>
                                    </div>
                                    <div class="flex items-center space-x-3 flex-shrink-0">
                                        <span class="text-xs text-gray-400">Rp 1.000</span>
                                        <input type="checkbox" :checked="selectedToppings.some(t => t.id === 9)" @change="toggleTopping(9, 1000, 'Kerupuk Bawang')" class="w-4 h-4 text-red-600 rounded border-gray-300 accent-red-600">
                                    </div>
                                </label>

                                <!-- 10. Kerupuk Mie -->
                                <label class="flex items-center justify-between p-3.5 cursor-pointer hover:bg-gray-50/50 transition select-none">
                                    <div class="flex items-center space-x-2.5 min-w-0">
                                        <div class="w-9 h-9 rounded-xl overflow-hidden bg-gray-100 border border-gray-100 flex-shrink-0 shadow-inner">
                                            <img src="/images/toppings/kerupuk-mie.jpg" class="w-full h-full object-cover" onerror="this.src='https://placehold.co/50x50?text=KP'">
                                        </div>
                                        <span class="text-xs sm:text-sm font-semibold text-gray-700 truncate">Kerupuk Mie <span class="text-[10px] text-gray-400 font-normal">(Secentong)</span></span>
                                    </div>
                                    <div class="flex items-center space-x-3 flex-shrink-0">
                                        <span class="text-xs text-gray-400">Rp 1.000</span>
                                        <input type="checkbox" :checked="selectedToppings.some(t => t.id === 10)" @change="toggleTopping(10, 1000, 'Kerupuk Mie')" class="w-4 h-4 text-red-600 rounded border-gray-300 accent-red-600">
                                    </div>
                                </label>

                                <!-- 11. Kerupuk Oren -->
                                <label class="flex items-center justify-between p-3.5 cursor-pointer hover:bg-gray-50/50 transition select-none">
                                    <div class="flex items-center space-x-2.5 min-w-0">
                                        <div class="w-9 h-9 rounded-xl overflow-hidden bg-gray-100 border border-gray-100 flex-shrink-0 shadow-inner">
                                            <img src="/images/toppings/kerupuk-oren.jpg" class="w-full h-full object-cover" onerror="this.src='https://placehold.co/50x50?text=KP'">
                                        </div>
                                        <span class="text-xs sm:text-sm font-semibold text-gray-700 truncate">Kerupuk Oren <span class="text-[10px] text-gray-400 font-normal">(Secentong)</span></span>
                                    </div>
                                    <div class="flex items-center space-x-3 flex-shrink-0">
                                        <span class="text-xs text-gray-400">Rp 1.000</span>
                                        <input type="checkbox" :checked="selectedToppings.some(t => t.id === 11)" @change="toggleTopping(11, 1000, 'Kerupuk Oren')" class="w-4 h-4 text-red-600 rounded border-gray-300 accent-red-600">
                                    </div>
                                </label>

                                <!-- 12. Telur Ayam -->
                                <label class="flex items-center justify-between p-3.5 cursor-pointer hover:bg-gray-50/50 transition select-none">
                                    <div class="flex items-center space-x-2.5 min-w-0">
                                        <div class="w-9 h-9 rounded-xl overflow-hidden bg-gray-100 border border-gray-100 flex-shrink-0 shadow-inner">
                                            <img src="/images/toppings/telur.jpg" class="w-full h-full object-cover" onerror="this.src='https://placehold.co/50x50?text=TLR'">
                                        </div>
                                        <span class="text-xs sm:text-sm font-semibold text-gray-700 truncate">Telur Ayam <span class="text-[10px] text-gray-400 font-normal">(1pcs)</span></span>
                                    </div>
                                    <div class="flex items-center space-x-3 flex-shrink-0">
                                        <span class="text-xs text-gray-400">Rp 3.000</span>
                                        <input type="checkbox" :checked="selectedToppings.some(t => t.id === 12)" @change="toggleTopping(12, 3000, 'Telur Ayam')" class="w-4 h-4 text-red-600 rounded border-gray-300 accent-red-600">
                                    </div>
                                </label>

                                <!-- 13. Siomay Mini -->
                                <label class="flex items-center justify-between p-3.5 cursor-pointer hover:bg-gray-50/50 transition select-none">
                                    <div class="flex items-center space-x-2.5 min-w-0">
                                        <div class="w-9 h-9 rounded-xl overflow-hidden bg-gray-100 border border-gray-100 flex-shrink-0 shadow-inner">
                                            <img src="/images/toppings/siomay-mini.jpg" class="w-full h-full object-cover" onerror="this.src='https://placehold.co/50x50?text=SM'">
                                        </div>
                                        <span class="text-xs sm:text-sm font-semibold text-gray-700 truncate">Siomay Mini <span class="text-[10px] text-gray-400 font-normal">(5pcs)</span></span>
                                    </div>
                                    <div class="flex items-center space-x-3 flex-shrink-0">
                                        <span class="text-xs text-gray-400">Rp 1.000</span>
                                        <input type="checkbox" :checked="selectedToppings.some(t => t.id === 13)" @change="toggleTopping(13, 1000, 'Siomay Mini')" class="w-4 h-4 text-red-600 rounded border-gray-300 accent-red-600">
                                    </div>
                                </label>

                                <!-- 14. Cuanki Lidah -->
                                <label class="flex items-center justify-between p-3.5 cursor-pointer hover:bg-gray-50/50 transition select-none">
                                    <div class="flex items-center space-x-2.5 min-w-0">
                                        <div class="w-9 h-9 rounded-xl overflow-hidden bg-gray-100 border border-gray-100 flex-shrink-0 shadow-inner">
                                            <img src="/images/toppings/cuanki-lidah.jpg" class="w-full h-full object-cover" onerror="this.src='https://placehold.co/50x50?text=CL'">
                                        </div>
                                        <span class="text-xs sm:text-sm font-semibold text-gray-700 truncate">Cuanki Lidah <span class="text-[10px] text-gray-400 font-normal">(3pcs)</span></span>
                                    </div>
                                    <div class="flex items-center space-x-3 flex-shrink-0">
                                        <span class="text-xs text-gray-400">Rp 2.000</span>
                                        <input type="checkbox" :checked="selectedToppings.some(t => t.id === 14)" @change="toggleTopping(14, 2000, 'Cuanki Lidah')" class="w-4 h-4 text-red-600 rounded border-gray-300 accent-red-600">
                                    </div>
                                </label>

                                <!-- 15. Batagor Kering -->
                                <label class="flex items-center justify-between p-3.5 cursor-pointer hover:bg-gray-50/50 transition select-none">
                                    <div class="flex items-center space-x-2.5 min-w-0">
                                        <div class="w-9 h-9 rounded-xl overflow-hidden bg-gray-100 border border-gray-100 flex-shrink-0 shadow-inner">
                                            <img src="/images/toppings/batagor-kering.jpg" class="w-full h-full object-cover" onerror="this.src='https://placehold.co/50x50?text=BK'">
                                        </div>
                                        <span class="text-xs sm:text-sm font-semibold text-gray-700 truncate">Batagor Kering <span class="text-[10px] text-gray-400 font-normal">(5pcs)</span></span>
                                    </div>
                                    <div class="flex items-center space-x-3 flex-shrink-0">
                                        <span class="text-xs text-gray-400">Rp 1.000</span>
                                        <input type="checkbox" :checked="selectedToppings.some(t => t.id === 15)" @change="toggleTopping(15, 1000, 'Batagor Kering')" class="w-4 h-4 text-red-600 rounded border-gray-300 accent-red-600">
                                    </div>
                                </label>

                                <!-- 16. Kerupuk Mawar Putih (SEKARANG BERDIRI SENDIRI) -->
                                <label class="flex items-center justify-between p-3.5 cursor-pointer hover:bg-gray-50/50 transition select-none">
                                    <div class="flex items-center space-x-2.5 min-w-0">
                                        <div class="w-9 h-9 rounded-xl overflow-hidden bg-gray-100 border border-gray-100 flex-shrink-0 shadow-inner">
                                            <img src="/images/toppings/kerupuk-mawar-putih.jpg" class="w-full h-full object-cover" onerror="this.src='https://placehold.co/50x50?text=KWP'">
                                        </div>
                                        <span class="text-xs sm:text-sm font-semibold text-gray-700 truncate">Kerupuk Mawar Putih <span class="text-[10px] text-gray-400 font-normal">(Secentong)</span></span>
                                    </div>
                                    <div class="flex items-center space-x-3 flex-shrink-0">
                                        <span class="text-xs text-gray-400">Rp 1.000</span>
                                        <input type="checkbox" :checked="selectedToppings.some(t => t.id === 16)" @change="toggleTopping(16, 1000, 'Kerupuk Mawar Putih')" class="w-4 h-4 text-red-600 rounded border-gray-300 accent-red-600">
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- LAYOUT 2: PILIHAN RASA POP ICE -->
                <template x-if="strtoupper(selectedMenu.name) === 'POP ICE'">
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden">
                        <div class="p-4 border-b border-gray-50 bg-white flex justify-between items-center">
                            <div>
                                <span class="block font-bold text-gray-800 text-xs sm:text-sm">Pilihan Rasa Pop Ice</span>
                                <span class="text-[11px] text-gray-400 font-light mt-0.5 block">Silakan pilih satu varian rasa gratis</span>
                            </div>
                            <span class="bg-green-50 text-green-700 text-[10px] sm:text-xs font-bold px-2 py-0.5 rounded-md" x-text="soup ? soup + ' Terpilih' : 'Wajib Pilih'"></span>
                        </div>
                        
                        <div class="divide-y divide-gray-50 max-h-[320px] overflow-y-auto">
                            <label class="flex items-center justify-between p-3.5 cursor-pointer hover:bg-gray-50/50 transition select-none">
                                <div class="flex items-center space-x-2.5 min-w-0">
                                    <div class="w-7 h-7 bg-amber-700 text-white font-bold rounded-lg flex items-center justify-center text-[10px] flex-shrink-0">CH</div>
                                    <span class="text-xs sm:text-sm font-semibold text-gray-700 truncate">Chocolate</span>
                                </div>
                                <div class="flex items-center space-x-3 flex-shrink-0">
                                    <span class="text-xs text-green-600 font-medium bg-green-50 px-1.5 py-0.5 rounded">Sudah Termasuk</span>
                                    <input type="radio" name="popice_flavor" value="Chocolate" x-model="soup" class="w-4 h-4 text-red-600 rounded-full border-gray-300 accent-red-600">
                                </div>
                            </label>
                            <label class="flex items-center justify-between p-3.5 cursor-pointer hover:bg-gray-50/50 transition select-none">
                                <div class="flex items-center space-x-2.5 min-w-0">
                                    <div class="w-7 h-7 bg-pink-400 text-white font-bold rounded-lg flex items-center justify-center text-[10px] flex-shrink-0">PK</div>
                                    <span class="text-xs sm:text-sm font-semibold text-gray-700 truncate">Permen Karet</span>
                                </div>
                                <div class="flex items-center space-x-3 flex-shrink-0">
                                    <span class="text-xs text-green-600 font-medium bg-green-50 px-1.5 py-0.5 rounded">Sudah Termasuk</span>
                                    <input type="radio" name="popice_flavor" value="Permen Karet" x-model="soup" class="w-4 h-4 text-red-600 rounded-full border-gray-300 accent-red-600">
                                </div>
                            </label>
                        </div>
                    </div>
                </template>

                <!-- LAYOUT 3: PILIHAN RASA GOOD DAY -->
                <template x-if="strtoupper(selectedMenu.name) === 'GOOD DAY'">
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden">
                        <div class="p-4 border-b border-gray-50 bg-white flex justify-between items-center">
                            <div>
                                <span class="block font-bold text-gray-800 text-xs sm:text-sm">Pilihan Rasa Good Day</span>
                                <span class="text-[11px] text-gray-400 font-light mt-0.5 block">Silakan pilih varian rasa kopi gratis</span>
                            </div>
                            <span class="bg-green-50 text-green-700 text-[10px] sm:text-xs font-bold px-2 py-0.5 rounded-md" x-text="soup ? soup + ' Terpilih' : 'Wajib Pilih'"></span>
                        </div>
                        
                        <div class="divide-y divide-gray-50 max-h-[320px] overflow-y-auto">
                            <label class="flex items-center justify-between p-3.5 cursor-pointer hover:bg-gray-50/50 transition select-none">
                                <div class="flex items-center space-x-2.5 min-w-0">
                                    <div class="w-7 h-7 bg-yellow-100 text-yellow-800 font-bold rounded-lg flex items-center justify-center text-[10px] flex-shrink-0">VL</div>
                                    <span class="text-xs sm:text-sm font-semibold text-gray-700 truncate">Vanilla Latte</span>
                                </div>
                                <div class="flex items-center space-x-3 flex-shrink-0">
                                    <span class="text-xs text-green-600 font-medium bg-green-50 px-1.5 py-0.5 rounded">Sudah Termasuk</span>
                                    <input type="radio" name="goodday_flavor" value="Vanilla Latte" x-model="soup" class="w-4 h-4 text-red-600 rounded-full border-gray-300 accent-red-600">
                                </div>
                            </label>
                            <label class="flex items-center justify-between p-3.5 cursor-pointer hover:bg-gray-50/50 transition select-none">
                                <div class="flex items-center space-x-2.5 min-w-0">
                                    <div class="w-7 h-7 bg-amber-800 text-white font-bold rounded-lg flex items-center justify-center text-[10px] flex-shrink-0">MC</div>
                                    <span class="text-xs sm:text-sm font-semibold text-gray-700 truncate">Mocacinno</span>
                                </div>
                                <div class="flex items-center space-x-3 flex-shrink-0">
                                    <span class="text-xs text-green-600 font-medium bg-green-50 px-1.5 py-0.5 rounded">Sudah Termasuk</span>
                                    <input type="radio" name="goodday_flavor" value="Mocacinno" x-model="soup" class="w-4 h-4 text-red-600 rounded-full border-gray-300 accent-red-600">
                                </div>
                            </label>
                            <label class="flex items-center justify-between p-3.5 cursor-pointer hover:bg-gray-50/50 transition select-none">
                                <div class="flex items-center space-x-2.5 min-w-0">
                                    <div class="w-7 h-7 bg-orange-600 text-white font-bold rounded-lg flex items-center justify-center text-[10px] flex-shrink-0">CN</div>
                                    <span class="text-xs sm:text-sm font-semibold text-gray-700 truncate">Caribbean Nut</span>
                                </div>
                                <div class="flex items-center space-x-3 flex-shrink-0">
                                    <span class="text-xs text-green-600 font-medium bg-green-50 px-1.5 py-0.5 rounded">Sudah Termasuk</span>
                                    <input type="radio" name="goodday_flavor" value="Caribbean Nut" x-model="soup" class="w-4 h-4 text-red-600 rounded-full border-gray-300 accent-red-600">
                                </div>
                            </label>
                            <label class="flex items-center justify-between p-3.5 cursor-pointer hover:bg-gray-50/50 transition select-none">
                                <div class="flex items-center space-x-2.5 min-w-0">
                                    <div class="w-7 h-7 bg-blue-500 text-white font-bold rounded-lg flex items-center justify-center text-[10px] flex-shrink-0">CC</div>
                                    <span class="text-xs sm:text-sm font-semibold text-gray-700 truncate">Coolin' Coffee</span>
                                </div>
                                <div class="flex items-center space-x-3 flex-shrink-0">
                                    <span class="text-xs text-green-600 font-medium bg-green-50 px-1.5 py-0.5 rounded">Sudah Termasuk</span>
                                    <input type="radio" name="goodday_flavor" value="Coolin' Coffee" x-model="soup" class="w-4 h-4 text-red-600 rounded-full border-gray-300 accent-red-600">
                                </div>
                            </label>
                        </div>
                    </div>
                </template>

                <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-xs">
                    <label class="block font-bold text-gray-800 text-xs sm:text-sm mb-2">Catatan Tambahan</label>
                    <textarea rows="2" class="w-full border border-gray-100 rounded-xl p-3 text-xs focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition placeholder:text-gray-300" placeholder="Contoh: Es dipisah..."></textarea>
                </div>
            </div>

            <div class="p-4 border-t border-gray-100 bg-white rounded-b-3xl sm:rounded-b-2xl flex items-center justify-between gap-4">
                <div class="pl-1">
                    <span class="text-[9px] uppercase tracking-wider text-gray-400 font-bold block">Total Harga</span>
                    <span class="text-lg font-black text-red-600" x-text="formatPrice(totalCalculatedPrice)"></span>
                </div>
                <button 
                    :disabled="(strtoupper(selectedMenu.name) !== 'POP ICE' && strtoupper(selectedMenu.name) !== 'GOOD DAY') ? selectedToppings.length < 3 : !soup"
                    @click="addCustomToCart()" 
                    class="bg-red-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-red-700 transition shadow-md text-xs cursor-pointer disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed">
                    Tambah Pesanan
                </button>
            </div>

        </div>
    </div>
</div>

<script>
function menuSystem() {
    return {
        modalOpen: false,
        selectedMenu: {},
        soup: 'pedas gurih',
        spicy: 3,
        selectedToppings: [], 
        totalCalculatedPrice: 0,
        cart: [], 
        
        get cartCount() {
            return this.cart.reduce((sum, item) => sum + item.quantity, 0);
        },
        get cartTotal() {
            return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        },
        
        openCustomizeModal(menu) {
            this.selectedMenu = menu;
            const menuNameCaps = this.strtoupper(menu.name);
            
            if (menuNameCaps === 'POP ICE') {
                this.soup = 'Chocolate'; 
                this.totalCalculatedPrice = parseFloat(menu.price); 
            } else if (menuNameCaps === 'GOOD DAY') {
                this.soup = 'Vanilla Latte'; 
                this.totalCalculatedPrice = parseFloat(menu.price); 
            } else {
                this.soup = 'pedas gurih'; 
                this.totalCalculatedPrice = 0; 
            }
            this.selectedToppings = [];
            this.spicy = 3;
            this.modalOpen = true;
        },
        
        toggleTopping(id, price, name) {
            const index = this.selectedToppings.findIndex(t => t.id === id);
            if (index > -1) {
                this.selectedToppings.splice(index, 1);
                this.totalCalculatedPrice -= parseFloat(price);
            } else {
                this.selectedToppings.push({ id, name, price: parseFloat(price) });
                this.totalCalculatedPrice += parseFloat(price);
            }
        },
        
        addCustomToCart() {
            const menuNameCaps = this.strtoupper(this.selectedMenu.name);
            const isDrinkCustom = (menuNameCaps === 'POP ICE' || menuNameCaps === 'GOOD DAY');
            
            const cartItem = {
                id: 'custom-' + Date.now(),
                menu_id: this.selectedMenu.id,
                name: isDrinkCustom ? this.selectedMenu.name + ' (' + this.soup + ')' : this.selectedMenu.name,
                price: this.totalCalculatedPrice,
                quantity: 1,
                options: {
                    soup: this.soup,
                    spicy: isDrinkCustom ? null : this.spicy,
                    toppings: [...this.selectedToppings]
                }
            };
            
            this.cart.push(cartItem);
            this.saveCartToSession(); 
            this.modalOpen = false;
        },
        
        addToCartDirect(menu) {
            const existing = this.cart.find(item => item.menu_id === menu.id && !item.options);
            if (existing) {
                existing.quantity++;
            } else {
                this.cart.push({
                    id: 'direct-' + menu.id,
                    menu_id: menu.id,
                    name: menu.name,
                    price: parseFloat(menu.price),
                    quantity: 1,
                    options: null
                });
            }
            this.saveCartToSession();
        },
        
        saveCartToSession() {
            fetch("{{ route('cart.save') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ cart: this.cart })
            })
            .then(response => response.json())
            .catch(err => console.error('Gagal sinkronisasi session keranjang:', err));
        },
        
        formatPrice(value) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
        },

        strtoupper(str) {
            return str ? str.toUpperCase() : '';
        }
    }
}
</script>
@endsection