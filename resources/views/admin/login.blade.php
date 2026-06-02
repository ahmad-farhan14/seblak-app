@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 flex flex-col justify-center items-center px-4">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl p-8 border border-gray-100">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-red-100 text-red-600 rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl font-black">
                🏪
            </div>
            <h2 class="text-2xl font-black text-gray-800">Akses Masuk Kasir</h2>
            <p class="text-sm text-gray-400 mt-1">Masukkan kode akses khusus perangkat kasir toko</p>
        </div>

        @if(session('error'))
            <div class="mb-4 p-4 text-sm text-red-700 bg-red-50 rounded-2xl border border-red-100 font-bold">
                ⚠️ {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 p-4 text-sm text-red-700 bg-red-50 rounded-2xl border border-red-100 font-bold">
                ⚠️ {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-6">
            @csrf
            {{-- Email kasir disembunyikan, cukup input password saja --}}
            <input type="hidden" name="email" value="kasir@seblakwsp.com">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Kode Sandi Akses</label>
                <input type="password" name="password" placeholder="••••••••" required
                    class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-red-500 font-mono tracking-widest text-center text-lg">
            </div>
            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-4 rounded-2xl shadow-lg shadow-red-600/20 transition-all active:scale-[0.98]">
                Buka Monitor Kasir
            </button>
        </form>
    </div>
</div>
@endsection