<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEBLAKWSP - Sistem Pemesanan</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50 antialiased text-gray-800 m-0 p-0">

    <nav class="w-full bg-white border-b border-gray-100 shadow-xs h-16 flex items-center px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl w-full mx-auto flex items-center justify-between">
            <a href="{{ route('landing') }}" class="font-black text-lg tracking-wider text-gray-900">
                SEBLAK<span class="text-red-600">WSP</span>
            </a>
            
            <div class="flex items-center space-x-6 text-sm font-bold text-gray-600">
                <a href="{{ route('landing') }}" class="hover:text-red-600 transition">Home</a>
                <a href="{{ route('front.menu') }}" class="hover:text-red-600 transition">Menu</a>
            </div>
        </div>
    </nav>

    <main class="w-full">
        @yield('content')
    </main>

</body>
</html>