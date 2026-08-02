<x-app-layout title="Dashboard Admin - Sistem Absensi UKM Taekwondo" mobile-header-title="Admin Dashboard">

    <x-page-header title="Overview Internal" subtitle="Ringkasan statistik data real-time Taekwondo UAD">
        <a href="{{ route('rekap.export', ['start' => now()->startOfMonth()->toDateString(), 'end' => now()->toDateString()]) }}" class="bg-brand-blue text-white px-6 py-3 md:py-2.5 w-full sm:w-auto rounded-xl md:rounded-full font-semibold text-sm flex justify-center items-center gap-2 hover:bg-brand-hover transition-colors shadow-lg shadow-brand-blue/30">
            <span class="material-symbols-outlined text-[20px]">download</span>
            Export Laporan
        </a>
    </x-page-header>

    <livewire:dashboard-stats />

</x-app-layout>
