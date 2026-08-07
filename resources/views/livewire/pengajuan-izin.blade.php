<div class="space-y-6 md:space-y-8">
    <!-- Header + Aksi -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="font-heading font-extrabold text-3xl text-gray-900 dark:text-white tracking-tight">Riwayat Izin & Sakit</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 font-medium">Ajukan izin/sakit dan pantau status pengajuan Anda.</p>
        </div>
        <button type="button" wire:click="toggleForm(true)"
            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 bg-brand-blue text-white rounded-xl md:rounded-full font-semibold text-sm hover:bg-brand-hover shadow-lg shadow-brand-blue/30 transition-colors focus:outline-none">
            <span class="material-symbols-outlined text-[20px]">add_circle</span>
            Ajukan Izin/Sakit
        </button>
    </div>

    <!-- Daftar Riwayat -->
    <div class="flex flex-col gap-4">
        @forelse ($pengajuan as $izin)
            @php
                $isSakit = $izin->jenis === 'sakit';
            @endphp
            <div wire:key="izin-{{ $izin->id }}" class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-3xl p-5 shadow-card flex items-center justify-between animate-[fadeIn_0.3s_ease-in-out] transition-colors">
                <div class="flex items-center gap-4 min-w-0">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 transition-colors {{ $isSakit ? 'bg-yellow-50 dark:bg-yellow-900/30 text-yellow-500 dark:text-yellow-400' : 'bg-blue-50 dark:bg-blue-900/30 text-blue-500 dark:text-blue-400' }}">
                        <span class="material-symbols-outlined text-[24px]">{{ $isSakit ? 'medical_services' : 'flight_takeoff' }}</span>
                    </div>
                    <div class="min-w-0">
                        <h3 class="font-bold text-gray-900 dark:text-white text-sm flex items-center gap-1.5 transition-colors">
                            Pengajuan {{ $isSakit ? 'Sakit' : 'Izin' }}
                            <x-status-badge :status="$izin->status" />
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-medium transition-colors">{{ $izin->tanggal?->translatedFormat('l, d M Y') }}</p>
                        @if ($izin->status === 'menunggu')
                            <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5 font-medium">Diajukan: {{ $izin->diajukan_pada?->format('d M Y H:i') }}</p>
                        @endif
                    </div>
                </div>
                @if ($izin->status === 'menunggu')
                    <button type="button" wire:click="batalkan({{ $izin->id }})" class="w-10 h-10 rounded-full bg-red-50 dark:bg-red-900/20 flex items-center justify-center text-red-500 dark:text-red-400 hover:bg-red-500 hover:text-white dark:hover:bg-red-500 dark:hover:text-white transition-colors shrink-0 focus:outline-none" title="Batalkan Pengajuan">
                        <span class="material-symbols-outlined text-[20px]">delete</span>
                    </button>
                @endif
            </div>
        @empty
            <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-3xl p-10 shadow-card text-center">
                <span class="material-symbols-outlined text-[48px] text-gray-300 dark:text-gray-600 mb-3 block">event_busy</span>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">Belum ada pengajuan izin/sakit.</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Klik "Ajukan Izin/Sakit" untuk membuat pengajuan baru.</p>
            </div>
        @endforelse

        <div class="mt-2">
            {{ $pengajuan->links() }}
        </div>
    </div>

    <!-- Modal Pengajuan Izin/Sakit -->
    @if ($showForm)
        <div class="fixed inset-0 z-[60] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-gray-900/40 dark:bg-gray-900/70 backdrop-blur-sm transition-opacity" wire:click="toggleForm(false)"></div>
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl w-full max-w-md relative z-10 animate-[fadeIn_0.2s_ease-out] overflow-hidden flex flex-col max-h-[90vh] transition-colors duration-300">

            <div class="p-5 md:p-6 border-b border-gray-50 dark:border-gray-700 flex justify-between items-start bg-white dark:bg-gray-800 transition-colors">
                <div>
                    <h3 class="font-heading font-extrabold text-gray-900 dark:text-white text-xl">Formulir Pengajuan</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-1">Batas pengajuan maks. 2 jam sebelum latihan.</p>
                </div>
                <button type="button" wire:click="toggleForm(false)" class="text-gray-400 hover:text-red-500 dark:hover:text-red-400 bg-gray-50 dark:bg-gray-700 hover:bg-red-50 dark:hover:bg-red-900/20 p-2 rounded-full transition-colors focus:outline-none">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <div class="p-5 md:p-6 overflow-y-auto custom-scrollbar bg-gray-50/50 dark:bg-gray-800/50 transition-colors">
                <form wire:submit.prevent="save" class="space-y-5">
                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Tanggal Berhalangan</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">calendar_today</span>
                            <input type="date" required wire:model.live="tanggal" class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl pl-10 pr-4 py-3 text-sm font-medium text-gray-700 dark:text-white focus:outline-none focus:ring-4 focus:ring-brand-light dark:focus:ring-brand-blue/20 focus:border-brand-blue transition-all">
                        </div>
                        @if ($jadwalInfo)
                            <p class="text-xs font-medium text-brand-blue dark:text-brand-light mt-1.5">{{ $jadwalInfo }}</p>
                        @endif
                        @if ($batasInfo)
                            <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">Batas akhir pengajuan: <strong class="text-gray-700 dark:text-gray-300">{{ $batasInfo }}</strong></p>
                        @endif
                        @error('tanggal')
                            <p class="text-xs text-red-500 dark:text-red-400 font-semibold mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Keterangan</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="relative flex flex-col items-center gap-2 p-4 border border-gray-200 dark:border-gray-700 rounded-2xl bg-white dark:bg-gray-900 cursor-pointer hover:border-blue-300 dark:hover:border-blue-600 transition-all has-[:checked]:border-blue-500 dark:has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50 dark:has-[:checked]:bg-blue-900/20 has-[:checked]:ring-2 has-[:checked]:ring-blue-100 dark:has-[:checked]:ring-blue-900/50 group">
                                <input type="radio" name="jenis" value="izin" required wire:model="jenis" class="absolute opacity-0">
                                <div class="w-8 h-8 rounded-full bg-blue-50 dark:bg-gray-800 text-blue-500 dark:text-gray-400 flex items-center justify-center group-has-[:checked]:bg-blue-500 group-has-[:checked]:text-white transition-colors">
                                    <span class="material-symbols-outlined text-[18px]">flight_takeoff</span>
                                </div>
                                <span class="text-sm font-bold text-gray-700 dark:text-gray-300 group-has-[:checked]:text-blue-700 dark:group-has-[:checked]:text-blue-400">Izin</span>
                            </label>

                            <label class="relative flex flex-col items-center gap-2 p-4 border border-gray-200 dark:border-gray-700 rounded-2xl bg-white dark:bg-gray-900 cursor-pointer hover:border-yellow-300 dark:hover:border-yellow-600 transition-all has-[:checked]:border-yellow-500 dark:has-[:checked]:border-yellow-500 has-[:checked]:bg-yellow-50 dark:has-[:checked]:bg-yellow-900/20 has-[:checked]:ring-2 has-[:checked]:ring-yellow-100 dark:has-[:checked]:ring-yellow-900/50 group">
                                <input type="radio" name="jenis" value="sakit" wire:model="jenis" class="absolute opacity-0">
                                <div class="w-8 h-8 rounded-full bg-yellow-50 dark:bg-gray-800 text-yellow-500 dark:text-gray-400 flex items-center justify-center group-has-[:checked]:bg-yellow-500 group-has-[:checked]:text-white transition-colors">
                                    <span class="material-symbols-outlined text-[18px]">medical_services</span>
                                </div>
                                <span class="text-sm font-bold text-gray-700 dark:text-gray-300 group-has-[:checked]:text-yellow-700 dark:group-has-[:checked]:text-yellow-400">Sakit</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Alasan Lengkap</label>
                        <textarea wire:model="keterangan" rows="3" placeholder="Tuliskan alasan secara detail..." class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-sm font-medium text-gray-700 dark:text-white focus:outline-none focus:ring-4 focus:ring-brand-light dark:focus:ring-brand-blue/20 focus:border-brand-blue transition-all resize-none"></textarea>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Lampiran Bukti (Opsional)</label>
                        <div class="w-full bg-white dark:bg-gray-900 border-2 border-gray-200 dark:border-gray-700 border-dashed rounded-2xl p-5 text-center cursor-pointer hover:bg-brand-light/50 dark:hover:bg-gray-800 hover:border-brand-blue/50 dark:hover:border-brand-blue/50 transition-colors flex flex-col items-center justify-center gap-2 relative overflow-hidden group">
                            <input type="file" wire:model="bukti" accept="image/*,.pdf" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            <div class="w-12 h-12 rounded-full bg-gray-50 dark:bg-gray-800 text-gray-400 flex items-center justify-center mb-1 group-hover:bg-white dark:group-hover:bg-gray-700 group-hover:text-brand-blue transition-colors shadow-sm">
                                <span class="material-symbols-outlined text-[24px]">cloud_upload</span>
                            </div>
                            <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">@if ($bukti) {{ $bukti->getClientOriginalName() }} @else Ketuk untuk unggah gambar/PDF (Max 2MB) @endif</span>
                        </div>
                        @error('bukti')
                            <p class="text-xs text-red-500 font-medium mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </form>
            </div>

            <div class="p-5 border-t border-gray-50 dark:border-gray-700 flex gap-3 bg-white dark:bg-gray-800 transition-colors">
                <button type="button" wire:click="toggleForm(false)" class="flex-1 py-3 px-4 rounded-xl border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 text-sm font-bold hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors focus:outline-none">Batal</button>
                <button type="button" wire:click="save" class="flex-1 py-3 px-4 rounded-xl bg-brand-blue text-white text-sm font-bold hover:bg-brand-hover transition-colors shadow-md shadow-brand-blue/20 focus:outline-none">Kirim Pengajuan</button>
            </div>
        </div>
    </div>
    @endif
</div>