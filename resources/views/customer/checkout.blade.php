@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8" x-data="checkoutSystem()">
    <h2 class="text-2xl font-black text-gray-900 mb-6">Ringkasan Pesanan</h2>

    <form @submit.prevent="processOrder" class="space-y-6">
        @csrf
        
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-xs">
            <input type="text" x-model="customerName" placeholder="Nama Pelanggan*" 
                   class="w-full p-4 border border-gray-200 rounded-2xl" required>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-xs">
            <h3 class="font-black text-gray-800 mb-4">Pilih Metode Pembayaran</h3>
            <div class="grid grid-cols-2 gap-4">
                <label class="p-4 border-2 rounded-xl cursor-pointer" :class="paymentMethod === 'QRIS' ? 'border-red-500' : ''">
                    <input type="radio" value="QRIS" x-model="paymentMethod" class="hidden">
                    <span class="font-bold">QRIS (Scan)</span>
                </label>
                <label class="p-4 border-2 rounded-xl cursor-pointer" :class="paymentMethod === 'Tunai' ? 'border-red-500' : ''">
                    <input type="radio" value="Tunai" x-model="paymentMethod" class="hidden">
                    <span class="font-bold">Tunai (Cash)</span>
                </label>
            </div>
        </div>

        <button type="submit" class="w-full bg-red-600 text-white font-bold py-4 rounded-2xl">
            Selesaikan Pembayaran
        </button>
    </form>

    <div x-show="showSuccess" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4" x-cloak>
        <div class="bg-white p-8 rounded-3xl w-full max-w-sm text-center">
            <template x-if="paymentMethod === 'QRIS'">
                <div class="space-y-4">
                    <h2 class="font-black text-xl">Scan QRIS</h2>
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=ContohDataPembayaran" class="mx-auto">
                    <p class="text-sm text-gray-500">Silakan scan kode di atas untuk membayar.</p>
                </div>
            </template>
            <template x-if="paymentMethod === 'Tunai'">
                <div class="space-y-4">
                    <h2 class="font-black text-xl">Bayar di Kasir</h2>
                    <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto text-4xl">💰</div>
                    <p class="text-sm text-gray-500">Silakan bawa nomor pesananmu ke kasir untuk membayar tunai.</p>
                </div>
            </template>
            <button @click="window.location.href='/'" class="mt-6 w-full py-3 bg-gray-200 rounded-xl font-bold">Tutup</button>
        </div>
    </div>
</div>

<script>
function checkoutSystem() {
    return {
        showSuccess: false,
        paymentMethod: '',
        customerName: '',
        processOrder() {
            if(!this.paymentMethod) return alert('Pilih metode pembayaran!');
            this.showSuccess = true;
        }
    }
}
</script>
@endsection