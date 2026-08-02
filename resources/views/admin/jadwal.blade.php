<x-app-layout title="Jadwal Latihan - Sistem Absensi UKM Taekwondo" mobile-header-title="Jadwal">

    <x-page-header title="Jadwal Latihan" subtitle="Atur hari, jam, dan petugas absensi untuk latihan rutin.">
        <button type="button" onclick="Livewire.dispatch('tambahJadwal')" class="bg-brand-blue hover:bg-brand-hover text-white px-5 py-2.5 rounded-xl font-semibold shadow-lg shadow-brand-blue/30 transition-all flex items-center justify-center gap-2 hover:scale-[1.02] w-full sm:w-auto">
            <span class="material-symbols-outlined text-[20px]">add</span>
            <span>Tambah Jadwal</span>
        </button>
    </x-page-header>

    <livewire:kelola-jadwal />

</x-app-layout>
