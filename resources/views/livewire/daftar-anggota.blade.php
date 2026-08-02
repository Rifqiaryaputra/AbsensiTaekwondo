<div>
    @php
        $avatarColors = ['bg-brand-blue', 'bg-purple-500', 'bg-sky-500', 'bg-emerald-500', 'bg-rose-500', 'bg-amber-500'];
        $avatarColor = fn ($id) => $avatarColors[$id % count($avatarColors)];
    @endphp

    <!-- Data Card -->
    <div class="bg-white dark:bg-dark-card rounded-3xl shadow-card dark:shadow-dark-card border border-gray-50 dark:border-dark-border flex flex-col overflow-hidden transition-colors duration-300">

        <!-- Filter Bar -->
        <div class="p-4 md:p-6 border-b border-gray-100 dark:border-dark-border flex flex-col md:flex-row items-center gap-4 bg-gray-50/50 dark:bg-gray-800/50">
            <div class="relative w-full md:flex-1">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                <input wire:model.live.debounce.300ms="search"
                    class="w-full pl-12 pr-4 py-3.5 md:py-3 bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-2xl font-medium text-sm text-gray-800 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:border-brand-blue focus:ring-4 focus:ring-brand-light dark:focus:ring-brand-blue/20 transition-all"
                    placeholder="Cari nama atau NIM..." type="text">
            </div>

            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                <select wire:model.live="fakultasFilter" class="w-full sm:w-auto md:w-44 px-4 py-3.5 md:py-3 bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-2xl font-medium text-sm text-gray-600 dark:text-gray-300 focus:outline-none focus:border-brand-blue focus:ring-4 focus:ring-brand-light dark:focus:ring-brand-blue/20 transition-all appearance-none cursor-pointer">
                    <option value="">Semua Fakultas</option>
                    @foreach ($this->fakultasList() as $fak)
                        <option value="{{ $fak }}">{{ $fak }}</option>
                    @endforeach
                </select>
                <select wire:model.live="prodiFilter" class="w-full sm:w-auto md:w-44 px-4 py-3.5 md:py-3 bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-2xl font-medium text-sm text-gray-600 dark:text-gray-300 focus:outline-none focus:border-brand-blue focus:ring-4 focus:ring-brand-light dark:focus:ring-brand-blue/20 transition-all appearance-none cursor-pointer">
                    <option value="">Semua Prodi</option>
                    @foreach ($this->prodiFilterList() as $p)
                        <option value="{{ $p }}">{{ $p }}</option>
                    @endforeach
                </select>
                <button wire:click="resetFilters"
                    class="w-full sm:w-12 md:w-11 h-12 md:h-11 flex items-center justify-center bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-2xl text-gray-500 dark:text-gray-400 hover:text-brand-blue dark:hover:text-white hover:border-brand-blue/30 dark:hover:border-gray-500 hover:bg-brand-light dark:hover:bg-gray-700 transition-colors flex-shrink-0 gap-2" title="Reset Filter">
                    <span class="material-symbols-outlined">refresh</span>
                    <span class="sm:hidden font-medium text-sm">Reset Filter</span>
                </button>
            </div>
        </div>

        <!-- Table Container -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[800px]">
                <thead>
                    <tr class="bg-gray-50/80 dark:bg-gray-800/80 border-b border-gray-100 dark:border-dark-border">
                        <th class="py-4 px-6 font-bold text-[11px] text-gray-400 dark:text-gray-500 uppercase tracking-wider w-16">Foto</th>
                        <th class="py-4 px-6 font-bold text-[11px] text-gray-400 dark:text-gray-500 uppercase tracking-wider">Biodata Anggota</th>
                        <th class="py-4 px-6 font-bold text-[11px] text-gray-400 dark:text-gray-500 uppercase tracking-wider">NIM / ID</th>
                        <th class="py-4 px-6 font-bold text-[11px] text-gray-400 dark:text-gray-500 uppercase tracking-wider">Fakultas & Prodi</th>
                        <th class="py-4 px-6 font-bold text-[11px] text-gray-400 dark:text-gray-500 uppercase tracking-wider text-right w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-dark-border">
                    @forelse ($members as $member)
                        <tr wire:key="member-{{ $member->id }}" class="hover:bg-brand-light/30 dark:hover:bg-gray-800/50 transition-colors group">
                            <td class="py-4 px-6">
                                @if ($member->foto_dobok)
                                    <img src="{{ asset($member->foto_dobok) }}" alt="Foto {{ $member->nama_lengkap }}" class="w-11 h-11 rounded-full object-cover shadow-md shrink-0">
                                @else
                                    <div class="w-11 h-11 rounded-full {{ $avatarColor($member->id) }} text-white flex items-center justify-center font-heading font-bold text-lg shadow-md">
                                        {{ strtoupper(mb_substr($member->nama_lengkap, 0, 1)) }}
                                    </div>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex flex-col">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-bold text-gray-900 dark:text-white text-sm">{{ $member->nama_lengkap }}</span>
                                        @if ($this->isIncomplete($member))
                                            <span class="inline-flex items-center gap-1 bg-amber-100 dark:bg-yellow-900/30 text-amber-700 dark:text-yellow-400 text-[10px] font-bold px-2 py-0.5 rounded-full" title="Data tidak lengkap - perlu dilengkapi">⚠️ Data Tidak Lengkap</span>
                                        @endif
                                        @if (blank($member->no_bpjs))
                                            <span class="material-symbols-outlined text-[18px] text-amber-500" title="Data belum lengkap (BPJS kosong)">warning</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-1.5 text-gray-500 dark:text-gray-400 mt-1">
                                        <span class="material-symbols-outlined text-[14px]">chat</span>
                                        <span class="text-xs font-medium">{{ $member->no_whatsapp }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex flex-col gap-1.5 items-start">
                                    <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full text-xs font-bold tracking-wide">{{ $member->nim }}</span>
                                    <span class="text-xs font-medium text-gray-400 dark:text-gray-500">{{ $member->id_anggota }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex flex-col">
                                    <span class="font-bold text-gray-900 dark:text-white text-sm">{{ $member->fakultas }}</span>
                                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 mt-0.5">{{ $member->program_studi }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="openView({{ $member->id }})" class="w-9 h-9 flex items-center justify-center bg-transparent text-gray-400 dark:text-gray-500 rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/30 hover:text-blue-600 dark:hover:text-blue-400 transition-colors" title="Lihat Detail">
                                        <span class="material-symbols-outlined text-[20px]">visibility</span>
                                    </button>
                                    <button wire:click="edit({{ $member->id }})" class="w-9 h-9 flex items-center justify-center bg-transparent text-gray-400 dark:text-gray-500 rounded-xl hover:bg-amber-50 dark:hover:bg-amber-900/30 hover:text-amber-500 dark:hover:text-amber-400 transition-colors" title="Edit Data">
                                        <span class="material-symbols-outlined text-[20px]">edit</span>
                                    </button>
                                    <button wire:click="openDelete({{ $member->id }})" class="w-9 h-9 flex items-center justify-center bg-transparent text-gray-400 dark:text-gray-500 rounded-xl hover:bg-red-50 dark:hover:bg-red-900/30 hover:text-red-500 dark:hover:text-red-400 transition-colors" title="Hapus Data">
                                        <span class="material-symbols-outlined text-[20px]">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="py-12 flex flex-col items-center justify-center text-center">
                                    <span class="material-symbols-outlined text-[64px] text-gray-300 dark:text-gray-600 mb-4">search_off</span>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Data tidak ditemukan</h3>
                                    <p class="text-sm text-gray-500 dark:text-dark-muted mt-1">Coba sesuaikan filter pencarian Anda.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-5 md:p-6 border-t border-gray-100 dark:border-dark-border flex flex-col sm:flex-row items-center justify-between text-sm text-gray-500 dark:text-dark-muted bg-white dark:bg-dark-card gap-4">
            <span class="font-medium text-xs md:text-sm">Menampilkan {{ $members->count() }} dari {{ $total }} data</span>
            <div class="flex gap-1.5">
                <button type="button" wire:key="page-prev" wire:click="previousPage" @disabled($members->onFirstPage())
                    class="px-3.5 py-2 md:px-3 md:py-1.5 border border-gray-200 dark:border-dark-border rounded-lg font-medium transition-colors {{ $members->onFirstPage() ? 'bg-gray-50 dark:bg-gray-800/50 text-gray-400 dark:text-gray-600 cursor-not-allowed' : 'bg-white dark:bg-dark-card hover:bg-gray-50 dark:hover:bg-gray-800 text-gray-600 dark:text-gray-300' }}">
                    Prev
                </button>
                @for ($i = 1; $i <= $members->lastPage(); $i++)
                    <button type="button" wire:key="page-{{ $i }}" wire:click="gotoPage({{ $i }})"
                        class="{{ $i === $members->currentPage() ? 'w-10 md:w-8 py-2 md:py-1.5 bg-brand-blue text-white rounded-lg font-bold shadow-md shadow-brand-blue/20' : 'w-10 md:w-8 py-2 md:py-1.5 bg-white dark:bg-dark-card border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-brand-blue dark:hover:text-white font-medium transition-colors' }}">
                        {{ $i }}
                    </button>
                @endfor
                <button type="button" wire:key="page-next" wire:click="nextPage" @disabled(!$members->hasMorePages())
                    class="px-3.5 py-2 md:px-3 md:py-1.5 border border-gray-200 dark:border-dark-border rounded-lg font-medium transition-colors {{ $members->hasMorePages() ? 'bg-white dark:bg-dark-card hover:bg-gray-50 dark:hover:bg-gray-800 text-gray-600 dark:text-gray-300' : 'bg-gray-50 dark:bg-gray-800/50 text-gray-400 dark:text-gray-600 cursor-not-allowed' }}">
                    Next
                </button>
            </div>
        </div>
    </div>

    <!-- Add/Edit Member Modal -->
    <div id="addMemberModal" class="modal-overlay fixed inset-0 bg-gray-900/60 dark:bg-black/80 backdrop-blur-sm z-[100] flex items-center justify-center p-4 @if($showForm) active @endif">
        <div class="modal-content bg-white dark:bg-dark-card rounded-3xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden flex flex-col">

            <div class="px-6 py-4 border-b border-gray-100 dark:border-dark-border flex items-center justify-between bg-gray-50/50 dark:bg-gray-800/50">
                <h2 class="font-heading font-bold text-xl text-gray-900 dark:text-white">{{ $editingId ? 'Edit Data Anggota' : 'Tambah Anggota Baru' }}</h2>
                <button wire:click="closeForm" class="text-gray-400 hover:text-gray-700 dark:hover:text-white transition-colors p-2 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-700">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="p-6 overflow-y-auto">
                <form wire:submit.prevent="save" class="space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="nama" value="{{ $nama }}" required placeholder="Masukkan nama lengkap" class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue outline-none transition-all placeholder-gray-400">
                            @error('nama') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">NIM <span class="text-red-500">*</span></label>
                            <input type="number" wire:model="nim" value="{{ $nim }}" required placeholder="Contoh: 2200182440" class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue outline-none transition-all placeholder-gray-400">
                            @error('nim') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Tanggal Lahir <span class="text-red-500">*</span></label>
                            <input type="date" wire:model="tglLahir" value="{{ $tglLahir }}" required class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue outline-none transition-all">
                            @error('tglLahir') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Jenis Kelamin <span class="text-red-500">*</span></label>
                            <select wire:model="jk" required class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue outline-none transition-all appearance-none cursor-pointer">
                                <option value="" disabled hidden @selected($jk === '')>Pilih Jenis Kelamin</option>
                                <option value="L" @selected($jk === 'L')>Laki-laki</option>
                                <option value="P" @selected($jk === 'P')>Perempuan</option>
                            </select>
                            @error('jk') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">No. WhatsApp <span class="text-red-500">*</span></label>
                            <input type="tel" wire:model="wa" value="{{ $wa }}" required placeholder="Contoh: 6281234567890" class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue outline-none transition-all placeholder-gray-400">
                            @error('wa') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Fakultas <span class="text-red-500">*</span></label>
                            <select wire:model.live="fakultas" required class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue outline-none transition-all appearance-none cursor-pointer">
                                <option value="" disabled hidden @selected($fakultas === '')>Pilih Fakultas</option>
                                @if ($editingId && ! in_array($fakultas, $this->fakultasList(), true))
                                    <option value="{{ $fakultas }}" selected>{{ $fakultas }}</option>
                                @endif
                                @foreach ($this->fakultasList() as $fak)
                                    <option value="{{ $fak }}" @selected($fakultas === $fak)>{{ $fak }}</option>
                                @endforeach
                            </select>
                            @error('fakultas') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Program Studi <span class="text-red-500">*</span></label>
                            <select wire:model.live="prodi" required class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue outline-none transition-all appearance-none cursor-pointer">
                                <option value="" disabled hidden @selected($prodi === '')>Pilih Program Studi</option>
                                @if ($editingId && $prodi !== '' && ! in_array($prodi, $this->prodiList(), true))
                                    <option value="{{ $prodi }}" selected>{{ $prodi }}</option>
                                @endif
                                @foreach ($this->prodiList() as $p)
                                    <option value="{{ $p }}" @selected($prodi === $p)>{{ $p }}</option>
                                @endforeach
                            </select>
                            @error('prodi') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">No. BPJS (Opsional)</label>
                            <input type="text" wire:model="bpjs" value="{{ $bpjs }}" placeholder="Masukkan No. BPJSTK" class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue outline-none transition-all placeholder-gray-400">
                            @error('bpjs') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Foto Berseragam Dobok -->
                    <div class="space-y-1.5 mt-4">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Foto Berseragam Dobok</label>
                        <div class="mt-1 flex flex-col justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-xl hover:border-brand-blue dark:hover:border-brand-light transition-colors group relative bg-gray-50/50 dark:bg-gray-800/30">
                            @if ($foto)
                                <div class="flex items-center justify-between w-full">
                                    <div class="flex items-center gap-3 overflow-hidden">
                                        <img src="{{ $foto->temporaryUrl() }}" class="w-10 h-10 rounded-lg object-cover shrink-0" alt="Preview">
                                        <div class="flex flex-col truncate">
                                            <span class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $foto->getClientOriginalName() }}</span>
                                            <span class="text-[10px] font-medium text-green-500 flex items-center gap-1"><span class="material-symbols-outlined text-[12px]">check_circle</span> Siap diunggah</span>
                                        </div>
                                    </div>
                                    <button type="button" wire:click="$set('foto', null)" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-full transition-colors shrink-0" title="Hapus foto">
                                        <span class="material-symbols-outlined text-[20px]">close</span>
                                    </button>
                                </div>
                            @elseif ($fotoLama)
                                <div class="flex items-center gap-3 overflow-hidden">
                                    <img src="{{ asset($fotoLama) }}" class="w-10 h-10 rounded-lg object-cover shrink-0" alt="Foto lama">
                                    <div class="flex flex-col truncate">
                                        <span class="text-sm font-semibold text-gray-900 dark:text-white truncate">Foto saat ini</span>
                                        <span class="text-[10px] font-medium text-gray-400">Pilih file baru untuk mengganti</span>
                                    </div>
                                </div>
                            @else
                                <div class="space-y-1 text-center cursor-pointer w-full h-full">
                                    <span class="material-symbols-outlined text-4xl text-gray-400 group-hover:text-brand-blue transition-colors">add_photo_alternate</span>
                                    <div class="flex text-sm text-gray-600 dark:text-gray-400 justify-center items-center">
                                        <span class="font-medium text-brand-blue hover:text-brand-hover">Upload file</span>
                                        <p class="pl-1">atau drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG up to 2MB</p>
                                </div>
                            @endif
                            <input type="file" wire:model="foto" accept="image/png, image/jpeg" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        </div>
                        @error('foto')
                            <p class="text-xs text-red-500 font-medium mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </form>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 dark:border-dark-border bg-gray-50/50 dark:bg-gray-800/50 flex justify-end gap-3">
                <button wire:click="closeForm" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">Batal</button>
                <button wire:click="save" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-brand-blue hover:bg-brand-hover shadow-md transition-colors">Simpan Data</button>
            </div>
        </div>
    </div>

    <!-- View Detail Modal -->
    @php $viewed = $this->viewingMember(); @endphp
    @if ($viewed)
        <div id="viewMemberModal" class="modal-overlay fixed inset-0 bg-gray-900/60 dark:bg-black/80 backdrop-blur-sm z-[100] flex items-center justify-center p-4 @if($showView) active @endif">
            <div class="modal-content bg-white dark:bg-dark-card rounded-3xl shadow-2xl w-full max-w-md overflow-hidden flex flex-col relative">
                <div class="bg-brand-blue h-32 relative">
                    <button wire:click="closeView" class="absolute top-4 right-4 text-white/80 hover:text-white bg-black/20 hover:bg-black/40 rounded-full w-8 h-8 flex items-center justify-center transition-colors">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>

                <div class="px-6 pb-8 relative -mt-16">
                    @if ($viewed->foto_dobok)
                        <img src="{{ asset($viewed->foto_dobok) }}" alt="Foto {{ $viewed->nama_lengkap }}" class="w-28 h-28 mx-auto rounded-full object-cover border-[6px] border-white dark:border-dark-card shadow-md z-10 relative">
                    @else
                        <div class="w-28 h-28 mx-auto rounded-full {{ $avatarColor($viewed->id) }} border-[6px] border-white dark:border-dark-card text-white flex items-center justify-center font-heading font-bold text-5xl shadow-md z-10 relative">
                            {{ strtoupper(mb_substr($viewed->nama_lengkap, 0, 1)) }}
                        </div>
                    @endif

                    <div class="text-center mt-4 mb-6">
                        <h3 class="font-heading font-extrabold text-2xl text-gray-900 dark:text-white leading-tight">{{ $viewed->nama_lengkap }}</h3>
                        <p class="text-sm font-medium text-brand-blue dark:text-brand-light mt-1.5">{{ $viewed->nim }} · {{ $viewed->id_anggota }}</p>
                    </div>

                    <div class="bg-gray-50/80 dark:bg-gray-800/80 p-6 rounded-2xl border border-gray-100 dark:border-gray-700">
                        <div class="grid grid-cols-2 gap-y-6 gap-x-4 text-sm">
                            <div>
                                <p class="text-gray-500 dark:text-gray-400 text-[11px] uppercase tracking-wider font-bold mb-1.5">Fakultas / Prodi</p>
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $viewed->fakultas }} · {{ $viewed->program_studi }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 dark:text-gray-400 text-[11px] uppercase tracking-wider font-bold mb-1.5">No. WhatsApp</p>
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $viewed->no_whatsapp }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 dark:text-gray-400 text-[11px] uppercase tracking-wider font-bold mb-1.5">Tgl Lahir / Gender</p>
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $viewed->tanggal_lahir?->format('d/m/Y') }} · {{ $viewed->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 dark:text-gray-400 text-[11px] uppercase tracking-wider font-bold mb-1.5">No. BPJS</p>
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $viewed->no_bpjs ?: '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex flex-col items-center justify-center">
                        <p class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-3">QR Code Keanggotaan</p>
                        <div class="p-2.5 bg-white border border-gray-100 dark:border-gray-700 rounded-2xl shadow-sm mb-3">
                            <img src="{{ asset($viewed->qr_code) }}" alt="QR {{ $viewed->id_anggota }}" class="w-36 h-36 rounded-xl">
                        </div>
                        <a href="{{ asset($viewed->qr_code) }}" download="QR_{{ $viewed->id_anggota }}.svg" class="flex items-center gap-1.5 px-4 py-2 bg-brand-light dark:bg-brand-blue/20 text-brand-blue dark:text-brand-light rounded-xl font-semibold text-xs hover:bg-blue-100 dark:hover:bg-brand-blue/30 transition-colors">
                            <span class="material-symbols-outlined text-[18px]">download</span>
                            Download QR
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Confirm Delete Modal -->
    @php $deleted = $this->deletingMember(); @endphp
    @if ($deleted)
        <div id="deleteModal" class="modal-overlay fixed inset-0 bg-gray-900/60 dark:bg-black/80 backdrop-blur-sm z-[100] flex items-center justify-center p-4 @if($showDelete) active @endif">
            <div class="modal-content bg-white dark:bg-dark-card rounded-3xl shadow-2xl w-full max-w-sm p-6 text-center">
                <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-[32px]">warning</span>
                </div>
                <h3 class="font-heading font-bold text-lg text-gray-900 dark:text-white mb-2">Hapus Data Anggota?</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Data <strong class="text-gray-900 dark:text-white">{{ $deleted->nama_lengkap }}</strong> akan dihapus permanen dan tidak dapat dikembalikan.</p>

                <div class="flex gap-3 justify-center">
                    <button wire:click="closeDelete" class="flex-1 px-4 py-2.5 rounded-xl text-sm font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">Batal</button>
                    <button wire:click="confirmDelete" class="flex-1 px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-red-500 hover:bg-red-600 shadow-md transition-colors">Ya, Hapus</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Import Data Modal -->
    <div id="importModal" class="modal-overlay fixed inset-0 bg-gray-900/60 dark:bg-black/80 backdrop-blur-sm z-[100] flex items-center justify-center p-4 @if($showImport) active @endif">
        <div class="modal-content bg-white dark:bg-dark-card rounded-3xl shadow-2xl w-full max-w-md overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-dark-border flex items-center justify-between bg-gray-50/50 dark:bg-gray-800/50">
                <h2 class="font-heading font-bold text-xl text-gray-900 dark:text-white">Import Data Anggota</h2>
                <button wire:click="closeImport" class="text-gray-400 hover:text-gray-700 dark:hover:text-white transition-colors p-2 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-700">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="p-6 space-y-5">
                <div class="w-full bg-white dark:bg-dark-card border-2 border-gray-200 dark:border-gray-700 border-dashed rounded-2xl p-6 text-center cursor-pointer hover:bg-brand-light/50 dark:hover:bg-gray-800 hover:border-brand-blue/50 dark:hover:border-brand-blue/50 transition-colors flex flex-col items-center justify-center gap-2 relative overflow-hidden group">
                    <input type="file" wire:model="importFile" accept=".xlsx,.xls,.csv" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                    <div class="w-12 h-12 rounded-full bg-gray-50 dark:bg-gray-800 text-gray-400 flex items-center justify-center mb-1 group-hover:bg-white dark:group-hover:bg-gray-700 group-hover:text-brand-blue transition-colors shadow-sm">
                        <span class="material-symbols-outlined text-[24px]">file_upload</span>
                    </div>
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">@if ($importFile) {{ $importFile->getClientOriginalName() }} @else Ketuk untuk unggah file Excel/CSV @endif</span>
                    <span class="text-[10px] text-gray-400 dark:text-gray-500">Format: NAMA LENGKAP | NIM | TANGGAL LAHIR | JENIS KELAMIN | NO WHATSAPP | FAKULTAS | PROGRAM STUDI | NO BPJS</span>
                </div>
                @error('importFile')
                    <p class="text-xs text-red-500 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="px-6 py-4 border-t border-gray-100 dark:border-dark-border bg-gray-50/50 dark:bg-gray-800/50 flex justify-end gap-3">
                <button wire:click="closeImport" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">Batal</button>
                <button wire:click="import" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-brand-blue hover:bg-brand-hover shadow-md transition-colors">Import Data</button>
            </div>
        </div>
    </div>
</div>
