<div>
    <!-- List Jadwal -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($jadwal as $jadw)
            <div wire:key="jadwal-{{ $jadw->id }}" class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-6 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-24 h-24 bg-brand-blue/5 rounded-bl-[100px] -z-10 group-hover:bg-brand-blue/10 transition-colors"></div>

                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-brand-light dark:bg-brand-blue/20 text-brand-blue dark:text-brand-light flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[24px]">event</span>
                        </div>
                        <div>
                            <h3 class="font-heading font-bold text-lg text-gray-900 dark:text-white leading-none">{{ $jadw->hari }}</h3>
                            <span class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-1 inline-block">Rutin Mingguan</span>
                        </div>
                    </div>

                    <div class="flex gap-1">
                        <button type="button" wire:click="edit({{ $jadw->id }})" class="p-2 text-gray-400 hover:text-brand-blue dark:hover:text-brand-light bg-gray-50 dark:bg-gray-800 rounded-lg transition-colors" title="Edit">
                            <span class="material-symbols-outlined text-[18px]">edit</span>
                        </button>
                        <button type="button" wire:click="openDelete({{ $jadw->id }}, '{{ $jadw->hari }}')" class="p-2 text-gray-400 hover:text-red-500 bg-gray-50 dark:bg-gray-800 rounded-lg transition-colors" title="Hapus">
                            <span class="material-symbols-outlined text-[18px]">delete</span>
                        </button>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center gap-4 bg-gray-50 dark:bg-gray-800/50 p-3 rounded-xl border border-gray-100 dark:border-gray-700">
                        <div class="flex-1">
                            <p class="text-[10px] text-gray-500 dark:text-gray-400 uppercase font-bold tracking-wider mb-0.5">Mulai</p>
                            <p class="font-semibold text-gray-800 dark:text-white">{{ \Carbon\Carbon::parse($jadw->jam_start)->format('H:i') }} WIB</p>
                        </div>
                        <div class="w-px h-8 bg-gray-200 dark:bg-gray-700"></div>
                        <div class="flex-1">
                            <p class="text-[10px] text-gray-500 dark:text-gray-400 uppercase font-bold tracking-wider mb-0.5">Selesai</p>
                            <p class="font-semibold text-gray-800 dark:text-white">{{ \Carbon\Carbon::parse($jadw->jam_close)->format('H:i') }} WIB</p>
                        </div>
                    </div>

                    <div>
                        <p class="text-[10px] text-gray-500 dark:text-gray-400 uppercase font-bold tracking-wider mb-2">Petugas Bertugas</p>
                        <div class="flex flex-wrap gap-2">
                            @forelse ($jadw->petugas as $pt)
                                <span class="bg-blue-50 dark:bg-brand-blue/20 text-brand-blue dark:text-brand-light px-2.5 py-1 rounded-md text-xs font-semibold border border-blue-100 dark:border-brand-blue/30">{{ $pt->anggota?->nama_lengkap ?? $pt->name }}</span>
                            @empty
                                <span class="text-xs text-red-500 font-medium italic">Belum ada petugas</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white dark:bg-gray-800 rounded-3xl border border-gray-50 dark:border-gray-700 shadow-card py-16 px-6 text-center">
                <span class="material-symbols-outlined text-[48px] text-gray-300 dark:text-gray-600 mb-3">calendar_month</span>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Belum ada jadwal latihan</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Gunakan tombol 'Tambah Jadwal' untuk membuat jadwal pertama.</p>
            </div>
        @endforelse
    </div>

    <!-- Modal Tambah/Edit Jadwal -->
    <div id="jadwalModal" class="modal-overlay fixed inset-0 bg-gray-900/50 dark:bg-black/70 backdrop-blur-sm z-[100] flex items-center justify-center p-4 @if($showForm) active @endif">
        <div class="modal-content bg-white dark:bg-gray-800 rounded-2xl w-full max-w-lg shadow-2xl overflow-y-auto flex flex-col max-h-[90vh]">
            <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center sticky top-0 bg-white dark:bg-gray-800 rounded-t-2xl z-10">
                <h3 class="font-heading font-bold text-xl text-gray-900 dark:text-white">{{ $editingId ? 'Edit Jadwal' : 'Tambah Jadwal Baru' }}</h3>
                <button type="button" wire:click="closeForm" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="p-6 space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Hari Latihan <span class="text-red-500">*</span></label>
                    <select wire:model="hari" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl font-medium text-gray-800 dark:text-white focus:outline-none focus:border-brand-blue focus:ring-2 focus:ring-brand-light dark:focus:ring-brand-blue/20 transition-all appearance-none">
                        @foreach ($daftarHari as $h)
                            <option value="{{ $h }}" @selected($hari === $h)>{{ $h }}</option>
                        @endforeach
                    </select>
                    @error('hari') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Jam Mulai <span class="text-red-500">*</span></label>
                        <input type="time" wire:model="jamMulai" value="{{ $jamMulai }}" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl font-medium text-gray-800 dark:text-white focus:outline-none focus:border-brand-blue focus:ring-2 focus:ring-brand-light dark:focus:ring-brand-blue/20 transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Jam Tutup <span class="text-red-500">*</span></label>
                        <input type="time" wire:model="jamTutup" value="{{ $jamTutup }}" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl font-medium text-gray-800 dark:text-white focus:outline-none focus:border-brand-blue focus:ring-2 focus:ring-brand-light dark:focus:ring-brand-blue/20 transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Petugas Absensi <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @forelse ($petugas as $p)
                            <label class="relative flex items-center gap-3 p-3 border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-gray-800 cursor-pointer hover:border-brand-blue/50 transition-all has-[:checked]:border-brand-blue dark:has-[:checked]:border-brand-blue has-[:checked]:bg-brand-light/40 dark:has-[:checked]:bg-brand-blue/20">
                                <input type="checkbox" wire:model="petugasTerpilih" value="{{ $p->id }}" @checked(in_array((string) $p->id, $petugasTerpilih, true)) class="accent-brand-blue">
                                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $p->anggota?->nama_lengkap ?? $p->name }}</span>
                            </label>
                        @empty
                            <p class="text-xs text-gray-500 dark:text-gray-400 italic col-span-full">Belum ada petugas terdaftar.</p>
                        @endforelse
                    </div>
                    @error('petugasTerpilih') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">info</span>
                        Maksimal 2 petugas dalam satu jadwal.
                    </p>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 flex justify-end gap-3 rounded-b-2xl mt-auto">
                <button type="button" wire:click="closeForm" class="px-5 py-2.5 text-gray-600 dark:text-gray-300 font-medium hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors">Batal</button>
                <button type="button" wire:click="save" class="px-5 py-2.5 bg-brand-blue hover:bg-brand-hover text-white font-semibold rounded-xl shadow-md transition-colors">Simpan Jadwal</button>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div id="deleteModal" class="modal-overlay fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-[100] flex items-center justify-center p-4 @if($showDelete) active @endif">
        <div class="modal-content bg-white dark:bg-gray-800 w-full max-w-sm rounded-3xl shadow-2xl p-6 text-center">
            <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-[32px]">warning</span>
            </div>
            <h3 class="font-heading font-bold text-lg text-gray-900 dark:text-white mb-2">Hapus Jadwal?</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Apakah Anda yakin ingin menghapus jadwal latihan hari <strong class="text-gray-900 dark:text-white">{{ $deleteHari }}</strong>?</p>
            <div class="flex justify-center gap-3">
                <button type="button" wire:click="closeDelete" class="px-5 py-2.5 rounded-xl text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors w-full">Batal</button>
                <button type="button" wire:click="confirmDelete" class="px-5 py-2.5 bg-red-500 text-white rounded-xl text-sm font-semibold hover:bg-red-600 transition-colors shadow-md shadow-red-500/30 w-full">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>
