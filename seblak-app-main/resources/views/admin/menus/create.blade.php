@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Tambah Menu Baru</h1>

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-100 rounded">
            <ul class="text-sm text-red-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.menus.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 bg-white p-6 rounded-lg shadow">
        @csrf

        <div>
            <label class="block text-sm font-semibold mb-1">Kategori</label>
            <select name="category_id" class="w-full border rounded p-2">
                <option value="">-- Pilih Kategori --</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-semibold mb-1">Nama Menu</label>
            <input type="text" name="name" class="w-full border rounded p-2" value="{{ old('name') }}" required>
        </div>

        <div>
            <label class="block text-sm font-semibold mb-1">Deskripsi</label>
            <textarea name="description" rows="3" class="w-full border rounded p-2">{{ old('description') }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-semibold mb-1">Harga (rp)</label>
            <input type="number" name="price" class="w-full border rounded p-2" value="{{ old('price', 0) }}" required>
        </div>

        <div>
            <label class="block text-sm font-semibold mb-1">Gambar (opsional)</label>
            <input type="file" name="image" accept="image/*">
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-600">Batal</a>
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">Simpan</button>
        </div>
    </form>
</div>
@endsection
