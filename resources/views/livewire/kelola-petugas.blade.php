<div>
    <!-- Main Card -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-50 dark:border-gray-700 shadow-card flex flex-col transition-colors duration-300">
        <div class="overflow-x-auto w-full pt-4">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/80 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700 transition-colors">
                        <th class="py-3 px-4 md:py-4 md:px-6 font-bold text-[10px] md:text-[11px] text-gray-400 dark:text-gray-400 uppercase tracking-wider">Profil Anggota</th>
                        <th class="py-3 px-2 md:py-4 md:px-6 font-bold text-[10px] md:text-[11px] text-gray-400 dark:text-gray-400 uppercase tracking-wider">Kontak & Login</th>
                        <th class="py-3 px-2 md:py-4 md:px-6 font-bold text-[10px] md:text-[11px] text-gray-400 dark:text-gray-400 uppercase tracking-wider">Jadwal Absen</th>
                        <th class="py-3 px-4 md:py-4 md:px-6 font-bold text-[10px] md:text-[11px] text-gray-400 dark:text-gray-400 uppercase tracking-wider text-right w-24 md:w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody" class="divide-y divide-gray-50 dark:divide-gray-700">
                    @forelse ($petugas as $p)
                        <tr wire:key="petugas-{{ $p->id }}" class="hover:bg-brand-light/40 dark:hover:bg-gray-700/40 transition-colors group">
                            <td class="py-3 px-4 md:py-4 md:px-6">
                                <div class="flex items-center gap-2.5 md:gap-4">
                                    @if ($p->anggota?->foto_dobok)
                                        <img src="{{ asset($p->anggota->foto_dobok) }}" alt="Foto {{ $p->anggota->nama_lengkap }}" class="w-9 h-9 md:w-11 md:h-11 rounded-full object-cover shadow-md shrink-0">
                                    @else
                                        <div class="w-9 h-9 md:w-11 md:h-11 rounded-full text-white flex items-center justify-center font-heading font-bold text-base md:text-lg shadow-md shrink-0" style="background-color: {{ $this->avatarColor($p->id) }}">
                                            {{ strtoupper(mb_substr($p->anggota?->nama_lengkap ?? $p->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div class="flex flex-col">
                                        <span class="font-bold text-gray-900 dark:text-white text-xs md:text-sm">{{ $p->anggota?->nama_lengkap ?? $p->name }}</span>
                                        <span class="text-[10px] md:text-xs font-medium text-gray-500 dark:text-gray-400 mt-0.5">NIM: {{ $p->anggota?->nim ?? '-' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-2 md:py-4 md:px-6">
                                <div class="flex flex-col">
                                    <span class="font-medium text-gray-700 dark:text-gray-300 text-xs md:text-sm">{{ $p->email }}</span>
                                    <span class="text-[10px] md:text-xs font-medium text-gray-400 dark:text-gray-500 mt-0.5">Pass: ********</span>
                                </div>
                            </td>
                            <td class="py-3 px-2 md:py-4 md:px-6">
                                @if ($p->jadwalPetugas->isNotEmpty())
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] md:text-xs font-semibold bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                        {{ $p->jadwalPetugas->pluck('hari')->join(', ') }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400 dark:text-gray-500 italic">Belum ada jadwal</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 md:py-4 md:px-6 text-right">
                                <div class="flex items-center justify-end gap-1 md:gap-2">
                                    <button type="button" wire:click="openForm({{ $p->id }})" class="w-8 h-8 md:w-9 md:h-9 flex items-center justify-center bg-transparent text-gray-400 dark:text-gray-500 rounded-lg md:rounded-xl hover:bg-amber-50 dark:hover:bg-amber-900/20 hover:text-amber-500 dark:hover:text-amber-400 transition-colors opacity-100 md:opacity-70 group-hover:opacity-100 focus:outline-none" title="Edit Data">
                                        <span class="material-symbols-outlined text-[18px] md:text-[20px]">edit</span>
                                    </button>
                                    <button type="button" wire:click="openDelete({{ $p->id }}, '{{ addslashes($p->anggota?->nama_lengkap ?? $p->name) }}')" class="w-8 h-8 md:w-9 md:h-9 flex items-center justify-center bg-transparent text-gray-400 dark:text-gray-500 rounded-lg md:rounded-xl hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-500 dark:hover:text-red-400 transition-colors opacity-100 md:opacity-70 group-hover:opacity-100 focus:outline-none" title="Hapus Data">
                                        <span class="material-symbols-outlined text-[18px] md:text-[20px]">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="flex-col items-center justify-center py-12 text-center">
                                    <span class="material-symbols-outlined text-[48px] text-gray-300 dark:text-gray-600 mb-3">person_off</span>
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Tidak ada data petugas</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Gunakan tombol 'Tambah Petugas' untuk memasukkan data baru.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah/Edit -->
    <div id="formModal" class="modal-overlay fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-[100] flex items-center justify-center p-4 @if($showForm) active @endif">
        <div class="modal-content bg-white dark:bg-gray-800 w-full max-w-lg rounded-3xl shadow-2xl overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                <h3 class="font-heading font-bold text-lg text-gray-900 dark:text-white">{{ $editingId ? 'Edit Petugas' : 'Tambah Petugas' }}</h3>
                <button type="button" wire:click="closeForm" class="text-gray-400 hover:text-red-500 dark:hover:text-red-400 transition-colors focus:outline-none">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="p-6 space-y-5">
                @if (! $editingId)
                    <div class="relative">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Cari Anggota <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[20px]">person_search</span>
                            <input type="text" wire:model.live.debounce.250ms="searchAnggota" autocomplete="off"
                                class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl font-medium text-sm text-gray-800 dark:text-white focus:outline-none focus:border-brand-blue dark:focus:border-brand-blue/50 transition-all"
                                placeholder="Ketik nama atau NIM anggota...">
                        </div>
                        @if ($candidates->isNotEmpty())
                            <ul class="absolute z-20 mt-1 w-full bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl shadow-lg max-h-40 overflow-y-auto">
                                @foreach ($candidates as $c)
                                    <li wire:key="cand-{{ $c->id }}" wire:click="selectAnggota({{ $c->id }}, '{{ addslashes($c->anggota?->nama_lengkap) }}', '{{ $c->anggota?->nim }}')" class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer border-b border-gray-50 dark:border-gray-700/50 last:border-0 transition-colors">
                                        <div class="font-semibold text-sm text-gray-900 dark:text-white">{{ $c->anggota?->nama_lengkap }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">NIM: {{ $c->anggota?->nim }}</div>
                                    </li>
                                @endforeach
                            </ul>
                        @elseif ($searchAnggota !== '')
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 italic">Anggota tidak ditemukan.</p>
                        @endif
                    </div>
                @endif

                @if ($selectedNama !== '')
                    <div class="p-3 bg-brand-light/50 dark:bg-gray-700/50 rounded-xl border border-brand-blue/20 dark:border-gray-600 flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-brand-blue text-white flex items-center justify-center font-bold text-sm shadow-sm">{{ strtoupper(mb_substr($selectedNama, 0, 1)) }}</div>
                            <div>
                                <p class="font-bold text-sm text-gray-900 dark:text-white leading-none">{{ $selectedNama }}</p>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">NIM: {{ $selectedNim }}</p>
                            </div>
                        </div>
                        @if (! $editingId)
                            <button type="button" wire:click="resetSelection" class="text-xs text-brand-blue dark:text-brand-light font-semibold hover:underline">Ganti</button>
                        @endif
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Email Login <span class="text-red-500">*</span></label>
                        <input type="email" wire:model="email" value="{{ $email }}" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl font-medium text-sm text-gray-800 dark:text-white focus:outline-none focus:border-brand-blue transition-all" placeholder="email@contoh.com">
                        @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Password {{ $editingId ? '(Opsional)' : '' }} <span class="text-red-500">{{ $editingId ? '' : '*' }}</span></label>
                        <input type="password" wire:model="password" value="{{ $password }}" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl font-medium text-sm text-gray-800 dark:text-white focus:outline-none focus:border-brand-blue transition-all" placeholder="••••••••">
                        @error('password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/80 flex justify-end gap-3">
                <button type="button" wire:click="closeForm" class="px-5 py-2.5 rounded-xl text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">Batal</button>
                <button type="button" wire:click="save" class="px-5 py-2.5 bg-brand-blue text-white rounded-xl text-sm font-semibold hover:bg-brand-hover transition-colors shadow-md shadow-brand-blue/30">Simpan Petugas</button>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div id="deleteModal" class="modal-overlay fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-[100] flex items-center justify-center p-4 @if($showDelete) active @endif">
        <div class="modal-content bg-white dark:bg-gray-800 w-full max-w-sm rounded-3xl shadow-2xl p-6 text-center">
            <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-[32px]">warning</span>
            </div>
            <h3 class="font-heading font-bold text-lg text-gray-900 dark:text-white mb-2">Hapus Petugas?</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Hapus akun petugas <strong class="text-gray-900 dark:text-white">{{ $deleteNama }}</strong>? Akun anggota aslinya tidak terpengaruh dan tetap dapat login sebagai anggota.</p>
            <div class="flex justify-center gap-3">
                <button type="button" wire:click="closeDelete" class="px-5 py-2.5 rounded-xl text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors w-full">Batal</button>
                <button type="button" wire:click="confirmDelete" class="px-5 py-2.5 bg-red-500 text-white rounded-xl text-sm font-semibold hover:bg-red-600 transition-colors shadow-md shadow-red-500/30 w-full">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>
