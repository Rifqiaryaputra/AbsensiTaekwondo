@php
    $dashboardActive = request()->routeIs('anggota.dashboard');
    $izinActive = request()->routeIs('anggota.izin');
    $pengaturanActive = request()->routeIs('anggota.pengaturan');
@endphp

<div class="md:hidden fixed bottom-0 left-0 right-0 w-full bg-white/90 dark:bg-gray-800/90 backdrop-blur-lg border-t border-gray-100 dark:border-gray-700 pb-safe z-50 shadow-[0_-10px_40px_rgba(0,0,0,0.08)] rounded-t-3xl transition-colors duration-300">
    <div class="flex justify-around items-center h-16 max-w-lg mx-auto px-2">

        <a href="{{ route('anggota.dashboard') }}" class="flex flex-col items-center justify-center w-full h-full gap-0.5 group transition-all pt-1 {{ $dashboardActive ? 'text-brand-blue dark:text-brand-light' : 'text-gray-400 hover:text-brand-blue dark:hover:text-brand-light' }}">
            <div class="px-5 py-1.5 rounded-full mb-0.5 transition-all {{ $dashboardActive ? 'bg-brand-light dark:bg-brand-blue/20' : 'group-hover:bg-gray-100 dark:group-hover:bg-gray-700' }}">
                <span class="material-symbols-outlined text-[24px]">grid_view</span>
            </div>
            <span class="text-[10px] {{ $dashboardActive ? 'font-bold' : 'font-semibold' }}">Dashboard</span>
        </a>

        <a href="{{ route('anggota.izin') }}" class="flex flex-col items-center justify-center w-full h-full gap-0.5 group transition-all pt-1 {{ $izinActive ? 'text-brand-blue dark:text-brand-light' : 'text-gray-400 hover:text-brand-blue dark:hover:text-brand-light' }}">
            <div class="px-5 py-1.5 rounded-full mb-0.5 transition-all {{ $izinActive ? 'bg-brand-light dark:bg-brand-blue/20' : 'group-hover:bg-gray-100 dark:group-hover:bg-gray-700' }}">
                <span class="material-symbols-outlined text-[24px]">fact_check</span>
            </div>
            <span class="text-[10px] {{ $izinActive ? 'font-bold' : 'font-semibold' }}">Izin/Sakit</span>
        </a>

        <a href="{{ route('anggota.pengaturan') }}" class="flex flex-col items-center justify-center w-full h-full gap-0.5 group transition-all pt-1 {{ $pengaturanActive ? 'text-brand-blue dark:text-brand-light' : 'text-gray-400 hover:text-brand-blue dark:hover:text-brand-light' }}">
            <div class="px-5 py-1.5 rounded-full mb-0.5 transition-all {{ $pengaturanActive ? 'bg-brand-light dark:bg-brand-blue/20' : 'group-hover:bg-gray-100 dark:group-hover:bg-gray-700' }}">
                <span class="material-symbols-outlined text-[24px]">settings</span>
            </div>
            <span class="text-[10px] {{ $pengaturanActive ? 'font-bold' : 'font-semibold' }}">Pengaturan</span>
        </a>

    </div>
</div>