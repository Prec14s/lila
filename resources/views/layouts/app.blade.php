<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>{{ $title ?? 'Warkop Samalila' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#4B2E1E">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    screens: {
                        'xs': '375px',
                    },
                    colors: {
                        coffee: {
                            50: '#F8F1E9', 100: '#F5EDE3', 200: '#E7D4BC',
                            400: '#B08D57', 500: '#8C6A4A', 600: '#6F4E37',
                            700: '#5A3E2B', 800: '#4B2E1E', 900: '#2B1B12',
                        },
                    },
                    fontFamily: { sans: ['Poppins', 'ui-sans-serif', 'system-ui'] },
                    keyframes: {
                        floaty: { '0%,100%': { transform: 'translateY(0)' }, '50%': { transform: 'translateY(-6px)' } },
                        popin: { '0%': { transform: 'scale(0.85)', opacity: 0 }, '100%': { transform: 'scale(1)', opacity: 1 } },
                        steam: { '0%': { transform: 'translateY(0) scaleX(1)', opacity: .55 }, '100%': { transform: 'translateY(-16px) scaleX(1.4)', opacity: 0 } },
                    },
                    animation: {
                        floaty: 'floaty 3s ease-in-out infinite',
                        popin: 'popin .25s ease-out',
                        steam: 'steam 2.2s ease-in-out infinite',
                    },
                },
            },
        };
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        html { scroll-behavior: smooth; -webkit-tap-highlight-color: transparent; }
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-thumb { background: #E7D4BC; border-radius: 8px; }
        .card-hover { transition: transform .25s ease, box-shadow .25s ease; }
        @media (hover: hover) {
            .card-hover:hover { transform: translateY(-4px); box-shadow: 0 12px 24px -8px rgba(75,46,30,.25); }
        }
        .btn-press { transition: transform .12s ease, opacity .12s ease; touch-action: manipulation; }
        .btn-press:active { transform: scale(0.95); opacity: 0.9; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .pb-safe { padding-bottom: max(1rem, env(safe-area-inset-bottom)); }
    </style>
</head>
<body class="bg-coffee-50 text-coffee-900 font-sans antialiased min-h-screen selection:bg-coffee-700 selection:text-white">
    <div class="min-h-screen bg-coffee-50 relative">
        @yield('content')
    </div>

    @stack('scripts')

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('toast', { message: '', show: false, type: 'success' });
        });
    </script>
</body>
</html>
