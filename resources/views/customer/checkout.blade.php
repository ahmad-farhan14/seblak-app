@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-10 px-4" x-data="{ paymentMethod: 'tunai' }">
    <div class="max-w-2xl mx-auto">
        
        <div class="mb-6 flex items-center space-x-3">
            <a href="{{ route('front.menu') }}" class="w-10 h-10 bg-white rounded-xl flex items-center justify-center border border-gray-200 text-gray-600 font-bold hover:bg-gray-50 transition">
                ←
            </a>
            <div>
                <h2 class="text-xl font-black text-gray-800">Konfirmasi Pesanan</h2>
                <p class="text-xs text-gray-400">Satu langkah lagi sebelum pesananmu dimasak dapur</p>
            </div>
        </div>

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-50 border border-red-100 text-red-700 text-xs font-bold rounded-2xl">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6">
            
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-xs">
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-wider mb-4">Daftar Menu Kamu</h3>
                <div class="divide-y divide-gray-50">
                    @forelse($cart as $item)
                        <div class="py-3.5 flex justify-between items-start gap-4">
                            <div class="min-w-0 flex-1">
                                <h4 class="font-bold text-sm text-gray-800 truncate">
                                    {{ $item['name'] }} <span class="text-red-600 font-black ml-0.5">x{{ $item['quantity'] }}</span>
                                </h4>
                                <p class="text-xs text-gray-400 mt-0.5 leading-relaxed font-medium">
                                    @if(!empty($item['options']['toppings']))
                                        Opsi: {{ collect($item['options']['toppings'])->pluck('name')->implode(', ') }}
                                    @elseif(isset($item['options']['flavor']))
                                        @php
                                            $itemTemp = $item['options']['temp'] ?? 'Ice';
                                            if (str_contains(strtolower($item['name']), 'pop ice')) {
                                                $itemTemp = 'Ice';
                                            }
                                        @endphp
                                        Suhu: <span class="text-blue-600 font-bold">{{ $itemTemp }}</span> | Varian Rasa: {{ $item['options']['flavor'] }}
                                    @else
                                        Original
                                    @endif
                                </p>
                                
                                @if(!empty($item['options']['notes']))
                                    <p class="text-[11px] text-orange-600 bg-orange-50/70 inline-block px-2 py-0.5 rounded font-bold mt-1">
                                        📝 Notes: "{{ $item['options']['notes'] }}"
                                    </p>
                                @endif
                            </div>
                            <span class="text-sm font-bold text-gray-700 whitespace-nowrap">
                                Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                            </span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 py-4 italic text-center">Keranjang belanja kosong.</p>
                    @endforelse
                </div>
            </div>

            <form id="payment-form" action="{{ route('cart.process') }}" method="POST" class="bg-white p-6 rounded-3xl border border-gray-100 shadow-xs space-y-5">
                @csrf
                
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-wider">Data Pengirim</h3>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Nama Kamu <span class="text-red-500">*</span></label>
                    <input type="text" id="customer_name" name="customer_name" required value="{{ old('customer_name') }}"
                           placeholder="Contoh: Ahmad, Siti, dsb..."
                           class="w-full border border-gray-200 bg-gray-50 rounded-2xl px-4 py-3.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:bg-white transition-all placeholder:text-gray-300 font-medium">
                    @error('customer_name')
                        <span class="text-red-500 text-xs mt-1 block font-bold">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Catatan Tambahan untuk Kasir (Opsional)</label>
                    <textarea id="notes" name="notes" rows="2" placeholder="Contoh: Tolong struknya dicetak dua ya mbak..."
                              class="w-full border border-gray-200 bg-gray-50 rounded-2xl px-4 py-3.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:bg-white transition-all placeholder:text-gray-300 font-medium">{{ old('notes') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Metode Pembayaran <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex flex-col items-center justify-center p-4 border rounded-2xl cursor-pointer transition select-none" 
                               :class="paymentMethod === 'tunai' ? 'border-red-500 bg-red-50 text-red-600 font-black shadow-xs' : 'border-gray-200 text-gray-600 bg-white font-medium hover:bg-gray-50/50'">
                            <input type="radio" name="payment_method" value="tunai" x-model="paymentMethod" class="sr-only">
                            <span class="text-xl mb-1">💵</span>
                            <span class="text-xs uppercase tracking-wider">Bayar Tunai</span>
                        </label>
                        
                        <label class="flex flex-col items-center justify-center p-4 border rounded-2xl cursor-pointer transition select-none" 
                               :class="paymentMethod === 'qris' ? 'border-red-500 bg-red-50 text-red-600 font-black shadow-xs' : 'border-gray-200 text-gray-600 bg-white font-medium hover:bg-gray-50/50'">
                            <input type="radio" name="payment_method" value="qris" x-model="paymentMethod" class="sr-only">
                            <span class="text-xl mb-1">📱</span>
                            <span class="text-xs uppercase tracking-wider">QRIS / Non-Tunai</span>
                        </label>
                    </div>
                </div>

                <div class="border-t border-gray-100 my-2"></div>

                <div class="flex justify-between items-center py-2">
                    <div>
                        <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block">Total Pembayaran</span>
                        <span class="text-xs text-orange-600 font-bold">
                            ({{ session('order_type') === 'take_away' ? '🥡 Bawa Pulang' : '🍽️ Meja #' . session('table_number', 'Dine In') }})
                        </span>
                    </div>
                    <span class="text-xl font-black text-red-600 tracking-tight">
                        Rp {{ number_format(collect($cart)->reduce(function($sum, $item){ return $sum + ($item['price'] * $item['quantity']); }, 0), 0, ',', '.') }}
                    </span>
                </div>

                @if(count($cart) > 0)
                    <button type="submit" id="submit-button" class="w-full bg-red-600 hover:bg-red-700 text-white font-black py-4 px-4 rounded-xl text-xs uppercase tracking-wider transition-all duration-200 active:scale-[0.98] shadow-md shadow-red-600/10 cursor-pointer text-center">
                        🚀 Selesai & Kirim Ke Kasir
                    </button>
                @else
                    <button type="button" disabled class="w-full bg-gray-200 text-gray-400 font-black py-4 px-4 rounded-xl text-xs uppercase tracking-wider cursor-not-allowed text-center">
                        Keranjang Kosong
                    </button>
                @endif
            </form>

        </div>
    </div>
</div>

{{-- SCRIPT INTEGRASI MIDTRANS SNAP DI SISI FRONTEND --}}
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
    document.getElementById('payment-form').addEventListener('submit', function(e) {
        // Cek apakah user memilih pembayaran QRIS
        const selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;
        
        if (selectedMethod === 'qris') {
            e.preventDefault(); // Stop form submit bawaan html browser
            
            const submitBtn = document.getElementById('submit-button');
            submitBtn.disabled = true;
            submitBtn.innerText = "⏳ MENGHUBUNGKAN KE MIDTRANS...";

            // Kirim data form via AJAX Fetch ke backend CartController
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.snap_token) {
                    // Luncurkan Pop-Up Kotak Pembayaran Midtrans secara Magis!
                    window.snap.pay(data.snap_token, {
                        onSuccess: function(result) {
                            // Dialihkan otomatis ke halaman sukses pesanan
                            window.location.href = "/order-success/" + data.order_id;
                        },
                        onPending: function(result) {
                            // Menunggu pembayaran, alihkan ke halaman instruksi
                            window.location.href = "/order-success/" + data.order_id;
                        },
                        onError: function(result) {
                            alert("Pembayaran Gagal! Silakan coba lagi.");
                            submitBtn.disabled = false;
                            submitBtn.innerText = "🚀 Selesai & Kirim Ke Kasir";
                        },
                        onClose: function() {
                            alert('Kamu menutup halaman pembayaran sebelum selesai.');
                            submitBtn.disabled = false;
                            submitBtn.innerText = "🚀 Selesai & Kirim Ke Kasir";
                        }
                    });
                } else {
                    alert(data.message || 'Terjadi kesalahan sistem internal.');
                    submitBtn.disabled = false;
                    submitBtn.innerText = "🚀 Selesai & Kirim Ke Kasir";
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal memproses transaksi digital.');
                submitBtn.disabled = false;
                submitBtn.innerText = "🚀 Selesai & Kirim Ke Kasir";
            });
        }
    });
</script>
@endsection