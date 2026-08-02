<x-app-layout title="Hari Libur - Sistem Absensi UKM Taekwondo" mobile-header-title="Hari Libur">

    <x-page-header title="Hari Libur Latihan" subtitle="Atur tanggal pengecualian di mana absensi tidak akan dibuka">
        <button type="button" onclick="Livewire.dispatch('tambahLibur')" class="flex items-center justify-center gap-2 px-6 py-3 md:py-2.5 bg-brand-blue text-white rounded-xl md:rounded-full font-semibold text-sm hover:bg-brand-hover shadow-lg shadow-brand-blue/30 transition-all w-full sm:w-auto">
            <span class="material-symbols-outlined text-[20px]">add</span>
            Tambah Libur
        </button>
    </x-page-header>

    <livewire:kelola-hari-libur />

</x-app-layout>
