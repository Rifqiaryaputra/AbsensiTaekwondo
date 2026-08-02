<x-app-layout title="Database Anggota - UKM Taekwondo" mobile-header-title="Database Anggota">

    <x-page-header title="Database Anggota" subtitle="Kelola dan pantau data internal anggota UKM">
        <button type="button" onclick="Livewire.dispatch('exportAnggota')" class="flex items-center justify-center gap-2 px-5 py-3 md:py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 rounded-xl md:rounded-full font-semibold text-sm hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-brand-blue transition-colors shadow-sm w-full sm:w-auto">
            <span class="material-symbols-outlined text-[20px]">download</span>
            Export Excel
        </button>
        <button type="button" onclick="Livewire.dispatch('importAnggota')" class="flex items-center justify-center gap-2 px-5 py-3 md:py-2.5 bg-brand-light dark:bg-brand-blue/20 text-brand-blue dark:text-brand-light rounded-xl md:rounded-full font-semibold text-sm hover:bg-blue-100 dark:hover:bg-brand-blue/30 transition-colors w-full sm:w-auto">
            <span class="material-symbols-outlined text-[20px]">file_upload</span>
            Import Data
        </button>
        <button type="button" onclick="Livewire.dispatch('tambahAnggota')" class="flex items-center justify-center gap-2 px-6 py-3 md:py-2.5 bg-brand-blue text-white rounded-xl md:rounded-full font-semibold text-sm hover:bg-brand-hover shadow-lg shadow-brand-blue/30 transition-colors w-full sm:w-auto">
            <span class="material-symbols-outlined text-[20px]">add</span>
            Tambah Anggota
        </button>
    </x-page-header>

    <livewire:daftar-anggota />

</x-app-layout>
