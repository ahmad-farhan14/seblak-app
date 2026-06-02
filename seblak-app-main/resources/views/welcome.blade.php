<div>
    <span class="block font-bold text-gray-800 text-sm mb-3">Tambah Topping (Bisa Pilih Banyak)</span>
    <div class="grid grid-cols-2 gap-3">
        
        <label class="flex items-center p-2.5 border rounded-2xl cursor-pointer hover:bg-gray-50 transition" :class="selectedToppings.includes(1) ? 'border-red-500 bg-red-50/50' : 'border-gray-100'">
            <input type="checkbox" name="toppings[]" value="1" @change="toggleTopping(1, 3000)" class="rounded text-red-600 focus:ring-red-500 h-4 w-4 accent-red-600">
            <div class="ml-3">
                <span class="block text-xs font-bold text-gray-800">Bakso Sapi</span>
                <small class="text-gray-500 text-[10px]">+ Rp 3.000</small>
            </div>
        </label>

        <label class="flex items-center p-2.5 border rounded-2xl cursor-pointer hover:bg-gray-50 transition" :class="selectedToppings.includes(2) ? 'border-red-500 bg-red-50/50' : 'border-gray-100'">
            <input type="checkbox" name="toppings[]" value="2" @change="toggleTopping(2, 4000)" class="rounded text-red-600 focus:ring-red-500 h-4 w-4 accent-red-600">
            <div class="ml-3">
                <span class="block text-xs font-bold text-gray-800">Dumpling Keju</span>
                <small class="text-gray-500 text-[10px]">+ Rp 4.000</small>
            </div>
        </label>

        <label class="flex items-center p-2.5 border rounded-2xl cursor-pointer hover:bg-gray-50 transition" :class="selectedToppings.includes(3) ? 'border-red-500 bg-red-50/50' : 'border-gray-100'">
            <input type="checkbox" name="toppings[]" value="3" @change="toggleTopping(3, 3000)" class="rounded text-red-600 focus:ring-red-500 h-4 w-4 accent-red-600">
            <div class="ml-3">
                <span class="block text-xs font-bold text-gray-800">Sosis Ayam</span>
                <small class="text-gray-500 text-[10px]">+ Rp 3.000</small>
            </div>
        </label>

        <label class="flex items-center p-2.5 border rounded-2xl cursor-pointer hover:bg-gray-50 transition" :class="selectedToppings.includes(4) ? 'border-red-500 bg-red-50/50' : 'border-gray-100'">
            <input type="checkbox" name="toppings[]" value="4" @change="toggleTopping(4, 2000)" class="rounded text-red-600 focus:ring-red-500 h-4 w-4 accent-red-600">
            <div class="ml-3">
                <span class="block text-xs font-bold text-gray-800">Ceker Empuk</span>
                <small class="text-gray-500 text-[10px]">+ Rp 2.000</small>
            </div>
        </label>

    </div>
</div>