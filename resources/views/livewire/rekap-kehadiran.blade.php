<div>
    <!-- Filter Card -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-50 dark:border-gray-700 p-5 md:p-7 shadow-card relative overflow-hidden transition-colors duration-300">
        <div class="absolute -right-20 -top-20 w-64 h-64 bg-brand-light dark:bg-brand-blue/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="flex flex-col md:flex-row md:items-end gap-4 md:gap-5 relative z-10">
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

            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto pt-2 md:pt-0 shrink-0">
                <a href="{{ $this->exportUrl() }}"
                   class="w-full sm:w-auto flex-1 sm:flex-none py-3.5 px-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 rounded-2xl font-semibold text-sm hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-brand-blue dark:hover:text-brand-light hover:border-brand-blue/30 transition-colors flex items-center justify-center gap-2 shadow-sm focus:outline-none">
                    <span class="material-symbols-outlined text-[20px]">download</span>
                    Export Excel
                </a>
            </div>
        </div>
    </div>

    <!-- Summary Banner -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center bg-brand-light dark:bg-brand-blue/10 rounded-2xl p-4 md:p-5 border border-brand-blue/10 dark:border-brand-blue/20 gap-3 md:gap-0 transition-colors duration-300">
        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
            Menampilkan data dari <span class="font-bold text-gray-900 dark:text-white">{{ $this->formatShort($start) }} - {{ $this->formatShort($end) }}</span> (Total: <span class="font-bold text-gray-900 dark:text-white">{{ count($data) }}</span> data)
        </p>
        <div class="flex flex-wrap gap-4 text-xs font-semibold text-gray-600 dark:text-gray-400">
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-green-500"></span>Hadir: <span>{{ $summary['hadir'] }}</span></span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>Izin: <span>{{ $summary['izin'] }}</span></span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-yellow-500"></span>Sakit: <span>{{ $summary['sakit'] }}</span></span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>Alfa: <span>{{ $summary['alfa'] }}</span></span>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-50 dark:border-gray-700 shadow-card flex flex-col overflow-hidden transition-colors duration-300">
        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/80 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700 transition-colors">
                        <th class="py-3 px-3 md:py-4 md:px-6 font-bold text-[10px] md:text-[11px] text-gray-400 dark:text-gray-400 uppercase tracking-wider">NAMA ANGGOTA</th>
                        <th class="py-3 px-3 md:py-4 md:px-6 font-bold text-[10px] md:text-[11px] text-gray-400 dark:text-gray-400 uppercase tracking-wider">NIM</th>
                        <th class="py-3 px-3 md:py-4 md:px-6 font-bold text-[10px] md:text-[11px] text-gray-400 dark:text-gray-400 uppercase tracking-wider hidden sm:table-cell">TANGGAL LATIHAN</th>
                        <th class="py-3 px-3 md:py-4 md:px-6 font-bold text-[10px] md:text-[11px] text-gray-400 dark:text-gray-400 uppercase tracking-wider">STATUS</th>
                    </tr>
                </thead>
                <tbody id="tableBody" class="divide-y divide-gray-50 dark:divide-gray-700">
                    @forelse ($data as $item)
                        <tr wire:key="rekap-{{ $item->id }}" class="hover:bg-brand-light/40 dark:hover:bg-gray-700/40 transition-colors group">
                            <td class="py-3 px-3 md:py-4 md:px-6 font-bold text-gray-900 dark:text-white text-[11px] md:text-sm group-hover:text-brand-blue dark:group-hover:text-brand-light transition-colors">
                                {{ $item->anggota?->nama_lengkap ?? '-' }}
                                <span class="block sm:hidden text-[10px] text-gray-500 dark:text-gray-400 font-normal mt-0.5">{{ $this->formatTanggal($item->tanggal) }}</span>
                            </td>
                            <td class="py-3 px-3 md:py-4 md:px-6 text-[11px] md:text-sm font-medium text-gray-500 dark:text-gray-400">{{ $item->anggota?->nim }}</td>
                            <td class="py-3 px-3 md:py-4 md:px-6 text-[11px] md:text-sm font-medium text-gray-600 dark:text-gray-300 hidden sm:table-cell">{{ $this->formatTanggal($item->tanggal) }}</td>
                            <td class="py-3 px-3 md:py-4 md:px-6">
                                <x-status-badge :status="$item->status" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="py-12 flex flex-col items-center justify-center text-center">
                                    <span class="material-symbols-outlined text-[48px] text-gray-300 dark:text-gray-600 mb-3">search_off</span>
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Tidak ada data ditemukan</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Coba sesuaikan rentang tanggal pada filter.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
