<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }} — Warkop Samalila</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        coffee: {
                            50: '#F8F1E9', 100: '#F5EDE3', 200: '#E7D4BC',
                            400: '#B08D57', 500: '#8C6A4A', 600: '#6F4E37',
                            700: '#5A3E2B', 800: '#4B2E1E', 900: '#2B1B12',
                        },
                    },
                    fontFamily: { sans: ['Poppins', 'ui-sans-serif', 'system-ui'] },
                },
            },
        };
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        ::-webkit-scrollbar { width: 8px; } ::-webkit-scrollbar-thumb { background: #E7D4BC; border-radius: 8px; }
        .nav-link { transition: all .18s ease; }
        .nav-link:hover { transform: translateX(4px); }
        .card-pop { animation: cardPop .35s ease-out; }
        @keyframes cardPop { 0% { opacity:0; transform: translateY(10px);} 100% {opacity:1; transform: translateY(0);} }
        .toast-in { animation: toastIn .3s ease-out; }
        @keyframes toastIn { 0% { opacity:0; transform: translateY(-12px) scale(.95);} 100% {opacity:1; transform: translateY(0) scale(1);} }
    </style>
</head>
<body class="bg-coffee-50 font-sans text-coffee-900 antialiased" x-data="{ sidebarOpen: false }">

    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
               class="fixed lg:static z-40 inset-y-0 left-0 w-64 bg-coffee-800 text-coffee-50 transform transition-transform duration-300 ease-in-out flex flex-col">
            <div class="px-6 py-5 flex items-center gap-2 border-b border-coffee-700">
                <span class="text-2xl">☕</span>
                <div>
                    <p class="font-bold leading-tight">Warkop Samalila</p>
                    <p class="text-xs text-coffee-200 capitalize">{{ auth()->user()->role ?? '' }}</p>
                </div>
            </div>

            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                @php $role = auth()->user()->role ?? null; @endphp

                @if($role === 'owner')
                    @php
                        $ownerLinks = [
                            ['route' => 'owner.dashboard', 'icon' => '📊', 'label' => 'Dashboard'],
                            ['route' => 'owner.verification.index', 'icon' => '✅', 'label' => 'Verifikasi Pembayaran'],
                            ['route' => 'owner.categories.index', 'icon' => '🗂️', 'label' => 'Kategori Menu'],
                            ['route' => 'owner.menus.index', 'icon' => '🍽️', 'label' => 'Data Menu'],
                            ['route' => 'owner.payment-settings.index', 'icon' => '💳', 'label' => 'Metode Pembayaran'],
                            ['route' => 'owner.orders.index', 'icon' => '🧾', 'label' => 'Riwayat & Cek Order'],
                            ['route' => 'owner.settings.whatsapp', 'icon' => '📱', 'label' => 'Pengaturan WhatsApp'],
                        ];
                    @endphp
                    @foreach($ownerLinks as $link)
                        <a href="{{ route($link['route']) }}"
                           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm
                           {{ request()->routeIs($link['route']) || (str_starts_with($link['route'], 'owner.orders') && request()->routeIs('owner.orders.*')) ? 'bg-coffee-600 text-white font-semibold' : 'text-coffee-100 hover:bg-coffee-700' }}">
                            <span>{{ $link['icon'] }}</span> {{ $link['label'] }}
                        </a>
                    @endforeach
                @elseif($role === 'dapur')
                    <a href="{{ route('dapur.dashboard') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm bg-coffee-600 text-white font-semibold">
                        <span>🍳</span> Pesanan Masuk
                    </a>
                @elseif($role === 'superadmin')
                    <a href="{{ route('superadmin.dashboard') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('superadmin.dashboard') ? 'bg-coffee-600 text-white font-semibold' : 'text-coffee-100 hover:bg-coffee-700' }}">
                        <span>📊</span> Dashboard
                    </a>
                    <a href="{{ route('superadmin.accounts.index') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('superadmin.accounts.*') ? 'bg-coffee-600 text-white font-semibold' : 'text-coffee-100 hover:bg-coffee-700' }}">
                        <span>👥</span> Kelola Akun
                    </a>
                    <a href="{{ route('superadmin.logs.index') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('superadmin.logs.*') ? 'bg-coffee-600 text-white font-semibold' : 'text-coffee-100 hover:bg-coffee-700' }}">
                        <span>📋</span> Log Aktivitas Pengguna
                    </a>
                @endif
            </nav>

            <div class="px-3 py-4 border-t border-coffee-700">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn-press w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-coffee-100 hover:bg-red-600/80 transition">
                        <span>🚪</span> Keluar
                    </button>
                </form>
            </div>
        </aside>

        <div class="fixed inset-0 bg-black/40 z-30 lg:hidden" x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" x-transition.opacity></div>

        <!-- Main -->
        <div class="flex-1 flex flex-col min-w-0">
            <header class="bg-white border-b border-coffee-100 px-4 lg:px-8 py-4 flex items-center justify-between sticky top-0 z-20">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden text-coffee-700 text-2xl">☰</button>
                    <h1 class="text-lg lg:text-xl font-bold text-coffee-800">{{ $title ?? 'Dashboard' }}</h1>
                </div>
                <div class="text-xs sm:text-sm text-coffee-600 flex items-center gap-3">
                    <span class="hidden md:inline-flex items-center gap-1.5 bg-coffee-50 border border-coffee-200/80 px-3 py-1.5 rounded-xl font-medium text-coffee-700 shadow-xs">
                        <span>📅 {{ now()->translatedFormat('d M Y') }}</span>
                        <span class="text-coffee-300">|</span>
                        <span>⏰ {{ now()->format('H:i') }} WIB</span>
                    </span>
                    <span>Halo, <span class="font-semibold text-coffee-800">{{ auth()->user()->name ?? '' }}</span> 👋</span>
                </div>
            </header>

            <main class="flex-1 p-4 lg:p-8">
                @if(session('success'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3500)" x-transition
                         class="toast-in mb-6 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 flex items-center justify-between">
                        <span>✅ {{ session('success') }}</span>
                        <button @click="show = false" class="text-emerald-500 font-bold">&times;</button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 rounded-xl bg-red-50 border border-red-200 text-red-700 px-4 py-3">
                        <ul class="list-disc list-inside text-sm space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session('wa_dapur_link'))
                    <div class="mb-6 rounded-xl bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 flex items-center justify-between flex-wrap gap-2">
                        <span>📲 Pesanan sudah disetujui. Teruskan ke dapur via WhatsApp sekarang.</span>
                        <a href="{{ session('wa_dapur_link') }}" target="_blank"
                           class="btn-press bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg">
                            Kirim ke Dapur →
                        </a>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
