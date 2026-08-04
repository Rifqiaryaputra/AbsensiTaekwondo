<x-app-layout title="Dashboard Anggota - Sistem Absensi UKM Taekwondo" mobile-header-title="Dashboard Anggota" mobile="static">

    <div class="max-w-[1024px] mx-auto space-y-6 md:space-y-8 animate-[fadeIn_0.3s_ease-in-out]" x-data="anggotaApp()">

        <!-- Area Kartu ID -->
        <div id="area-kartu-id" class="bg-white dark:bg-gray-800 rounded-3xl shadow-card border border-gray-50 dark:border-gray-700 overflow-hidden relative group transition-colors duration-300">
            <div class="absolute top-0 right-0 p-4 z-20 hidden md:block">
                <button type="button" onclick="showCustomMessage('Unduh kartu tersedia pada fase backend.', 'info')" class="bg-white dark:bg-gray-700 border border-gray-100 dark:border-gray-600 text-gray-500 dark:text-gray-300 hover:text-brand-blue dark:hover:text-brand-light shadow-sm hover:shadow-md px-4 py-2 rounded-xl text-sm font-semibold flex items-center gap-2 transition-all">
                    <span class="material-symbols-outlined text-[18px]">download</span> Unduh Kartu
                </button>
            </div>

            <div class="p-6 md:p-8 relative flex flex-col md:flex-row items-center md:items-start gap-6 md:gap-8">
                <!-- Foto & Status -->
                <div class="shrink-0 flex flex-col items-center">
                    <div class="w-28 h-28 sm:w-32 sm:h-32 rounded-full border-4 border-brand-light dark:border-brand-blue/30 shadow-md overflow-hidden bg-brand-blue/10 dark:bg-gray-700 relative z-10 flex items-center justify-center transition-colors">
                        @if ($anggota['foto'])
                            <img src="{{ asset($anggota['foto']) }}" alt="Foto {{ $anggota['nama'] }}" class="w-full h-full object-cover">
                        @else
                            <span class="font-heading font-extrabold text-4xl text-brand-blue dark:text-brand-light">{{ $anggota['inisial'] }}</span>
                        @endif
                    </div>
                    <div class="mt-[-12px] relative z-20 bg-status-hadir-bg dark:bg-green-900/40 text-status-hadir-text dark:text-green-400 border border-green-100 dark:border-green-800 px-3 py-1 rounded-full text-[10px] sm:text-xs font-bold flex items-center gap-1.5 shadow-sm transition-colors">
                        <span class="w-1.5 h-1.5 rounded-full bg-status-hadir-text dark:bg-green-400 animate-pulse"></span> Anggota Aktif
                    </div>
                </div>

                <!-- Identitas -->
                <div class="flex-1 text-center md:text-left mt-2 md:mt-4">
                    <div class="inline-flex items-center gap-1.5 bg-blue-50 dark:bg-blue-900/30 text-brand-blue dark:text-blue-400 px-3 py-1 rounded-full text-[10px] sm:text-xs font-bold tracking-wide mb-3 border border-blue-100 dark:border-blue-800 uppercase transition-colors">
                        <span class="material-symbols-outlined text-[14px]">badge</span> ID Anggota
                    </div>
                    <h2 class="font-heading text-2xl sm:text-3xl lg:text-4xl font-extrabold text-gray-900 dark:text-white tracking-tight leading-none transition-colors">{{ $anggota['nama'] }}</h2>
                    <div class="flex flex-col md:flex-row gap-1 md:gap-3 text-sm font-medium text-gray-500 dark:text-gray-400 mt-3 md:items-center transition-colors">
                        <span>ID Anggota: <strong class="text-gray-700 dark:text-gray-300">{{ $anggota['id_anggota'] }}</strong></span>
                        <span class="hidden md:block">•</span>
                        <span>BPJSTK: <strong class="text-gray-700 dark:text-gray-300">{{ $anggota['no_bpjs'] }}</strong></span>
                    </div>
                </div>

                <!-- QR Code -->
                <div class="shrink-0 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-3xl p-4 sm:p-5 flex flex-col items-center justify-center shadow-inner mt-4 md:mt-0 w-full md:w-auto transition-colors">
                    @if ($anggota['qr_code'])
                        <img src="{{ asset($anggota['qr_code']) }}" alt="QR {{ $anggota['id_anggota'] }}" class="w-24 h-24 sm:w-28 sm:h-28 rounded-xl">
                    @else
                        <span class="material-symbols-outlined text-6xl sm:text-7xl text-gray-800 dark:text-white">qr_code_2</span>
                    @endif
                    <span class="text-[10px] font-bold text-gray-400 mt-2 tracking-widest uppercase">Scan Absensi</span>
                </div>
            </div>
        </div>

        <!-- Mobile Download Button -->
        <button type="button" onclick="showCustomMessage('Unduh kartu tersedia pada fase backend.', 'info')" class="md:hidden w-full bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 text-gray-600 dark:text-gray-300 shadow-sm hover:shadow-md px-4 py-3 rounded-2xl text-sm font-semibold flex items-center justify-center gap-2 transition-all">
            <span class="material-symbols-outlined text-[18px]">download</span> Simpan Kartu ke Galeri
        </button>

        <!-- Dashboard Grid Area -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-8">

            <!-- Kiri: KPI Presensi -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-card border border-gray-50 dark:border-gray-700 p-6 transition-colors duration-300">
                <div class="mb-5 flex justify-between items-center">
                    <div>
                        <h2 class="font-heading font-bold text-lg text-gray-900 dark:text-white">Statistik Kehadiran</h2>
                        {{-- <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-0.5">Semester Ganjil 2026</p> --}}
                    </div>
                    <span class="bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-xs font-bold px-3 py-1 rounded-full transition-colors">{{ $statistik['total_sesi'] }} Sesi</span>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-status-hadir-bg dark:bg-green-900/20 rounded-2xl p-4 flex flex-col items-center justify-center transition-transform hover:scale-105 border border-transparent dark:border-green-900/30">
                        <h3 class="font-heading text-2xl md:text-3xl font-extrabold text-status-hadir-text dark:text-green-400">{{ $statistik['hadir'] }}</h3>
                        <p class="text-[11px] font-bold text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wide">Hadir</p>
                    </div>
                    <div class="bg-status-izin-bg dark:bg-blue-900/20 rounded-2xl p-4 flex flex-col items-center justify-center transition-transform hover:scale-105 border border-transparent dark:border-blue-900/30">
                        <h3 class="font-heading text-2xl md:text-3xl font-extrabold text-status-izin-text dark:text-blue-400">{{ $statistik['izin'] }}</h3>
                        <p class="text-[11px] font-bold text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wide">Izin</p>
                    </div>
                    <div class="bg-status-sakit-bg dark:bg-yellow-900/20 rounded-2xl p-4 flex flex-col items-center justify-center transition-transform hover:scale-105 border border-transparent dark:border-yellow-900/30">
                        <h3 class="font-heading text-2xl md:text-3xl font-extrabold text-status-sakit-text dark:text-yellow-400">{{ $statistik['sakit'] }}</h3>
                        <p class="text-[11px] font-bold text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wide">Sakit</p>
                    </div>
                    <div class="bg-status-alfa-bg dark:bg-red-900/20 rounded-2xl p-4 flex flex-col items-center justify-center transition-transform hover:scale-105 border border-transparent dark:border-red-900/30">
                        <h3 class="font-heading text-2xl md:text-3xl font-extrabold text-status-alfa-text dark:text-red-400">{{ $statistik['alfa'] }}</h3>
                        <p class="text-[11px] font-bold text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wide">Alfa</p>
                    </div>
                </div>
            </div>

            <!-- Kanan: Jadwal & Action -->
            <div class="flex flex-col gap-4">
                <!-- Banner Pemberitahuan Libur -->
                @if ($liburTerdekat)
                    <div class="bg-red-50 dark:bg-red-900/20 rounded-3xl p-5 border border-red-100 dark:border-red-900/50 flex gap-4 items-center shadow-sm relative overflow-hidden animate-[fadeIn_0.3s_ease-in-out] transition-colors">
                        <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/50 flex items-center justify-center text-red-500 dark:text-red-400 shrink-0 transition-colors">
                            <span class="material-symbols-outlined text-[22px]">event_busy</span>
                        </div>
                        <div>
                            <h3 class="font-bold text-red-700 dark:text-red-400 text-sm transition-colors">Pemberitahuan Libur</h3>
                            <p class="text-xs text-red-600/80 dark:text-red-300 font-medium mt-1 transition-colors">Latihan ditiadakan pada <strong class="text-red-700 dark:text-red-400">{{ $liburTerdekat['tanggal'] }}</strong> ({{ $liburTerdekat['keterangan'] }}).</p>
                        </div>
                    </div>
                @endif

                <!-- Banner Jadwal Terdekat -->
                @if ($jadwalTerdekat)
                    <div class="bg-brand-blue dark:bg-brand-hover rounded-3xl p-6 text-white shadow-lg shadow-brand-blue/30 flex items-center gap-5 relative overflow-hidden transition-colors">
                        <div class="absolute -right-10 -top-10 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
                        <div class="w-12 h-12 md:w-14 md:h-14 rounded-2xl border border-white/20 flex items-center justify-center shrink-0 bg-white/10 backdrop-blur-md z-10">
                            <span class="material-symbols-outlined text-[28px]">notifications_active</span>
                        </div>
                        <div class="flex-1 min-w-0 z-10">
                            <h3 class="font-semibold text-blue-100 text-[11px] md:text-xs tracking-wider uppercase mb-0.5">Jadwal Latihan Terdekat</h3>
                            <p class="font-heading font-bold text-lg md:text-xl truncate leading-tight mt-1">{{ $jadwalTerdekat['tanggal'] }}</p>
                            <div class="flex flex-wrap items-center gap-3 mt-2">
                                <span class="text-[11px] bg-white/20 px-2 py-1 rounded-md font-medium flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">schedule</span> {{ $jadwalTerdekat['jam'] }}</span>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Pengajuan Izin / Sakit -->
                <livewire:pengajuan-izin />
            </div>
        </div>

        <!-- Tabel Riwayat Singkat -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-card border border-gray-50 dark:border-gray-700 p-6 md:p-8 w-full mt-4 flex flex-col transition-colors duration-300">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4">
                <div>
                    <h2 class="font-heading text-lg md:text-xl font-bold text-gray-900 dark:text-white tracking-tight transition-colors">Riwayat Presensi</h2>
                    {{-- <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-1 transition-colors">10 data terakhir</p> --}}
                </div>

                <div class="relative shrink-0 w-full sm:w-auto min-w-[140px]">
                    <select x-model="bulan" class="appearance-none bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 text-xs sm:text-sm rounded-xl focus:outline-none focus:ring-4 focus:ring-brand-light dark:focus:ring-brand-blue/20 focus:border-brand-blue block w-full pl-4 pr-10 py-2.5 md:py-2 font-medium cursor-pointer transition-all">
                        @forelse ($bulanList as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @empty
                            <option value="">Tidak ada data</option>
                        @endforelse
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-400">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="border border-gray-100 dark:border-gray-700 rounded-2xl overflow-hidden relative shadow-sm transition-colors">
                <div class="overflow-x-auto w-full">
                    <table class="w-full text-left border-collapse min-w-[400px]">
                        <thead>
                            <tr class="bg-gray-50/80 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700 transition-colors">
                                <template x-for="item in filteredRiwayat()" :key="item.date">
                                    <th class="py-3 px-3 md:px-4 text-center min-w-[80px]">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-xs text-gray-600 dark:text-gray-300" x-text="item.day"></span>
                                            <span class="text-[10px] font-medium text-gray-400 dark:text-gray-500 mt-0.5" x-text="item.date"></span>
                                        </div>
                                    </th>
                                </template>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="bg-white dark:bg-gray-800 divide-x divide-gray-50 dark:divide-gray-700 transition-colors">
                                <template x-for="item in filteredRiwayat()" :key="item.date">
                                    <td class="py-4 px-3 md:px-4 text-center transition-colors border-r border-gray-50 dark:border-gray-700 last:border-0">
                                        <div class="mx-auto w-10 h-10 rounded-full flex items-center justify-center" :class="statusClass(item.status)">
                                            <span class="font-heading font-extrabold text-lg" :class="statusText(item.status)" x-text="statusLetter(item.status)"></span>
                                        </div>
                                    </td>
                                </template>
                            </tr>
                            <tr x-show="filteredRiwayat().length === 0">
                                <td class="py-6 text-center text-sm font-medium text-gray-400 dark:text-gray-500">Tidak ada data untuk bulan ini</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function anggotaApp() {
                return {
                    bulan: '{{ $bulanTerpilih }}',
                    riwayat: @json($riwayat),
                    filteredRiwayat() {
                        return this.bulan ? this.riwayat.filter(r => r.bulan === this.bulan) : this.riwayat;
                    },
                    statusLetter(status) {
                        return { hadir: 'H', izin: 'I', sakit: 'S', alfa: 'A' }[status] || '-';
                    },
                    statusClass(status) {
                        return {
                            hadir: 'bg-status-hadir-bg dark:bg-green-900/30',
                            izin: 'bg-status-izin-bg dark:bg-blue-900/30',
                            sakit: 'bg-status-sakit-bg dark:bg-yellow-900/30',
                            alfa: 'bg-status-alfa-bg dark:bg-red-900/30',
                        }[status] || 'bg-gray-50 dark:bg-gray-700';
                    },
                    statusText(status) {
                        return {
                            hadir: 'text-status-hadir-text dark:text-green-400',
                            izin: 'text-status-izin-text dark:text-blue-400',
                            sakit: 'text-status-sakit-text dark:text-yellow-400',
                            alfa: 'text-status-alfa-text dark:text-red-400',
                        }[status] || 'text-gray-300 dark:text-gray-500';
                    },
                };
            }
        </script>
    @endpush

</x-app-layout>
