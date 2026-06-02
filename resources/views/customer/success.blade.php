@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col justify-center items-center px-4 py-12">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl p-8 text-center border border-gray-100">
        <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold">✓</div>
        <h2 class="text-2xl font-black text-gray-900 mb-2">Pesanan Terkirim!</h2>
        <p class="text-sm text-gray-400 mb-6">Pesanan atas nama <span class="font-bold text-gray-700">{{ $customerName }}</span> berhasil disimpan.</p>

        @if($paymentMethod === 'QRIS')
            <div class="space-y-4 border p-4 bg-gray-50 rounded-2xl">
                <h3 class="font-black text-base text-gray-800">Silakan Scan QRIS</h3>
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=PembayaranSeblak" class="mx-auto border p-2 rounded-xl bg-white shadow-xs">
                <p class="text-xs text-gray-500">Buka aplikasi e-wallet kamu untuk melakukan pembayaran.</p>
            </div>
        @else
            <div class="space-y-4 border p-6 bg-yellow-50/50 border-yellow-100 rounded-2xl">
                <h3 class="font-black text-base text-yellow-800">Bayar Tunai di Kasir</h3>
                <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto text-3xl">💰</div>
                <p class="text-xs text-gray-600">Silakan sebutkan namamu ke kasir untuk menyelesaikan pembayaran tunai.</p>
            </div>
        @endif

        <a href="/" class="mt-8 block w-full py-4 bg-red-600 hover:bg-red-700 text-white font-bold rounded-2xl shadow-lg transition-all">Kembali ke Menu Utama</a>
    </div>
</div>
@endsection