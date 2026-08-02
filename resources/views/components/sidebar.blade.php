@props([
    'drawer' => true,
])

@php
    $role = auth()->user()->role ?? 'anggota';
    $isAdminPetugas = in_array($role, ['admin', 'petugas']);

    $items = [];
    if ($isAdminPetugas) {
        $items[] = ['route' => 'dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard'];
        $items[] = ['route' => 'absensi', 'icon' => 'qr_code_scanner', 'label' => 'Absen'];
        $items[] = ['route' => 'anggota.index', 'icon' => 'groups', 'label' => 'Data Anggota'];
        $items[] = ['route' => 'perizinan.index', 'icon' => 'fact_check', 'label' => 'Perizinan'];
        if ($role === 'admin') {
            $items[] = ['route' => 'petugas.index', 'icon' => 'badge', 'label' => 'Petugas Absensi'];
            $items[] = ['route' => 'jadwal.index', 'icon' => 'calendar_month', 'label' => 'Jadwal'];
        }
        $items[] = ['route' => 'hari-libur.index', 'icon' => 'event_busy', 'label' => 'Hari Libur'];
        $items[] = ['route' => 'rekap.index', 'icon' => 'assignment_turned_in', 'label' => 'Rekap Kehadiran'];
        if ($role === 'admin') {
            $items[] = ['route' => 'settings.index', 'icon' => 'settings', 'label' => 'Settings'];
        }
        $subtitle = 'Admin Dashboard';
    } else {
        $items[] = ['route' => 'anggota.dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard'];
        $items[] = ['route' => 'anggota.pengaturan', 'icon' => 'settings', 'label' => 'Pengaturan'];
        $subtitle = 'Sistem Anggota';
    }
@endphp

<aside id="sidebar"
    class="sidebar-menu fixed left-0 top-0 h-screen w-[280px] bg-white dark:bg-gray-800 flex-col p-6 z-50 shadow-[1px_0_15px_rgba(0,0,0,0.03)] dark:shadow-none border-r border-gray-100 dark:border-gray-700 md:translate-x-0 flex hidden md:flex transition-colors duration-300">

    <!-- Brand Header -->
    <div class="flex items-center justify-between mb-10 pl-2">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-brand-blue text-white rounded-2xl flex items-center justify-center font-heading font-bold text-xl shadow-lg shadow-brand-blue/30">
                UT
            </div>
            <div>
                <h1 class="font-heading font-bold text-gray-900 dark:text-white text-lg leading-tight">UKM Taekwondo</h1>
                <p class="text-xs text-gray-400 dark:text-gray-400 font-medium mt-0.5">{{ $subtitle }}</p>
            </div>
        </div>
        @if ($drawer)
            <button type="button" onclick="toggleMenu()" class="md:hidden text-gray-400 hover:text-red-500 dark:hover:text-red-400 transition-colors p-1 rounded-full hover:bg-red-50 dark:hover:bg-gray-700">
                <span class="material-symbols-outlined">close</span>
            </button>
        @endif
    </div>

    <!-- Navigation Links -->
    <nav class="flex-1 space-y-2 overflow-y-auto">
        @foreach ($items as $item)
            @if (request()->routeIs($item['route']))
                <a href="{{ route($item['route']) }}" class="flex items-center gap-3 px-4 py-3.5 bg-brand-blue text-white rounded-2xl font-semibold shadow-md shadow-brand-blue/20 transition-transform hover:scale-[1.02]">
                    <span class="material-symbols-outlined text-[20px]">{{ $item['icon'] }}</span>
                    <span class="text-sm">{{ $item['label'] }}</span>
                </a>
            @else
                <a href="{{ route($item['route']) }}" class="flex items-center gap-3 px-4 py-3.5 text-gray-500 dark:text-gray-400 hover:text-brand-blue dark:hover:text-white hover:bg-brand-light dark:hover:bg-gray-700 transition-colors rounded-2xl font-medium">
                    <span class="material-symbols-outlined text-[20px]">{{ $item['icon'] }}</span>
                    <span class="text-sm">{{ $item['label'] }}</span>
                </a>
            @endif
        @endforeach
    </nav>

    <div class="mt-auto pt-6 space-y-2 border-t border-gray-100 dark:border-gray-700">
        <button type="button" onclick="toggleDarkMode()" class="w-full flex items-center gap-3 px-4 py-3.5 text-gray-500 dark:text-gray-400 hover:text-brand-blue dark:hover:text-white hover:bg-brand-light dark:hover:bg-gray-700 transition-colors rounded-2xl font-medium focus:outline-none">
            <span class="material-symbols-outlined text-[20px]" id="darkModeIcon">dark_mode</span>
            <span class="text-sm" id="darkModeText">Dark Mode</span>
        </button>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-4 py-3.5 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors rounded-2xl font-medium focus:outline-none">
                <span class="material-symbols-outlined text-[20px]">logout</span>
                <span class="text-sm">Logout</span>
            </button>
        </form>
    </div>
</aside>
