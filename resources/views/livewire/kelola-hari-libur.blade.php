<div>
    @if (count($libur) === 0)
        <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-50 dark:border-gray-700 shadow-card py-16 px-6 text-center flex-col items-center justify-center transition-colors duration-300">
            <div class="w-20 h-20 bg-gray-50 dark:bg-gray-700/50 rounded-full flex items-center justify-center mb-5 mx-auto">
                <span class="material-symbols-outlined text-4xl text-gray-400 dark:text-gray-500">event_busy</span>
            </div>
            <h3 class="font-heading font-bold text-xl text-gray-900 dark:text-white mb-2">Belum ada hari libur terdaftar</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 font-medium max-w-md mx-auto mb-8">Tambahkan pengecualian hari libur agar sistem tidak mengharapkan kehadiran pada tanggal tersebut.</p>
            <button type="button" wire:click="openForm" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-brand-blue text-white rounded-full font-semibold text-sm hover:bg-brand-hover shadow-lg shadow-brand-blue/30 transition-colors">
                <span class="material-symbols-outlined text-[20px]">add</span>
                Tambah Libur Pertama
            </button>
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-50 dark:border-gray-700 shadow-card flex flex-col overflow-hidden transition-colors duration-300">
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/80 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700 transition-colors">
                            <th class="py-3 px-4 md:py-4 md:px-6 font-bold text-[10px] md:text-[11px] text-gray-400 dark:text-gray-400 uppercase tracking-wider">Tanggal</th>
                            <th class="py-3 px-4 md:py-4 md:px-6 font-bold text-[10px] md:text-[11px] text-gray-400 dark:text-gray-400 uppercase tracking-wider">Keterangan</th>
                            <th class="py-3 px-4 md:py-4 md:px-6 font-bold text-[10px] md:text-[11px] text-gray-400 dark:text-gray-400 uppercase tracking-wider text-right w-24 md:w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                        @foreach ($libur as $l)
                            <tr wire:key="libur-{{ $l->id }}" class="hover:bg-brand-light/40 dark:hover:bg-gray-700/40 transition-colors group">
                                <td class="py-3 px-4 md:py-4 md:px-6">
                                    <div class="flex items-start md:items-center gap-2 md:gap-3">
                                        <div class="w-8 h-8 md:w-10 md:h-10 rounded-xl md:rounded-2xl bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 flex items-center justify-center text-gray-500 dark:text-gray-400 shrink-0 mt-0.5 md:mt-0">
                                            <span class="material-symbols-outlined text-[16px] md:text-[20px]">calendar_month</span>
                                        </div>
                                        <span class="font-bold text-gray-900 dark:text-white text-xs md:text-sm pt-1.5 md:pt-0">{{ $l->tanggal?->translatedFormat('l, d F Y') }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 md:py-4 md:px-6">
                                    <span class="font-medium text-gray-600 dark:text-gray-300 text-xs md:text-sm">{{ $l->keterangan }}</span>
                                </td>
                                <td class="py-3 px-4 md:py-4 md:px-6 text-right">
                                    <div class="flex items-center justify-end gap-1 md:gap-2">
                                        <button type="button" wire:click="openForm({{ $l->id }})" class="w-8 h-8 md:w-9 md:h-9 flex items-center justify-center bg-transparent text-gray-400 dark:text-gray-500 rounded-lg md:rounded-xl hover:bg-amber-50 dark:hover:bg-amber-900/20 hover:text-amber-500 dark:hover:text-amber-400 transition-colors opacity-100 md:opacity-70 group-hover:opacity-100 focus:outline-none" title="Edit Data">
                                            <span class="material-symbols-outlined text-[18px] md:text-[20px]">edit</span>
                                        </button>
                                        <button type="button" wire:click="openDelete({{ $l->id }}, '{{ $l->tanggal?->translatedFormat('l, d F Y') }}')" class="w-8 h-8 md:w-9 md:h-9 flex items-center justify-center bg-transparent text-gray-400 dark:text-gray-500 rounded-lg md:rounded-xl hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-500 dark:hover:text-red-400 transition-colors opacity-100 md:opacity-70 group-hover:opacity-100 focus:outline-none" title="Hapus Data">
                                            <span class="material-symbols-outlined text-[18px] md:text-[20px]">delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Modal Tambah/Edit -->
    <div id="formModal" class="modal-overlay fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-[100] flex items-center justify-center p-4 @if($showForm) active @endif">
        <div class="modal-content bg-white dark:bg-gray-800 w-full max-w-md rounded-3xl shadow-2xl overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                <h3 class="font-heading font-bold text-lg text-gray-900 dark:text-white">{{ $editingId ? 'Edit Hari Libur' : 'Tambah Hari Libur' }}</h3>
                <button type="button" wire:click="closeForm" class="text-gray-400 hover:text-red-500 dark:hover:text-red-400 transition-colors focus:outline-none">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="p-6 space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Tanggal Libur <span class="text-red-500">*</span></label>
                    <div class="relative w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus-within:border-brand-blue dark:focus-within:border-brand-blue/70 focus-within:ring-1 focus-within:ring-brand-blue/20 transition-all overflow-hidden flex items-center">
                        <input type="date" wire:model="tanggal" value="{{ $tanggal }}" class="w-full px-4 py-2.5 bg-transparent font-medium text-sm text-gray-800 dark:text-white focus:outline-none cursor-pointer relative z-10 border-0 ring-0 focus:ring-0">
                        <span class="material-symbols-outlined absolute right-4 text-gray-400 pointer-events-none z-0 text-[20px]">calendar_today</span>
                    </div>
                    @error('tanggal') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Keterangan <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="keterangan" value="{{ $keterangan }}" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl font-medium text-sm text-gray-800 dark:text-white focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue/20 transition-all" placeholder="Misal: Libur Nasional Kemerdekaan">
                    @error('keterangan') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/80 flex justify-end gap-3">
                <button type="button" wire:click="closeForm" class="px-5 py-2.5 rounded-xl text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">Batal</button>
                <button type="button" wire:click="save" class="px-5 py-2.5 bg-brand-blue text-white rounded-xl text-sm font-semibold hover:bg-brand-hover transition-colors shadow-md shadow-brand-blue/30">Simpan</button>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div id="deleteModal" class="modal-overlay fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-[100] flex items-center justify-center p-4 @if($showDelete) active @endif">
        <div class="modal-content bg-white dark:bg-gray-800 w-full max-w-sm rounded-3xl shadow-2xl p-6 text-center">
            <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-[32px]">warning</span>
            </div>
            <h3 class="font-heading font-bold text-lg text-gray-900 dark:text-white mb-2">Hapus Libur?</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Apakah Anda yakin ingin menghapus data libur pada tanggal <strong class="text-gray-900 dark:text-white">{{ $deleteTanggal }}</strong>?</p>
            <div class="flex justify-center gap-3">
                <button type="button" wire:click="closeDelete" class="px-5 py-2.5 rounded-xl text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors w-full">Batal</button>
                <button type="button" wire:click="confirmDelete" class="px-5 py-2.5 bg-red-500 text-white rounded-xl text-sm font-semibold hover:bg-red-600 transition-colors shadow-md shadow-red-500/30 w-full">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>
