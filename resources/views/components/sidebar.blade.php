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
        $items[] = ['route' => 'anggota.izin', 'icon' => 'fact_check', 'label' => 'Riwayat Izin/Sakit'];
        $items[] = ['route' => 'anggota.pengaturan', 'icon' => 'settings', 'label' => 'Pengaturan'];
        $subtitle = 'Sistem Anggota';
    }
@endphp

<aside id="sidebar"
    x-data="{ showLogoutModal: false }"
    class="sidebar-menu fixed left-0 top-0 flex h-screen flex-col overflow-hidden supports-[height:100dvh]:h-[100dvh] w-[280px] bg-white dark:bg-gray-800 p-6 z-50 shadow-[1px_0_15px_rgba(0,0,0,0.03)] dark:shadow-none border-r border-gray-100 dark:border-gray-700 md:translate-x-0 flex hidden md:flex transition-colors duration-300">

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
    </div>

    <!-- Navigation Links -->
    <nav class="flex-1 min-h-0 space-y-2 overflow-y-auto overscroll-contain">
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

    <div class="mt-auto pt-6 pb-6 space-y-2 border-t border-gray-100 dark:border-gray-700">
        <button type="button" onclick="toggleDarkMode()" class="w-full flex items-center gap-3 px-4 py-3.5 text-gray-500 dark:text-gray-400 hover:text-brand-blue dark:hover:text-white hover:bg-brand-light dark:hover:bg-gray-700 transition-colors rounded-2xl font-medium focus:outline-none">
            <span class="material-symbols-outlined text-[20px]" id="darkModeIcon">dark_mode</span>
            <span class="text-sm" id="darkModeText">Dark Mode</span>
        </button>
        <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
            @csrf
        </form>
        <button type="button" x-on:click.prevent="showLogoutModal = true" class="w-full flex items-center gap-3 px-4 py-3.5 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors rounded-2xl font-medium focus:outline-none">
            <span class="material-symbols-outlined text-[20px]">logout</span>
            <span class="text-sm">Logout</span>
        </button>
    </div>

    <!-- Konfirmasi Logout -->
    <template x-teleport="body">
        <div x-cloak x-show="showLogoutModal" x-transition.opacity.scale.origin.center.duration.200ms
            class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center z-[70]">
            <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 md:p-8 max-w-sm w-full mx-4 text-center shadow-xl">
            <div class="bg-red-50 dark:bg-red-900/20 text-red-500 dark:text-red-400 w-16 h-16 rounded-full mx-auto flex items-center justify-center mb-4">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Konfirmasi Keluar?</h3>
            <p class="text-gray-500 dark:text-gray-400 text-sm mb-8">Apakah Anda yakin ingin keluar dari sistem? Anda harus login kembali untuk masuk.</p>
            <div class="flex items-center gap-3 w-full">
                <button type="button" x-on:click="showLogoutModal = false" class="flex-1 bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-semibold py-3 px-4 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-600 transition duration-200 focus:outline-none">Batal</button>
                <button type="button" x-on:click="document.getElementById('logout-form').submit();" class="flex-1 bg-red-500 text-white font-semibold py-3 px-4 rounded-xl hover:bg-red-600 transition duration-200 focus:outline-none">Ya, Keluar</button>
            </div>
            </div>
        </div>
    </template>
</aside>
