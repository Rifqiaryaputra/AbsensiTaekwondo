<x-app-layout title="Settings - Sistem Absensi UKM Taekwondo" mobile-header-title="Settings">

    <x-page-header title="Pengaturan Profil" subtitle="Kelola identitas organisasi dan universitas untuk keperluan cetak laporan.">
        <button type="button" onclick="saveProfil()" class="shrink-0 flex items-center justify-center gap-2 px-6 py-3 min-h-[48px] bg-brand-blue text-white rounded-xl md:rounded-full font-semibold text-sm hover:bg-brand-hover shadow-lg shadow-brand-blue/30 transition-colors w-full md:w-auto">
            <span class="material-symbols-outlined text-[20px]">save</span>
            Simpan Perubahan
        </button>
    </x-page-header>

    <!-- Settings Card -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-50 dark:border-gray-700 p-6 md:p-8 shadow-card w-full transition-colors duration-300 max-w-[1024px]">

        <!-- Logo Upload Section -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 md:gap-6 mb-8">
            <div>
                <label class="block text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Logo Unit Kegiatan (Kiri)</label>
                <div class="border-2 border-dashed border-gray-200 dark:border-gray-600 rounded-2xl bg-gray-50/50 dark:bg-gray-700/30 hover:bg-brand-light/50 dark:hover:bg-gray-700/60 p-6 flex flex-col items-center justify-center cursor-pointer hover:border-brand-blue/30 dark:hover:border-brand-blue/50 transition-colors group min-h-[140px]">
                    <span class="material-symbols-outlined text-gray-300 dark:text-gray-500 group-hover:text-brand-blue dark:group-hover:text-brand-light mb-2 text-3xl transition-colors">cloud_upload</span>
                    <p class="font-semibold text-sm text-gray-600 dark:text-gray-300 group-hover:text-brand-blue dark:group-hover:text-brand-light transition-colors">Klik untuk upload</p>
                    <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1 font-medium">PNG, JPG up to 2MB</p>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Logo Universitas (Kanan)</label>
                <div class="border-2 border-dashed border-gray-200 dark:border-gray-600 rounded-2xl bg-gray-50/50 dark:bg-gray-700/30 hover:bg-brand-light/50 dark:hover:bg-gray-700/60 p-6 flex flex-col items-center justify-center cursor-pointer hover:border-brand-blue/30 dark:hover:border-brand-blue/50 transition-colors group min-h-[140px]">
                    <span class="material-symbols-outlined text-gray-300 dark:text-gray-500 group-hover:text-brand-blue dark:group-hover:text-brand-light mb-2 text-3xl transition-colors">cloud_upload</span>
                    <p class="font-semibold text-sm text-gray-600 dark:text-gray-300 group-hover:text-brand-blue dark:group-hover:text-brand-light transition-colors">Klik untuk upload</p>
                    <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1 font-medium">PNG, JPG up to 2MB</p>
                </div>
            </div>
        </div>

        <!-- Divider -->
        <div class="h-px w-full bg-gray-100 dark:bg-gray-700 mb-8 transition-colors duration-300"></div>

        <!-- Form Fields Section -->
        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 md:gap-6">
                <div class="space-y-2">
                    <label class="block text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider" for="nama_unit">Nama Unit Kegiatan</label>
                    <input class="w-full px-4 py-3 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-2xl font-medium text-sm text-gray-800 dark:text-white focus:outline-none focus:border-brand-blue focus:ring-4 focus:ring-brand-light dark:focus:ring-brand-blue/20 transition-all min-h-[48px]" id="nama_unit" type="text" value="{{ $profil['nama_unit'] }}">
                </div>
                <div class="space-y-2">
                    <label class="block text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider" for="nama_univ">Nama Universitas</label>
                    <input class="w-full px-4 py-3 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-2xl font-medium text-sm text-gray-800 dark:text-white focus:outline-none focus:border-brand-blue focus:ring-4 focus:ring-brand-light dark:focus:ring-brand-blue/20 transition-all min-h-[48px]" id="nama_univ" type="text" value="{{ $profil['nama_universitas'] }}">
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider" for="alamat">Alamat / Sekretariat</label>
                <textarea class="w-full px-4 py-3 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-2xl font-medium text-sm text-gray-800 dark:text-white focus:outline-none focus:border-brand-blue focus:ring-4 focus:ring-brand-light dark:focus:ring-brand-blue/20 transition-all resize-y" id="alamat" rows="3">{{ $profil['alamat'] }}</textarea>
                <div class="flex items-center gap-1.5 mt-1.5 text-brand-blue dark:text-brand-light">
                    <span class="material-symbols-outlined text-[14px]">info</span>
                    <p class="text-[11px] font-semibold">Alamat ini akan ditampilkan pada kop surat laporan absensi.</p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // TODO Fase 4: penyimpanan dihubungkan ke tabel pengaturan_profil.
            window.saveProfil = function () {
                showToast('Berhasil', 'Pengaturan profil berhasil disimpan.', 'success');
            };
        </script>
    @endpush

</x-app-layout>
