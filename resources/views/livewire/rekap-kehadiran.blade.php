<div>
    <!-- Header: Title + Export Actions (aligned on the same row) -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div>
            <h1 class="font-heading font-extrabold text-3xl text-gray-900 dark:text-white tracking-tight">Rekap Kehadiran</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 font-medium">Filter dan unduh data absensi anggota untuk laporan periodik</p>
        </div>
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full sm:w-auto mt-4 sm:mt-0">
            <button type="button" wire:click="exportExcel"
                class="flex items-center justify-center gap-2 px-5 py-3 md:py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 rounded-xl md:rounded-full font-semibold text-sm hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-brand-blue dark:hover:text-brand-light hover:border-brand-blue/30 transition-colors shadow-sm">
                <span class="material-symbols-outlined text-[20px]">download</span>
                Export Excel
            </button>
            <button type="button" wire:click="exportPdf"
                class="flex items-center justify-center gap-2 px-5 py-3 md:py-2.5 bg-brand-blue text-white rounded-xl md:rounded-full font-semibold text-sm hover:bg-brand-hover shadow-lg shadow-brand-blue/30 transition-colors">
                <span class="material-symbols-outlined text-[20px]">picture_as_pdf</span>
                Export PDF
            </button>
        </div>
    </div>

    <div class="space-y-6 md:space-y-8">

    <!-- Filter Card -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-50 dark:border-gray-700 p-5 md:p-7 shadow-card relative overflow-hidden transition-colors duration-300">
        <div class="absolute -right-20 -top-20 w-64 h-64 bg-brand-light dark:bg-brand-blue/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="flex flex-col lg:flex-row lg:items-end gap-4 md:gap-5 relative z-10">
            <div class="flex-1 w-full space-y-2">
                <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider">Cari Anggota</label>
                <div class="relative w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-2xl focus-within:border-brand-blue dark:focus-within:border-brand-blue/70 focus-within:ring-4 focus-within:ring-brand-light dark:focus-within:ring-brand-blue/20 transition-all overflow-hidden flex items-center h-12">
                    <span class="material-symbols-outlined absolute left-4 text-gray-400 pointer-events-none z-0 text-[20px]">search</span>
                    <input type="text" wire:model.live.debounce.300ms="search" class="w-full px-4 pl-12 py-2.5 bg-transparent font-medium text-sm text-gray-800 dark:text-white focus:outline-none relative z-10 border-0 ring-0 focus:ring-0" placeholder="Cari nama, NIM, atau ID anggota...">
                </div>
            </div>

            <div class="flex-1 w-full space-y-2">
                <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider">Tanggal Awal</label>
                <div class="relative w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-2xl focus-within:border-brand-blue dark:focus-within:border-brand-blue/70 focus-within:ring-4 focus-within:ring-brand-light dark:focus-within:ring-brand-blue/20 transition-all overflow-hidden flex items-center h-12">
                    <input type="date" wire:model.live="start" class="w-full px-4 pl-12 py-2.5 bg-transparent font-medium text-sm text-gray-800 dark:text-white focus:outline-none cursor-pointer relative z-10 border-0 ring-0 focus:ring-0">
                    <span class="material-symbols-outlined absolute left-4 text-gray-400 pointer-events-none z-0 text-[20px]">calendar_today</span>
                </div>
            </div>

            <div class="flex-1 w-full space-y-2">
                <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider">Tanggal Akhir</label>
                <div class="relative w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-2xl focus-within:border-brand-blue dark:focus-within:border-brand-blue/70 focus-within:ring-4 focus-within:ring-brand-light dark:focus-within:ring-brand-blue/20 transition-all overflow-hidden flex items-center h-12">
                    <input type="date" wire:model.live="end" class="w-full px-4 pl-12 py-2.5 bg-transparent font-medium text-sm text-gray-800 dark:text-white focus:outline-none cursor-pointer relative z-10 border-0 ring-0 focus:ring-0">
                    <span class="material-symbols-outlined absolute left-4 text-gray-400 pointer-events-none z-0 text-[20px]">calendar_today</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Banner -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center bg-brand-light dark:bg-brand-blue/10 rounded-2xl p-4 md:p-5 border border-brand-blue/10 dark:border-brand-blue/20 gap-3 md:gap-0 transition-colors duration-300">
        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
            Periode: <span class="font-bold text-gray-900 dark:text-white">{{ $this->formatShort($start) }} - {{ $this->formatShort($end) }}</span>
        </p>
        <div class="flex flex-wrap gap-4 text-xs font-semibold text-gray-600 dark:text-gray-400">
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-green-500"></span>Hadir: <span>{{ $summary['hadir'] }}</span></span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-yellow-500"></span>Sakit: <span>{{ $summary['sakit'] }}</span></span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>Izin: <span>{{ $summary['izin'] }}</span></span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>Alfa: <span>{{ $summary['alfa'] }}</span></span>
        </div>
    </div>

    <!-- Aggregate Table Card -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-50 dark:border-gray-700 shadow-card flex flex-col overflow-hidden transition-colors duration-300">
        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[700px]">
                <thead>
                    <tr class="bg-gray-50/80 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700 transition-colors">
                        <th class="py-3 px-3 md:py-4 md:px-6 font-bold text-[10px] md:text-[11px] text-gray-400 dark:text-gray-400 uppercase tracking-wider w-12">No</th>
                        <th class="py-3 px-3 md:py-4 md:px-6 font-bold text-[10px] md:text-[11px] text-gray-400 dark:text-gray-400 uppercase tracking-wider">Nama Anggota</th>
                        <th class="py-3 px-3 md:py-4 md:px-6 font-bold text-[10px] md:text-[11px] text-gray-400 dark:text-gray-400 uppercase tracking-wider">NIM</th>
                        <th class="py-3 px-3 md:py-4 md:px-6 font-bold text-[10px] md:text-[11px] text-gray-400 dark:text-gray-400 uppercase tracking-wider text-center">Total Sakit</th>
                        <th class="py-3 px-3 md:py-4 md:px-6 font-bold text-[10px] md:text-[11px] text-gray-400 dark:text-gray-400 uppercase tracking-wider text-center">Total Izin</th>
                        <th class="py-3 px-3 md:py-4 md:px-6 font-bold text-[10px] md:text-[11px] text-gray-400 dark:text-gray-400 uppercase tracking-wider text-center">Total Alfa</th>
                        <th class="py-3 px-3 md:py-4 md:px-6 font-bold text-[10px] md:text-[11px] text-gray-400 dark:text-gray-400 uppercase tracking-wider text-center">Total Hadir</th>
                    </tr>
                </thead>
                <tbody id="tableBody" class="divide-y divide-gray-50 dark:divide-gray-700">
                    @forelse ($anggota as $a)
                        <tr wire:key="rekap-{{ $a->id }}" class="hover:bg-brand-light/40 dark:hover:bg-gray-700/40 transition-colors group">
                            <td class="py-3 px-3 md:py-4 md:px-6 text-sm font-bold text-gray-400 dark:text-gray-500">{{ $anggota->firstItem() + $loop->index }}</td>
                            <td class="py-3 px-3 md:py-4 md:px-6">
                                <span class="font-bold text-gray-900 dark:text-white text-[11px] md:text-sm group-hover:text-brand-blue dark:group-hover:text-brand-light transition-colors">{{ $a->nama_lengkap }}</span>
                            </td>
                            <td class="py-3 px-3 md:py-4 md:px-6 text-[11px] md:text-sm font-medium text-gray-500 dark:text-gray-400">{{ $a->nim }}</td>
                            <td class="py-3 px-3 md:py-4 md:px-6 text-center">
                                <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-[11px] md:text-xs font-bold bg-status-sakit-bg text-status-sakit-text dark:bg-yellow-900/30 dark:text-yellow-400 min-w-[32px]">{{ $a->total_sakit }}</span>
                            </td>
                            <td class="py-3 px-3 md:py-4 md:px-6 text-center">
                                <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-[11px] md:text-xs font-bold bg-status-izin-bg text-status-izin-text dark:bg-blue-900/30 dark:text-blue-400 min-w-[32px]">{{ $a->total_izin }}</span>
                            </td>
                            <td class="py-3 px-3 md:py-4 md:px-6 text-center">
                                <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-[11px] md:text-xs font-bold bg-status-alfa-bg text-status-alfa-text dark:bg-red-900/30 dark:text-red-400 min-w-[32px]">{{ $a->total_alfa }}</span>
                            </td>
                            <td class="py-3 px-3 md:py-4 md:px-6 text-center">
                                <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-[11px] md:text-xs font-bold bg-status-hadir-bg text-status-hadir-text dark:bg-green-900/30 dark:text-green-400 min-w-[32px]">{{ $a->total_hadir }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="py-12 flex flex-col items-center justify-center text-center">
                                    <span class="material-symbols-outlined text-[48px] text-gray-300 dark:text-gray-600 mb-3">search_off</span>
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Tidak ada data ditemukan</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Coba sesuaikan filter pencarian atau rentang tanggal.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-5 md:p-6 border-t border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row items-center justify-between text-sm text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 gap-4">
            <span class="font-medium text-xs md:text-sm">Menampilkan {{ $anggota->count() }} dari {{ $anggota->total() }} anggota</span>
            <div class="flex gap-1.5">
                <button type="button" wire:key="rekap-page-prev" wire:click="previousPage" @disabled($anggota->onFirstPage())
                    class="px-3.5 py-2 md:px-3 md:py-1.5 border border-gray-200 dark:border-gray-700 rounded-lg font-medium transition-colors {{ $anggota->onFirstPage() ? 'bg-gray-50 dark:bg-gray-800/50 text-gray-400 dark:text-gray-600 cursor-not-allowed' : 'bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300' }}">
                    Prev
                </button>
                @for ($i = 1; $i <= $anggota->lastPage(); $i++)
                    <button type="button" wire:key="rekap-page-{{ $i }}" wire:click="gotoPage({{ $i }})"
                        class="{{ $i === $anggota->currentPage() ? 'w-10 md:w-8 py-2 md:py-1.5 bg-brand-blue text-white rounded-lg font-bold shadow-md shadow-brand-blue/20' : 'w-10 md:w-8 py-2 md:py-1.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-brand-blue dark:hover:text-white font-medium transition-colors' }}">
                        {{ $i }}
                    </button>
                @endfor
                <button type="button" wire:key="rekap-page-next" wire:click="nextPage" @disabled(!$anggota->hasMorePages())
                    class="px-3.5 py-2 md:px-3 md:py-1.5 border border-gray-200 dark:border-gray-700 rounded-lg font-medium transition-colors {{ $anggota->hasMorePages() ? 'bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300' : 'bg-gray-50 dark:bg-gray-800/50 text-gray-400 dark:text-gray-600 cursor-not-allowed' }}">
                    Next
                </button>
            </div>
        </div>
    </div>
    </div>
</div>
