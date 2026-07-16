<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ $siteName }}</title>

    @if($settings['site_favicon'])
    <link rel="icon" href="{{ storage_url($settings['site_favicon']) }}" type="image/x-icon">
    @endif

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/lucide@1.24.0/dist/umd/lucide.min.js"></script>

    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">

    @stack('styles')
</head>

<body class="min-h-screen bg-gradient-to-br from-slate-100 via-slate-50 to-indigo-50/40">
    <div class="flex h-screen overflow-hidden">
        @include('admin.layouts.sidebar')

        <div id="mainContent" class="relative flex flex-col flex-1 overflow-hidden transition-all duration-300">
            {{-- top accent line --}}
            <div class="absolute top-0 inset-x-0 h-0.5 bg-gradient-to-r from-indigo-500 via-violet-500 to-fuchsia-500 z-30"></div>

            @include('admin.layouts.header')

            <main class="flex-1 overflow-y-auto">
                <div class="p-6 max-w-[1600px] mx-auto w-full">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    {{-- Overlay for mobile sidebar --}}
    <div id="sidebarOverlay" class="fixed inset-0 z-40 hidden bg-black bg-opacity-50 lg:hidden"></div>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="{{ asset('assets/js/admin.js') }}"></script>

    <script>lucide.createIcons();</script>

    @stack('scripts')

    @include('admin.partials.toast')
</body>

</html>
