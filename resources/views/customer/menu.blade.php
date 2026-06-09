@extends('layouts.app')

@section('content')
<div class="w-full pb-32 bg-gray-50 relative" x-data="menuSystem()">

    <div class="w-full bg-gray-50 pt-6">
        
        <div class="max-w-7xl mx-auto px-4">
            <div class="bg-white border border-gray-100 rounded-2xl p-4 flex items-center justify-between shadow-xs">
                <div class="flex items-center space-x-3 min-w-0">
                    <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="block text-[10px] text-gray-400 font-bold uppercase tracking-wider">Metode Pemesanan</span>
                        <h4 class="text-sm font-black text-gray-800 truncate">
                            {{ session('order_type') === 'dine_in' ? 'Makan di Sini (Dine In)' : 'Bawa Pulang (Take Away)' }}
                            @if(session('order_type') === 'dine_in' && session()->has('table_number'))
                                <span class="ml-1.5 px-2 py-0.5 bg-orange-100 text-orange-700 rounded-md text-xs font-black">
                                    Meja #{{ session('table_number') }}
                                </span>
                            @endif
                        </h4>
                    </div>
                </div>
                <a href="{{ route('landing') }}" class="text-xs font-bold text-red-600 hover:text-red-700 bg-red-50 px-3.5 py-2 rounded-xl transition shrink-0">Ubah</a>
            </div>
        </div>
        
        <div class="sticky top-0 z-30 bg-gray-50/95 backdrop-blur-md border-b border-gray-200/60 overflow-x-auto whitespace-nowrap scrollbar-none py-4 px-4 mt-4 shadow-2xs">
            <div class="max-w-7xl mx-auto flex space-x-3">
                @foreach($categories as $cat)
                    <a href="#cat-{{ $cat->id }}" 
                       class="inline-flex items-center justify-center px-5 py-2.5 rounded-full text-xs sm:text-sm font-bold tracking-wide transition-all duration-200 {{ $cat->slug === 'seblak' ? 'bg-red-600 text-white shadow-md shadow-red-600/20' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200/80' }}">
                       {{ $cat->name }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 mt-6 space-y-12">
        @foreach($categories as $cat)
            <section id="cat-{{ $cat->id }}" class="scroll-mt-24">
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
                                        @if(Illuminate\Support\Str::contains(Illuminate\Support\Str::lower($menu->name), 'seblak') || $menu->price == 0)
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
                            <div class="w-24 h-24 sm:w-28 sm:h-28 shrink-0 rounded-xl overflow-hidden bg-gray-50 border border-gray-100 shadow-inner">
                                {{-- FIXED: Sudah menggunakan asset() agar gambar lokal terbaca sempurna --}}
                                <img src="{{ asset($menu->image) }}" class="w-full h-full object-cover">
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endforeach
    </div>

    {{-- BAR NOTIFIKASI KERANJANG SEMENTARA --}}
    <div x-show="cartCount > 0" class="fixed bottom-6 left-4 right-4 md:left-auto md:right-6 z-50 w-auto md:w-80" style="display: none;" x-transition>
        <div class="bg-gray-900 text-white rounded-2xl p-4 shadow-xl flex items-center justify-between gap-4 border border-white/10">
            <div class="flex items-center space-x-3 min-w-0">
                <div class="w-10 h-10 bg-red-600 rounded-xl flex items-center justify-center relative shrink-0 shadow-md shadow-red-600/30">
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
            <button @click="window.location.href='{{ route('cart.checkout') }}'" class="bg-red-600 hover:bg-red-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition uppercase tracking-wider shrink-0 shadow-sm cursor-pointer">Check Out</button>
        </div>
    </div>

    {{-- MODAL POPUP KUSTOMISASI --}}
    <div x-show="modalOpen" class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs p-2 sm:p-4 flex items-end sm:items-center justify-center" x-transition x-cloak style="display: none;">
        <div class="bg-white w-full max-w-xl rounded-t-3xl sm:rounded-2xl h-[85vh] sm:max-h-[80vh] flex flex-col shadow-2xl relative" @click.away="modalOpen = false">
            
            <div class="flex justify-between items-start border-b border-gray-100 p-5 bg-white rounded-t-3xl sm:rounded-t-2xl">
                <div class="min-w-0">
                    <h3 class="text-base sm:text-lg font-bold text-gray-900 truncate" x-text="selectedMenu.name"></h3>
                    <p class="text-gray-400 text-xs mt-0.5 font-light truncate" x-text="selectedMenu.description"></p>
                </div>
                <button @click="modalOpen = false" class="text-gray-400 hover:text-gray-600 text-2xl font-semibold p-1 cursor-pointer leading-none">&times;</button>
            </div>

            <div class="overflow-y-auto p-4 sm:p-6 space-y-5 flex-1 bg-gray-50/50">
                
                {{-- BLOK A: OPSI KHUSUS SEBLAK --}}
                <div x-show="selectedMenu.name && strtoupper(selectedMenu.name).includes('SEBLAK')">
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
                                <span>0</span><span>1</span><span>2</span><span>3</span><span>4</span><span>5</span>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden">
                            <div class="p-4 border-b border-gray-50 bg-white flex justify-between items-center">
                                <div>
                                    <span class="block font-bold text-gray-800 text-xs sm:text-sm">Pilihan Toppings Tambahan</span>
                                    <span class="text-[11px] text-gray-400 font-light mt-0.5 block">Wajib memilih minimal 3 unit topping</span>
                                </div>
                                <span class="text-xs font-bold px-2.5 py-1 rounded-md transition-colors" 
                                      :class="getTotalToppingUnits() < 3 ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-green-50 text-green-700 border border-green-200'"
                                      x-text="getTotalToppingUnits() < 3 ? 'Pilih minimal 3 (' + getTotalToppingUnits() + ' unit)' : getTotalToppingUnits() + ' Unit Terpilih (Aman)'">
                                </span>
                            </div>

                            <div class="divide-y divide-gray-50 max-h-55 overflow-y-auto">
                                <template x-for="group in groupedToppings()" :key="group.name">
                                    <div class="pt-3">
                                        <div class="px-4 pb-3 text-xs font-black uppercase tracking-wide text-gray-500 bg-gray-50"><span x-text="group.name"></span></div>
                                        <template x-for="top in group.toppings" :key="top.id">
                                            <div class="flex items-center justify-between p-3.5 hover:bg-gray-50/50 transition select-none">
                                                <div class="flex items-center space-x-3 min-w-0">
                                                    <div class="w-10 h-10 rounded-lg overflow-hidden bg-orange-50 border border-orange-100 shrink-0">
                                                        <img :src="top.image" :alt="top.name" class="w-full h-full object-cover">
                                                    </div>
                                                    <span class="text-xs sm:text-sm font-semibold text-gray-700 truncate" x-text="top.name"></span>
                                                </div>
                                                <div class="flex items-center gap-2 shrink-0">
                                                    <div class="flex items-center border border-gray-200 rounded-full overflow-hidden bg-white">
                                                        <button type="button" @click.stop="changeToppingQty(top, -1)" class="w-8 h-8 text-gray-600 hover:bg-gray-100">-</button>
                                                        <div class="w-10 text-center text-sm font-semibold text-gray-800" x-text="getToppingQty(top)"></div>
                                                        <button type="button" @click.stop="changeToppingQty(top, 1)" class="w-8 h-8 text-gray-600 hover:bg-gray-100">+</button>
                                                    </div>
                                                    <span class="text-xs text-gray-400 font-bold" x-text="formatPrice(top.price * getToppingQty(top))"></span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- BLOK B: OPSI KHUSUS MINUMAN --}}
                <div x-show="selectedMenu.name && !strtoupper(selectedMenu.name).includes('SEBLAK')">
                    <div class="space-y-5">
                        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-xs">
                            <span class="block font-bold text-gray-800 text-xs sm:text-sm mb-3">Suhu Minuman <span class="text-red-500">*</span></span>
                            
                            <div x-show="strtoupper(selectedMenu.name).includes('POP ICE')">
                                <div class="grid grid-cols-1">
                                    <div class="flex items-center justify-center p-3 border border-blue-500 bg-blue-50 text-blue-600 rounded-xl font-bold text-xs sm:text-sm text-center shadow-inner">
                                        🧊 Dingin Saja (Fixed Ice Only)
                                    </div>
                                </div>
                            </div>

                            <div x-show="!strtoupper(selectedMenu.name).includes('POP ICE')">
                                <div class="grid grid-cols-2 gap-3">
                                    <label class="flex items-center justify-center p-3 border rounded-xl cursor-pointer font-bold text-xs sm:text-sm text-center transition" :class="drinkTemp === 'Ice' ? 'border-blue-500 bg-blue-50 text-blue-600' : 'border-gray-200 text-gray-600 bg-white'">
                                        <input type="radio" name="drink_temp" value="Ice" x-model="drinkTemp" class="sr-only"> 🧊 Dingin (Ice)
                                    </label>
                                    <label class="flex items-center justify-center p-3 border rounded-xl cursor-pointer font-bold text-xs sm:text-sm text-center transition" :class="drinkTemp === 'Hot' ? 'border-red-500 bg-red-50 text-red-600' : 'border-gray-200 text-gray-600 bg-white'">
                                        <input type="radio" name="drink_temp" value="Hot" x-model="drinkTemp" class="sr-only"> ☕ Panas (Hot)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-xs">
                            <span class="block font-bold text-gray-800 text-xs sm:text-sm mb-3">Pilihan Varian Rasa <span class="text-red-500">*</span></span>
                            <div class="grid grid-cols-2 gap-2.5">
                                <template x-for="flavor in (strtoupper(selectedMenu.name).includes('POP ICE') ? popIceFlavors : (strtoupper(selectedMenu.name).includes('NUTRISARI') ? nutriSariFlavors : goodDayFlavors))" :key="flavor">
                                    <label class="flex items-center p-3 border rounded-xl cursor-pointer font-bold text-xs text-left transition select-none" :class="selectedFlavor === flavor ? 'border-red-500 bg-red-50 text-red-600' : 'border-gray-100 text-gray-600 bg-white'">
                                        <input type="radio" name="drink_flavor" :value="flavor" x-model="selectedFlavor" class="sr-only">
                                        <span x-text="flavor"></span>
                                    </label>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CATATAN TAMBAHAN --}}
                <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-xs">
                    <label class="block font-bold text-gray-800 text-xs sm:text-sm mb-2">Catatan Tambahan untuk Dapur</label>
                    <textarea x-model="notes" rows="2" class="w-full border border-gray-100 rounded-xl p-3 text-xs focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition placeholder:text-gray-300" placeholder="Contoh: Es batu sedikit saja, kuah banjir..."></textarea>
                </div>
            </div>

            <div class="p-4 border-t border-gray-100 bg-white rounded-b-3xl sm:rounded-b-2xl flex items-center justify-between gap-4">
                <div class="pl-1">
                    <span class="text-[9px] uppercase tracking-wider text-gray-400 font-bold block">Total Harga</span>
                    <span class="text-lg font-black text-red-600" x-text="formatPrice(totalCalculatedPrice)"></span>
                </div>
                <button @click="addCustomToCart()" class="bg-red-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-red-700 transition shadow-md text-xs cursor-pointer">
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
        notes: '',
        selectedFlavor: 'Original',
        drinkTemp: 'Ice', 
        selectedToppings: [], 
        totalCalculatedPrice: 0,
        
        cart: @json(session('cart', [])), 
        
        toppingList: [
            { id: 1, name: 'Kerupuk jaat secentong', price: 1000, category: 'Kerupuk', image: "{{ asset('images/kerupukjaat.jpg') }}" },
            { id: 2, name: 'Kerupuk mawar secentong', price: 1000, category: 'Kerupuk', image: "{{ asset('images/kerupukmawar.jpg') }}" },
            { id: 3, name: 'Makaroni spiral secentong', price: 1000, category: 'Makaroni & Mie', image: "{{ asset('images/makaronispiral.jpg') }}" },
            { id: 4, name: 'Kwetiau secentong', price: 2000, category: 'Makaroni & Mie', image: "{{ asset('images/kwetiau.jpg') }}" },
            { id: 5, name: 'Kerupuk jengkol secentong', price: 1000, category: 'Kerupuk', image: "{{ asset('images/kpjengkol.jpg') }}" },
            { id: 6, name: 'Makaroni hitam secentong', price: 1000, category: 'Makaroni & Mie', image: "{{ asset('images/makaronihitam.jpg') }}" },
            { id: 7, name: 'Makaroni kuning secentong', price: 1000, category: 'Makaroni & Mie', image: "{{ asset('images/makaronikuning.jpg') }}" },
            { id: 8, name: 'Mie kering', price: 2000, category: 'Makaroni & Mie', image: "{{ asset('images/miekering.jpeg') }}" },
            { id: 9, name: 'Kerupuk bawang secentong', price: 1000, category: 'Kerupuk', image: "{{ asset('images/kpbawang.jpg') }}" },
            { id: 10, name: 'Kerupuk mie secentong', price: 1000, category: 'Kerupuk', image: "{{ asset('images/kpmie.jpg') }}" },
            { id: 11, name: 'Kerupuk oren secentong', price: 1000, category: 'Kerupuk', image: "{{ asset('images/kporen.jpg') }}" },
            { id: 12, name: 'Telur ayam 1pcs', price: 3000, category: 'Protein', image: "{{ asset('images/telur.jpeg') }}" },
            { id: 13, name: 'Baso 1 pcs', price: 2000, category: 'Protein', image: "{{ asset('images/baso.jpg') }}" },
            { id: 14, name: 'Cilok 1 pcs', price: 2000, category: 'Protein', image: "{{ asset('images/cilok.jpg') }}" },
            { id: 15, name: 'Sawi putih 1 lembar', price: 1500, category: 'Sayuran', image: "{{ asset('images/sawiputih.jpg') }}" },
            { id: 16, name: 'Kangkung 1 ikat', price: 2500, category: 'Sayuran', image: "{{ asset('images/kangkung.jpg') }}" },
            { id: 17, name: 'Jamur enoki setengah bungkus', price: 3000, category: 'Sayuran', image: "{{ asset('images/jamurenoki.jpg') }}" },
            { id: 18, name: 'Batagor kering 1 pcs', price: 2500, category: 'Protein', image: "{{ asset('images/batagorkering.jpeg') }}" },
            { id: 19, name: 'Cuanki lidah 1 pcs', price: 3000, category: 'Protein', image: "{{ asset('images/cuankilidah.jpg') }}" },
            { id: 20, name: 'Sosis 1 pcs', price: 2000, category: 'Protein', image: "{{ asset('images/sosis.jpg') }}" }
        ],

        popIceFlavors: ['Chocolate', 'Strawberry', 'Taro', 'Vanilla Blue', 'Mango', 'Avocado'],
        goodDayFlavors: ['Carrebian Nut', 'Mocacinno', 'Coolin', 'Vanilla Latte'],
        nutriSariFlavors: ['Jeruk Peras', 'American Sweet Orange', 'NutriSari Sweet Mango', 'NutriSari Jeruk Nipis'],

        get cartCount() { return this.cart.reduce((sum, item) => sum + (parseInt(item.quantity) || 1), 0); },
        get cartTotal() { return this.cart.reduce((sum, item) => sum + (parseFloat(item.price) * (parseInt(item.quantity) || 1)), 0); },
        
        openCustomizeModal(menu) {
            this.selectedMenu = Object.assign({}, menu); 
            this.notes = '';
            this.selectedToppings = [];
            this.spicy = 3;
            this.soup = 'pedas gurih';
            this.totalCalculatedPrice = parseFloat(menu.price);
            this.drinkTemp = 'Ice'; 
            
            if(this.strtoupper(menu.name).includes('POP ICE')) {
                this.selectedFlavor = 'Chocolate';
                this.drinkTemp = 'Ice'; 
            } else if(this.strtoupper(menu.name).includes('GOOD DAY')) {
                this.selectedFlavor = 'Mocacinno';
            } else if(this.strtoupper(menu.name).includes('NUTRISARI')) {
                this.selectedFlavor = 'Jeruk Peras';
            } else {
                this.selectedFlavor = 'Original';
            }

            this.modalOpen = true;
        },
        
        getToppingQty(topping) {
            const item = this.selectedToppings.find(t => t.id === topping.id);
            return item ? item.qty : 0;
        },

        groupedToppings() {
            const groups = {};
            this.toppingList.forEach(top => {
                const category = top.category || 'Lainnya';
                if (!groups[category]) { groups[category] = []; }
                groups[category].push(top);
            });
            return Object.keys(groups).map(name => ({ name, toppings: groups[name] }));
        },

        getTotalToppingUnits() { return this.selectedToppings.reduce((sum, item) => sum + item.qty, 0); },

        changeToppingQty(topping, delta) {
            const index = this.selectedToppings.findIndex(t => t.id === topping.id);
            const currentQty = index > -1 ? this.selectedToppings[index].qty : 0;
            const nextQty = currentQty + delta;

            if (nextQty <= 0) {
                if (index > -1) { this.selectedToppings.splice(index, 1); }
                if (currentQty > 0) { this.totalCalculatedPrice -= topping.price * currentQty; }
                return;
            }

            if (index > -1) {
                this.selectedToppings[index].qty = nextQty;
            } else {
                this.selectedToppings.push({ ...topping, qty: 1 });
            }
            this.totalCalculatedPrice += topping.price * delta;
        },
        
        addCustomToCart() {
            const isSeblak = this.strtoupper(this.selectedMenu.name).includes('SEBLAK');
            
            if (isSeblak && this.getTotalToppingUnits() < 3) {
                alert('Wajib memilih minimal 3 unit topping untuk melanjutkan menu Seblak!');
                return;
            }

            let menuName = this.selectedMenu.name;
            if (!isSeblak) {
                const flv = this.selectedFlavor.toLowerCase();
                const isBuah = (flv.includes('peras') || flv.includes('orange') || flv.includes('mango') || flv.includes('mangga') || flv.includes('nipis'));
                const isKopi = (flv.includes('cappuccino') || flv.includes('mocacinno') || flv.includes('moka') || flv.includes('vanilla') || flv.includes('latte') || flv.includes('coolin') || flv.includes('nut'));
                const isPopIceFlavor = (flv.includes('taro') || flv.includes('avocado') || flv.includes('permen') || flv.includes('bubble') || flv.includes('chocolate') || flv.includes('strawberry') || flv.includes('blue'));

                if (isBuah) { menuName = 'Nutrisari'; } 
                else if (isKopi) { menuName = 'Good Day'; } 
                else if (isPopIceFlavor) { menuName = 'Pop Ice'; this.drinkTemp = 'Ice'; }
            }
            
            const cartItem = {
                id: 'custom-' + Date.now(),
                menu_id: this.selectedMenu.id, 
                name: menuName,
                price: this.totalCalculatedPrice,
                quantity: 1, 
                qty: 1,      
                options: {
                    soup: isSeblak ? this.soup : null,
                    spicy: isSeblak ? this.spicy : null,
                    flavor: !isSeblak ? this.selectedFlavor : null,
                    temp: !isSeblak ? this.drinkTemp : null, 
                    toppings: isSeblak ? [...this.selectedToppings] : [],
                    notes: this.notes
                }
            };
            
            this.cart.push(cartItem);
            this.saveCartToSession(); 
            this.modalOpen = false;
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
            .then(data => { console.log('Session Keranjang Ter-sinkron:', data); })
            .catch(err => console.error('Gagal sinkronisasi session:', err));
        },
        
        formatPrice(value) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(value); },
        strtoupper(str) { return str ? str.toUpperCase() : ''; }
    }
}
</script>
@endsection