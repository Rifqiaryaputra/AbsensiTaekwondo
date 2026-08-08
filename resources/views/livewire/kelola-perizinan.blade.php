<div>
    <!-- Filter Bar -->
    <div class="bg-white dark:bg-dark-card rounded-3xl border border-gray-50 dark:border-dark-border p-5 md:p-6 shadow-card dark:shadow-dark-card flex flex-col md:flex-row gap-4 items-center z-10 relative transition-colors duration-300">
        <div class="relative w-full flex-1">
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">search</span>
            <input wire:model.live.debounce.300ms="search"
                class="w-full pl-12 pr-4 py-3 md:py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl md:rounded-xl font-medium text-sm text-gray-800 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:border-brand-blue focus:ring-4 focus:ring-brand-light dark:focus:ring-brand-blue/20 transition-all"
                placeholder="Cari nama anggota..." type="text">
        </div>

        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            <select wire:model.live="statusFilter" class="px-4 py-3 md:py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl md:rounded-xl font-medium text-sm text-gray-600 dark:text-gray-300 focus:outline-none focus:border-brand-blue focus:ring-4 focus:ring-brand-light dark:focus:ring-brand-blue/20 transition-all min-w-[150px] appearance-none cursor-pointer">
                <option value="">Semua Status</option>
                <option value="menunggu">Menunggu</option>
                <option value="disetujui">Disetujui</option>
                <option value="ditolak">Ditolak</option>
                <option value="dibatalkan">Dibatalkan</option>
            </select>
            <div class="relative w-full sm:w-auto">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">calendar_today</span>
                <input wire:model.live="dateFilter" class="w-full sm:w-auto pl-10 pr-4 py-3 md:py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl md:rounded-xl font-medium text-sm text-gray-600 dark:text-gray-300 focus:outline-none focus:border-brand-blue focus:ring-4 focus:ring-brand-light dark:focus:ring-brand-blue/20 transition-all" type="date">
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white dark:bg-dark-card rounded-3xl border border-gray-50 dark:border-dark-border shadow-card dark:shadow-dark-card flex flex-col overflow-hidden transition-colors duration-300">
        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[700px]">
                <thead>
                    <tr class="bg-gray-50/80 dark:bg-gray-800/80 border-b border-gray-100 dark:border-dark-border">
                        <th class="py-4 px-6 font-bold text-[11px] text-gray-400 dark:text-gray-500 uppercase tracking-wider">Profil Anggota</th>
                        <th class="py-4 px-6 font-bold text-[11px] text-gray-400 dark:text-gray-500 uppercase tracking-wider">Detail Pengajuan</th>
                        <th class="py-4 px-6 font-bold text-[11px] text-gray-400 dark:text-gray-500 uppercase tracking-wider">Keterangan / Bukti</th>
                        <th class="py-4 px-6 font-bold text-[11px] text-gray-400 dark:text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="py-4 px-6 font-bold text-[11px] text-gray-400 dark:text-gray-500 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-dark-border">
                    @forelse ($pengajuan as $item)
                        @php
                            $isSakit = $item->jenis === 'sakit';
                            $typeClass = $isSakit
                                ? 'bg-status-sakit-bg text-status-sakit-text dark:bg-yellow-900/30 dark:text-yellow-400'
                                : 'bg-status-izin-bg text-status-izin-text dark:bg-blue-900/30 dark:text-blue-400';
                        @endphp
                        <tr wire:key="izin-{{ $item->id }}" class="hover:bg-brand-light/40 dark:hover:bg-gray-800/50 transition-colors group align-top">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="hidden md:flex w-10 h-10 rounded-full bg-brand-blue text-white items-center justify-center font-heading font-bold text-sm shrink-0">
                                        {{ strtoupper(mb_substr($item->anggota?->nama_lengkap ?? '-', 0, 1)) }}
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-bold text-gray-900 dark:text-white text-sm">{{ $item->anggota?->nama_lengkap ?? '-' }}</span>
                                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 mt-0.5">NIM: {{ $item->anggota?->nim }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex flex-col gap-1.5 items-start">
                                    <span class="px-2.5 py-1 {{ $typeClass }} rounded-md text-[10px] font-bold tracking-wide uppercase">{{ $item->jenis }}</span>
                                    <div class="flex flex-col mt-0.5">
                                        <span class="text-xs font-bold text-gray-700 dark:text-gray-300 hidden md:block">Untuk Jadwal:</span>
                                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 leading-tight">{{ $this->formatTanggal($item->tanggal) }} ({{ $item->jadwal?->jam_start }})</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex flex-col gap-2 items-start">
                                    @if ($item->keterangan)
                                        <div class="text-xs text-gray-600 dark:text-gray-400 font-medium max-w-[220px] line-clamp-2 break-words" title="{{ $item->keterangan }}"><span class="font-semibold text-gray-800 dark:text-gray-200">Alasan:</span> {{ $item->keterangan }}</div>
                                    @endif
                                    @if ($item->bukti_lampiran)
                                        <a href="{{ asset($item->bukti_lampiran) }}" target="_blank" class="flex items-center gap-2 px-3 py-1.5 bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg transition-colors group/btn w-fit">
                                            <span class="material-symbols-outlined text-[18px] text-blue-500">attach_file</span>
                                            <span class="text-xs font-semibold text-gray-600 dark:text-gray-300 group-hover/btn:text-brand-blue dark:group-hover/btn:text-white truncate max-w-[180px]">Lihat Bukti</span>
                                        </a>
                                    @endif
                                    @if (! $item->keterangan && ! $item->bukti_lampiran)
                                        <p class="text-xs text-gray-400 dark:text-gray-500 italic">Tidak ada keterangan</p>
                                    @endif
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex flex-col gap-1">
                                    <x-status-badge :status="$item->status" />
                                    @if ($item->status === 'menunggu' && $item->diajukan_pada)
                                        <span class="text-[10px] text-gray-400 dark:text-gray-500 font-medium mt-1">Diajukan: {{ $item->diajukan_pada->format('d M Y H:i') }}</span>
                                    @elseif ($item->status === 'disetujui' && $item->diproses_pada)
                                        <span class="text-[10px] text-green-600 dark:text-green-500 font-medium flex items-center gap-0.5 mt-1">
                                            <span class="material-symbols-outlined text-[12px]">sync</span> Diupdate
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-4 px-6 text-right">
                                @if ($item->status === 'menunggu')
                                    <div class="flex items-center justify-end gap-2">
                                        <button wire:click="setStatus({{ $item->id }}, 'disetujui')" class="w-9 h-9 flex items-center justify-center bg-transparent text-green-600 dark:text-green-400 rounded-xl hover:bg-green-50 dark:hover:bg-green-900/30 transition-colors" title="Setujui (Approve)">
                                            <span class="material-symbols-outlined text-[20px]">check</span>
                                        </button>
                                        <button wire:click="setStatus({{ $item->id }}, 'ditolak')" class="w-9 h-9 flex items-center justify-center bg-transparent text-red-400 dark:text-red-400 rounded-xl hover:bg-red-50 dark:hover:bg-red-900/30 hover:text-red-500 transition-colors" title="Tolak (Reject)">
                                            <span class="material-symbols-outlined text-[20px]">close</span>
                                        </button>
                                    </div>
                                @elseif ($item->status === 'disetujui')
                                    <span class="inline-flex items-center gap-1 bg-gray-50/80 dark:bg-gray-800 px-3 py-2 rounded-lg border border-gray-100 dark:border-gray-700 text-xs font-bold text-gray-500 dark:text-gray-400">
                                        <span class="material-symbols-outlined text-[16px]">verified</span> Done
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 bg-red-50 dark:bg-red-900/20 px-3 py-2 rounded-lg border border-red-100 dark:border-red-900/50 text-xs font-bold text-red-500">
                                        <span class="material-symbols-outlined text-[16px]">cancel</span> Rejected
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="py-12 flex flex-col items-center justify-center text-center">
                                    <span class="material-symbols-outlined text-[64px] text-gray-300 dark:text-gray-600 mb-4">event_busy</span>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Data tidak ditemukan</h3>
                                    <p class="text-sm text-gray-500 dark:text-dark-muted mt-1">Coba sesuaikan filter pencarian Anda.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-5 border-t border-gray-100 dark:border-dark-border flex items-center justify-between text-sm text-gray-500 dark:text-dark-muted bg-white dark:bg-dark-card">
            <span class="font-medium text-xs md:text-sm">Menampilkan {{ count($pengajuan) }} pengajuan</span>
        </div>
    </div>
</div>
