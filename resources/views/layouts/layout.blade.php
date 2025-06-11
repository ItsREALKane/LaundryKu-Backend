<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - LaundryKu Admin</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Icons -->
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <!-- Custom CSS -->
    <style>
        /* Add custom styles here */
    </style>
</head>
<body class="bg-gray-100">
    @if(request()->is('login'))
        @yield('content')
    @else
        <div class="flex h-screen">
            <!-- Sidebar -->
            <aside class="w-64 bg-white shadow-lg">
                <div class="p-4">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-32 mx-auto">
                </div>
                <nav class="mt-6">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-100">
                        <span class="iconify" data-icon="mdi:view-dashboard-outline"></span>
                        <span class="ml-3">Dashboard</span>
                    </a>
                    <a href="{{ route('admin.orders') }}" class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-100">
                        <span class="iconify" data-icon="mdi:shopping-outline"></span>
                        <span class="ml-3">Pesanan</span>
                    </a>
                    <a href="{{ route('admin.bills') }}" class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-100">
                        <span class="iconify" data-icon="mdi:receipt-outline"></span>
                        <span class="ml-3">Tagihan</span>
                    </a>
                    <a href="{{ route('admin.history') }}" class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-100">
                        <span class="iconify" data-icon="mdi:history"></span>
                        <span class="ml-3">Riwayat</span>
                    </a>
                    <a href="{{ route('admin.settings') }}" class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-100">
                        <span class="iconify" data-icon="mdi:cog-outline"></span>
                        <span class="ml-3">Pengaturan</span>
                    </a>
                </nav>
            </aside>

            <!-- Main Content -->
            <div class="flex-1">
                <!-- Top Navigation -->
                <nav class="bg-white shadow-lg p-4">
                    <div class="flex justify-between items-center">
                        <h1 class="text-xl font-semibold">@yield('header')</h1>
                        <div class="flex items-center space-x-4">
                            <button class="text-gray-600 hover:text-gray-800">
                                <span class="iconify" data-icon="mdi:bell-outline"></span>
                            </button>
                            <div class="flex items-center space-x-2">
                                <span class="iconify" data-icon="mdi:account-circle-outline"></span>
                                <span>{{ Auth::user()->name }}</span>
                            </div>
                            <form action="{{ route('admin.logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-gray-600 hover:text-gray-800">
                                    <span class="iconify" data-icon="mdi:logout"></span>
                                </button>
                            </form>
                        </div>
                    </div>
                </nav>

                <!-- Page Content -->
                <main class="p-6">
                    @yield('content')
                </main>
            </div>
        </div>
    @endif

    <!-- Custom Scripts -->
    @stack('scripts')
</body>
</html>