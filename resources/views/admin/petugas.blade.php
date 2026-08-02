<x-app-layout title="Manajemen Petugas - Sistem Absensi UKM Taekwondo" mobile-header-title="Manajemen Petugas">

    <x-page-header title="Manajemen Petugas" subtitle="Kelola akun login untuk anggota yang bertugas melakukan scan absensi">
        <button type="button" onclick="Livewire.dispatch('tambahPetugas')" class="flex items-center justify-center gap-2 px-6 py-3 md:py-2.5 bg-brand-blue text-white rounded-xl md:rounded-full font-semibold text-sm hover:bg-brand-hover shadow-lg shadow-brand-blue/30 transition-all w-full sm:w-auto">
            <span class="material-symbols-outlined text-[20px]">add</span>
            Tambah Petugas
        </button>
    </x-page-header>

    <livewire:kelola-petugas />

</x-app-layout>
