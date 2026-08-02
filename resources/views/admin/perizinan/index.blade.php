<x-app-layout title="Perizinan - Sistem Absensi UKM Taekwondo" mobile-header-title="Perizinan">

    <x-page-header title="Manajemen Perizinan" subtitle="Validasi surat sakit dan persetujuan izin anggota" />

    <!-- Info Banner -->
    <div class="bg-blue-50/80 dark:bg-brand-blue/10 rounded-2xl p-4 md:p-5 flex flex-col sm:flex-row gap-4 items-start border border-blue-100 dark:border-brand-blue/20 shadow-sm relative overflow-hidden">
        <div class="absolute -right-10 -top-10 w-32 h-32 bg-blue-200/40 dark:bg-brand-blue/20 rounded-full blur-2xl"></div>
        <div class="w-10 h-10 rounded-full bg-white dark:bg-brand-blue/20 flex items-center justify-center text-brand-blue dark:text-brand-light shrink-0 shadow-sm z-10">
            <span class="material-symbols-outlined text-[22px]">auto_awesome</span>
        </div>
        <div class="z-10">
            <h4 class="text-brand-blue dark:text-brand-light font-bold text-sm">Otomatisasi Sistem Presensi</h4>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1 leading-relaxed font-medium">
                Pengajuan yang <span class="font-bold text-gray-900 dark:text-white">disetujui</span> akan secara otomatis mengubah status absensi anggota menjadi Izin/Sakit pada tanggal jadwal terkait.
                <br class="hidden sm:block" />Batas maksimal pengajuan oleh anggota adalah <span class="font-bold text-gray-900 dark:text-white">2 jam sebelum jam absen dimulai</span>.
            </p>
        </div>
    </div>

    <livewire:kelola-perizinan />

</x-app-layout>
