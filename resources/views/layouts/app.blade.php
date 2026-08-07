@props([
    'title' => config('app.name', 'Sistem Absensi UKM Taekwondo'),
    'mobile' => 'drawer',
    'mobileHeaderTitle' => '',
])

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'Sistem Absensi UKM Taekwondo') }}</title>

    @php
        $pengaturan = \App\Models\PengaturanProfil::instance();
        $faviconPath = $pengaturan->logo_unit_kegiatan && file_exists(public_path($pengaturan->logo_unit_kegiatan))
            ? asset($pengaturan->logo_unit_kegiatan)
            : null;
    @endphp
    <link rel="icon" href="{{ $faviconPath ?: asset('favicon.ico') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <script>
        // Cegah flash tampilan terang saat dimuat dalam dark mode
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="text-gray-800 dark:text-gray-200 flex min-h-screen font-sans transition-colors duration-300">

    @isset($header)
        {{ $header }}
    @endisset

    @if (($mobile ?? 'drawer') === 'drawer' && auth()->user()?->role !== 'anggota')
        <x-mobile-header :title="$mobileHeaderTitle ?? ''" type="drawer" />
        <x-mobile-overlay />
    @else
        <x-mobile-header :title="$mobileHeaderTitle ?? ''" type="static" />
    @endif

    @if (auth()->user()?->role === 'anggota')
        <x-bottom-nav />
    @endif
    <x-sidebar :drawer="($mobile ?? 'drawer') === 'drawer'" />

    <main class="w-full md:ml-[280px] pt-20 md:pt-8 min-h-screen pb-24 md:pb-12 transition-all duration-300">
        <div class="p-4 sm:p-5 md:p-6 max-w-[1280px] mx-auto space-y-6 md:space-y-8 w-full">
            {{ $slot }}
        </div>
    </main>

    <!-- Toast Container (custom messages) -->
    <div id="toast-container" class="fixed top-20 md:top-5 right-5 z-[100] flex flex-col gap-3"></div>

    <!-- Toast Notification -->
    <div id="toast" class="flex items-center gap-3 px-4 py-3 bg-white dark:bg-gray-800 rounded-2xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.2)] dark:shadow-black/50 border border-gray-100 dark:border-gray-700 min-w-[300px]">
        <div id="toastIconContainer" class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-green-100 text-green-600">
            <span id="toastIcon" class="material-symbols-outlined text-[20px]">check_circle</span>
        </div>
        <div class="flex-1">
            <h4 id="toastTitle" class="text-sm font-bold text-gray-900 dark:text-white leading-tight">Berhasil</h4>
            <p id="toastMessage" class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Pesan notifikasi.</p>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
