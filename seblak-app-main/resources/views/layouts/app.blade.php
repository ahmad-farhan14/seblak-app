<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seblak WSP - Aplikasi Pemesanan</title>
    <!-- Baris sakti di bawah ini yang menyambungkan ke server Vite Anda (localhost:5173) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Alpine.js untuk interaksi modal & tombol tanpa perlu jQuery -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 text-gray-900 font-sans antialiased">

    <!-- Navbar Sticky Modern -->
    <nav class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-gray-100" x-data="{ open: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('landing') }}" class="text-xl font-black tracking-wider text-red-600 uppercase">
                        Seblak<span class="text-amber-500">WSP</span>
                    </a>
                </div>
                
                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-8 font-medium text-gray-600">
                    <a href="#hero" class="hover:text-red-600 transition">Home</a>
                    <a href="#menu-favorit" class="hover:text-red-600 transition">Favorit</a>
                    <a href="{{ route('front.menu') }}" class="bg-red-600 text-white px-4 py-2 rounded-full text-sm hover:bg-red-700 transition shadow-sm">Pesan Sekarang</a>
                </div>

                <!-- Hamburger Menu Button Mobile -->
                <div class="flex items-center md:hidden">
                    <button @click="open = !open" class="text-gray-500 hover:text-gray-600 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Panel -->
        <div x-show="open" x-transition class="md:hidden bg-white border-b border-gray-200 px-4 pt-2 pb-4 space-y-1">
            <a href="#hero" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50">Home</a>
            <a href="#menu-favorit" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50">Favorit</a>
            <a href="{{ route('front.menu') }}" class="block text-center bg-red-600 text-white px-4 py-2 rounded-xl text-sm font-bold">Pesan Sekarang</a>
        </div>
    </nav>

    <!-- Konten Utama Halaman Akan Disuntikkan di Sini -->
    <main>
        @yield('content')
    </main>

</body>
</html>