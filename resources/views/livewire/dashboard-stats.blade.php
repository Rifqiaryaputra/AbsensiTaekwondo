<div wire:poll.10s="refresh" class="space-y-6 md:space-y-8">
    <!-- Row 1: KPI Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6">
        @foreach ($kpis as $kpi)
            <x-kpi-card :icon="$kpi['icon']" :label="$kpi['label']" :value="$kpi['value']" :icon-bg="$kpi['bg']" :icon-text="$kpi['text']" />
        @endforeach
    </div>

    <!-- Row 2: Charts & Summaries -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left: Demografi -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-3xl p-6 md:p-8 shadow-card flex flex-col transition-colors duration-300">
            <div class="flex items-center justify-between w-full mb-8">
                <h3 class="font-heading font-bold text-lg text-gray-900 dark:text-white">Statistik Jenis Kelamin</h3>
                <span class="bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-300 text-xs font-semibold px-3 py-1 rounded-full">Gender</span>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-8 md:gap-12 flex-1 pb-4 sm:pb-0">
                <!-- Donut Chart (CSS crafted) -->
                <div class="relative w-48 h-48 md:w-52 md:h-52 rounded-full border-[18px] border-brand-light dark:border-gray-700" style="background: conic-gradient(#3554d1 0% {{ $gender['laki']['persen'] }}%, #cbd5e1 {{ $gender['laki']['persen'] }}% 100%);">
                    <div class="absolute inset-2 bg-white dark:bg-gray-800 rounded-full flex flex-col items-center justify-center shadow-inner transition-colors duration-300">
                        <span class="font-heading font-bold text-3xl text-gray-900 dark:text-white">{{ $gender['total'] }}</span>
                        <span class="text-sm font-medium text-gray-400 dark:text-gray-500">Total</span>
                    </div>
                </div>

                <!-- Legend -->
                <div class="flex flex-col gap-4 md:gap-6 w-full sm:w-auto">
                    <div class="flex items-center gap-4 bg-gray-50 dark:bg-gray-700/50 p-4 rounded-2xl w-full transition-colors">
                        <div class="w-12 h-12 rounded-full bg-brand-blue/10 dark:bg-brand-blue/20 flex items-center justify-center text-brand-blue shrink-0">
                            <span class="material-symbols-outlined">male</span>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 dark:text-white">Laki-laki</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">{{ $gender['laki']['persen'] }}% ({{ $gender['laki']['jumlah'] }} org)</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 bg-gray-50 dark:bg-gray-700/50 p-4 rounded-2xl w-full transition-colors">
                        <div class="w-12 h-12 rounded-full bg-slate-200/50 dark:bg-slate-600/50 flex items-center justify-center text-slate-500 dark:text-slate-300 shrink-0">
                            <span class="material-symbols-outlined">female</span>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 dark:text-white">Perempuan</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">{{ $gender['perempuan']['persen'] }}% ({{ $gender['perempuan']['jumlah'] }} org)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Today's Attendance Summary -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 md:p-8 shadow-card flex flex-col transition-colors duration-300">
            <div class="flex items-center justify-between mb-8">
                <h3 class="font-heading font-bold text-lg text-gray-900 dark:text-white">Kehadiran Hari Ini</h3>
                <span class="text-xs font-semibold text-gray-400 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 px-3 py-1 rounded-full">{{ now()->translatedFormat('M d, Y') }}</span>
            </div>

            <div class="flex-1 grid grid-cols-2 gap-3 md:gap-4">
                <div class="bg-status-hadir-bg dark:bg-green-900/20 rounded-2xl p-4 md:p-5 flex flex-col items-center justify-center text-center gap-1 transition-transform hover:scale-105">
                    <span class="font-heading font-extrabold text-3xl md:text-4xl text-status-hadir-text dark:text-green-400">{{ $kehadiranHariIni['hadir'] }}</span>
                    <div>
                        <p class="text-sm font-bold text-gray-600 dark:text-gray-300 mt-1">Hadir</p>
                        <p class="text-[10px] text-gray-500 dark:text-gray-400 hidden sm:block">Tepat Waktu</p>
                    </div>
                </div>

                <div class="bg-status-izin-bg dark:bg-blue-900/20 rounded-2xl p-4 md:p-5 flex flex-col items-center justify-center text-center gap-1 transition-transform hover:scale-105">
                    <span class="font-heading font-extrabold text-3xl md:text-4xl text-status-izin-text dark:text-blue-400">{{ $kehadiranHariIni['izin'] }}</span>
                    <div>
                        <p class="text-sm font-bold text-gray-600 dark:text-gray-300 mt-1">Izin</p>
                        <p class="text-[10px] text-gray-500 dark:text-gray-400 hidden sm:block">Dikonfirmasi</p>
                    </div>
                </div>

                <div class="bg-status-sakit-bg dark:bg-yellow-900/20 rounded-2xl p-4 md:p-5 flex flex-col items-center justify-center text-center gap-1 transition-transform hover:scale-105">
                    <span class="font-heading font-extrabold text-3xl md:text-4xl text-status-sakit-text dark:text-yellow-400">{{ $kehadiranHariIni['sakit'] }}</span>
                    <div>
                        <p class="text-sm font-bold text-gray-600 dark:text-gray-300 mt-1">Sakit</p>
                        <p class="text-[10px] text-gray-500 dark:text-gray-400 hidden sm:block">Dengan Surat</p>
                    </div>
                </div>

                <div class="bg-status-alfa-bg dark:bg-red-900/20 rounded-2xl p-4 md:p-5 flex flex-col items-center justify-center text-center gap-1 transition-transform hover:scale-105">
                    <span class="font-heading font-extrabold text-3xl md:text-4xl text-status-alfa-text dark:text-red-400">{{ $kehadiranHariIni['alfa'] }}</span>
                    <div>
                        <p class="text-sm font-bold text-gray-600 dark:text-gray-300 mt-1">Alfa</p>
                        <p class="text-[10px] text-gray-500 dark:text-gray-400 hidden sm:block">Tanpa Keterangan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 3: Lists -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Left: Most Active Members -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 md:p-8 shadow-card transition-colors duration-300">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-heading font-bold text-lg text-gray-900 dark:text-white">Anggota Paling Aktif</h3>
                <button type="button" onclick="showToast('Info', 'Navigasi detail tersedia pada fase backend.', 'info')" class="text-brand-blue dark:text-brand-blue text-sm font-semibold hover:underline bg-brand-light dark:bg-gray-700 px-4 py-1.5 rounded-full transition-colors">View All</button>
            </div>
            <div class="space-y-4">
                @foreach ($anggotaAktif as $anggota)
                    <div wire:key="aktif-{{ $anggota['nim'] }}" class="flex flex-row items-center justify-between p-3 md:p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors rounded-2xl border border-transparent hover:border-gray-100 dark:hover:border-gray-600 group gap-2">
                        <div class="flex items-center gap-3 md:gap-4">
                            <div class="w-10 h-10 md:w-12 md:h-12 rounded-full bg-brand-light dark:bg-brand-blue/20 flex items-center justify-center text-brand-blue font-heading font-bold text-base md:text-lg group-hover:scale-110 transition-transform shrink-0">{{ $anggota['inisial'] }}</div>
                            <div>
                                <p class="font-bold text-gray-900 dark:text-white text-sm md:text-base">{{ $anggota['nama'] }}</p>
                                <p class="text-xs text-gray-400 font-medium mt-0.5">{{ $anggota['nim'] }}</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1.5 bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 px-2.5 py-1.5 md:px-3 md:py-1.5 rounded-full text-[10px] md:text-xs font-bold whitespace-nowrap">
                            <span class="material-symbols-outlined text-[14px]">local_fire_department</span>
                            <span class="hidden sm:inline">{{ $anggota['kehadiran'] }} Kehadiran</span>
                            <span class="sm:hidden">{{ $anggota['kehadiran'] }}x</span>
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Right: Frequent Alfa -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 md:p-8 shadow-card transition-colors duration-300">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-heading font-bold text-lg text-gray-900 dark:text-white">Paling Sering Alfa</h3>
                <button type="button" onclick="showToast('Info', 'Navigasi detail tersedia pada fase backend.', 'info')" class="text-brand-blue dark:text-brand-blue text-sm font-semibold hover:underline bg-brand-light dark:bg-gray-700 px-4 py-1.5 rounded-full transition-colors">View All</button>
            </div>
            <div class="space-y-4">
                @foreach ($seringAlfa as $anggota)
                    <div wire:key="alfa-{{ $anggota['nim'] }}" class="flex flex-row items-center justify-between p-3 md:p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors rounded-2xl border border-transparent hover:border-gray-100 dark:hover:border-gray-600 group gap-2">
                        <div class="flex items-center gap-3 md:gap-4">
                            <div class="w-10 h-10 md:w-12 md:h-12 rounded-full bg-red-50 dark:bg-red-900/30 flex items-center justify-center text-red-500 dark:text-red-400 font-heading font-bold text-base md:text-lg group-hover:scale-110 transition-transform shrink-0">{{ $anggota['inisial'] }}</div>
                            <div>
                                <p class="font-bold text-gray-900 dark:text-white text-sm md:text-base">{{ $anggota['nama'] }}</p>
                                <p class="text-xs text-gray-400 font-medium mt-0.5">{{ $anggota['nim'] }}</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1.5 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 px-2.5 py-1.5 md:px-3 md:py-1.5 rounded-full text-[10px] md:text-xs font-bold whitespace-nowrap">
                            <span class="material-symbols-outlined text-[14px]">warning</span>
                            {{ $anggota['alfa'] }} Alfa
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
