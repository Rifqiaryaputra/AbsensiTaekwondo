@php
    $dashboardActive = request()->routeIs('anggota.dashboard');
    $pengaturanActive = request()->routeIs('anggota.pengaturan');
@endphp

<div class="md:hidden fixed bottom-0 left-0 w-full bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700 pb-safe z-40 shadow-[0_-10px_40px_rgba(0,0,0,0.05)] rounded-t-3xl transition-colors duration-300">
    <div class="flex justify-around items-center h-16 max-w-lg mx-auto px-2">
        <a href="{{ route('anggota.dashboard') }}" class="flex flex-col items-center justify-center w-full h-full {{ $dashboardActive ? 'text-brand-blue dark:text-brand-light' : 'text-gray-400 hover:text-brand-blue dark:hover:text-brand-light' }} transition-all pt-1">
            <div class="{{ $dashboardActive ? 'bg-brand-light dark:bg-brand-blue/20' : '' }} px-4 py-1.5 rounded-xl mb-0.5 transition-colors">
                <span class="material-symbols-outlined text-[24px]">dashboard</span>
            </div>
            <span class="text-[10px] {{ $dashboardActive ? 'font-bold' : 'font-semibold' }}">Dashboard</span>
        </a>

        <a href="{{ route('anggota.pengaturan') }}" class="flex flex-col items-center justify-center w-full h-full {{ $pengaturanActive ? 'text-brand-blue dark:text-brand-light' : 'text-gray-400 hover:text-brand-blue dark:hover:text-brand-light' }} transition-all pt-1">
            <div class="{{ $pengaturanActive ? 'bg-brand-light dark:bg-brand-blue/20' : '' }} px-4 py-1.5 rounded-xl mb-0.5 transition-colors">
                <span class="material-symbols-outlined text-[24px]">settings</span>
            </div>
            <span class="text-[10px] {{ $pengaturanActive ? 'font-bold' : 'font-semibold' }}">Pengaturan</span>
        </a>
    </div>
</div>
