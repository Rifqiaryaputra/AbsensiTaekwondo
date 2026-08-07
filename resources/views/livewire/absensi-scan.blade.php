<div wire:poll.5s="refreshJadwal" x-data="{ showCloseModal: false }">
    <style>
        /* Pastikan feed kamera memenuhi seluruh area scanner */
        #qr-reader { position: relative; }
        #qr-reader > div { width: 100% !important; height: 100% !important; }
        #qr-reader video { width: 100% !important; height: 100% !important; object-fit: cover; border-radius: 0.75rem; }
    </style>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 xl:gap-8 items-start">

        <div class="xl:col-span-5 flex flex-col gap-5 md:gap-6 w-full">
            <!-- Active Schedule Alert -->
            @if ($jadwalInfo)
                <!-- Wrapper utama: flex-col di mobile, flex-row di desktop (md:) -->
                <div class="bg-[#F8FAFC] border border-[#E5E9F2] rounded-[1.5rem] p-4 md:p-5 flex flex-col md:flex-row md:items-center justify-between gap-4 md:gap-6 mb-6">
                    
                    <!-- Bagian Kiri: Ikon & Teks -->
                    <div class="flex items-center gap-4">
                        <!-- Lingkaran Ikon Lonceng -->
                        <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-[#3D5EE1] shadow-[0_2px_10px_rgba(0,0,0,0.04)] shrink-0">
                            <!-- Ikon Lonceng (Solid) -->
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"></path>
                            </svg>
                        </div>
                        
                        <!-- Teks Informasi -->
                        <div class="flex flex-col">
                            <h3 class="text-[14px] md:text-[15px] font-bold text-[#2A44B6] leading-snug md:max-w-[280px]">
                                Jadwal Aktif: {{ $jadwalInfo }}
                            </h3>
                            <p class="text-[12px] md:text-[13px] font-medium text-[#7A93F5] mt-0.5">
                                (Batas tutup absen: {{ $batasTutup }})
                            </p>
                        </div>
                    </div>

                    <!-- Bagian Kanan: Tombol -->
                    <button type="button" @click="showCloseModal = true" 
                        class="w-full md:w-auto bg-[#EF4444] hover:bg-red-600 text-white font-bold py-3 md:py-2.5 px-6 rounded-2xl flex items-center justify-center gap-2 shadow-[0_8px_20px_rgba(239,68,68,0.3)] transition-all shrink-0">
                        <!-- Ikon Gembok (Lock) -->
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                        </svg>
                        Tutup Absen
                    </button>
                </div>
            @elseif ($isLibur)
                @php \Carbon\Carbon::setLocale('id'); @endphp
                <div class="bg-red-50 dark:bg-red-900/20 rounded-2xl md:rounded-3xl p-4 md:p-5 flex items-start gap-4 border border-red-100 dark:border-red-900/30 transition-colors duration-300">
                    <div class="w-10 h-10 shrink-0 rounded-full bg-red-100 dark:bg-red-900/50 flex items-center justify-center text-red-600 dark:text-red-400">
                        <span class="material-symbols-outlined text-[20px]">event_busy</span>
                    </div>
                    <div>
                        <h3 class="text-red-700 dark:text-red-400 font-bold text-sm md:text-base">Pemberitahuan Libur</h3>
                        <p class="text-red-600 dark:text-red-400/80 text-xs md:text-sm font-medium mt-1">
                            Latihan ditiadakan pada <strong>{{ \Carbon\Carbon::parse($tanggalLibur)->translatedFormat('l, d F Y') }}</strong> ({{ $namaLibur ?: 'Libur' }}).
                        </p>
                    </div>
                </div>
            @elseif ($sesiMode === 'koreksi')
                <div class="bg-brand-light dark:bg-brand-blue/20 rounded-2xl md:rounded-3xl p-4 md:p-5 flex items-center gap-4 border border-brand-blue/10 dark:border-brand-blue/30 transition-colors duration-300">
                    <div class="w-10 h-10 rounded-full bg-white dark:bg-gray-800 flex items-center justify-center text-brand-blue dark:text-blue-400 shrink-0 shadow-sm">
                        <span class="material-symbols-outlined">update</span>
                    </div>
                    <div>
                        <p class="font-bold text-brand-blue dark:text-blue-300 text-sm">Sesi Perbaikan Absen Dibuka</p>
                        <p class="text-xs text-brand-blue/70 dark:text-blue-400/70 font-semibold mt-1">Koreksi status hanya tersedia pukul 12.00 - 13.00 WIB pada hari ini.</p>
                    </div>
                </div>
            @else
                <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-2xl md:rounded-3xl p-4 md:p-5 flex items-center gap-4 border border-yellow-100 dark:border-yellow-900/30 transition-colors duration-300">
                    <div class="w-10 h-10 rounded-full bg-white dark:bg-gray-800 flex items-center justify-center text-yellow-600 dark:text-yellow-400 shrink-0 shadow-sm">
                        <span class="material-symbols-outlined">event_busy</span>
                    </div>
                    @if ($jadwalHariIniInfo)
                        <div>
                            <p class="font-semibold text-sm text-yellow-700 dark:text-yellow-400">Jadwal hari ini: {{ $jadwalHariIniInfo }} <span class="capitalize">({{ $jadwalHariIniStatus }})</span></p>
                            <p class="text-xs text-yellow-600/80 dark:text-yellow-300 font-medium mt-1">Absensi hanya dibuka pada rentang jam jadwal. Halaman akan aktif otomatis saat jam mulai.</p>
                        </div>
                    @else
                        <p class="font-semibold text-sm text-yellow-700 dark:text-yellow-400">Tidak ada jadwal latihan hari ini.</p>
                    @endif
                </div>
            @endif

            <!-- QR Scanner & Input Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl md:rounded-3xl p-5 md:p-6 shadow-card flex flex-col gap-6 transition-colors duration-300">
                <div class="relative overflow-hidden rounded-2xl md:rounded-3xl">
                    <div id="qr-reader" wire:ignore class="w-full aspect-square [&>div]:h-full [&>div]:w-full [&_video]:h-full [&_video]:w-full [&_video]:object-cover"></div>
                </div>

                <!-- Manual Input Form -->
                <div class="flex flex-col sm:flex-row gap-3 w-full items-stretch sm:items-center">
                    <div class="relative flex-1 w-full">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 dark:text-gray-500">
                            <span class="material-symbols-outlined text-[20px]">badge</span>
                        </div>
                        <input wire:model="nim" wire:keydown.enter="processManualInput"
                            class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 focus:border-brand-blue dark:focus:border-blue-500 focus:ring-4 focus:ring-brand-light dark:focus:ring-brand-blue/20 rounded-xl md:rounded-2xl pl-11 pr-4 py-3.5 text-sm font-semibold text-gray-800 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 outline-none transition-all"
                            placeholder="Atau ketik NIM manual..." type="text">
                    </div>
                    <button wire:click="processManualInput"
                        class="bg-brand-blue text-white px-7 py-3.5 rounded-xl md:rounded-2xl font-semibold text-sm hover:bg-brand-hover shadow-lg shadow-brand-blue/30 transition-all flex items-center justify-center gap-2">
                        Input
                    </button>
                </div>
            </div>

            <!-- Statistics Wrapper Box -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl md:rounded-3xl p-5 md:p-6 shadow-card transition-colors duration-300">
                <div class="grid grid-cols-2 gap-3 md:gap-4">
                            <!-- Hadir -->
                    <div class="stat-card bg-status-hadir-bg dark:bg-green-900/20 rounded-2xl md:rounded-3xl p-5 flex flex-col items-center justify-center text-center transition-all hover:-translate-y-1 duration-300">
                        <span id="countHadir" class="font-heading font-extrabold text-[42px] leading-none text-status-hadir-text dark:text-green-400 mb-2">{{ $this->countStatus('hadir') }}</span>
                        <p class="text-[15px] font-bold text-slate-700 dark:text-slate-200 leading-tight">Hadir</p>
                    </div>
                            
                     <!-- Izin -->
                    <div class="stat-card bg-status-izin-bg dark:bg-blue-900/20 rounded-2xl md:rounded-3xl p-5 flex flex-col items-center justify-center text-center transition-all hover:-translate-y-1 duration-300">
                        <span id="countIzin" class="font-heading font-extrabold text-[42px] leading-none text-status-izin-text dark:text-blue-400 mb-2">{{ $this->countStatus('izin') }}</span>
                        <p class="text-[15px] font-bold text-slate-700 dark:text-slate-200 leading-tight">Izin</p>
                    </div>

                    <!-- Sakit -->
                    <div class="stat-card bg-status-sakit-bg dark:bg-yellow-900/20 rounded-2xl md:rounded-3xl p-5 flex flex-col items-center justify-center text-center transition-all hover:-translate-y-1 duration-300">
                        <span id="countSakit" class="font-heading font-extrabold text-[42px] leading-none text-status-sakit-text dark:text-yellow-400 mb-2">{{ $this->countStatus('sakit') }}</span>
                        <p class="text-[15px] font-bold text-slate-700 dark:text-slate-200 leading-tight">Sakit</p>
                    </div>

                    <!-- Alfa -->
                    <div class="stat-card bg-status-alfa-bg dark:bg-red-900/20 rounded-2xl md:rounded-3xl p-5 flex flex-col items-center justify-center text-center transition-all hover:-translate-y-1 duration-300">
                        <span id="countAlfa" class="font-heading font-extrabold text-[42px] leading-none text-status-alfa-text dark:text-red-400 mb-2">{{ $this->countStatus('alfa') }}</span>
                        <p class="text-[15px] font-bold text-slate-700 dark:text-slate-200 leading-tight">Alfa</p>    
                    </div>
                </div>
            </div>
        </div>

        <div class="xl:col-span-7 bg-white dark:bg-gray-800 rounded-2xl md:rounded-3xl p-6 md:p-8 shadow-card flex flex-col border border-gray-50/50 dark:border-gray-700/50 transition-colors duration-300">
            @if ($sesiMode === 'terkunci')
                <div class="flex flex-col items-center justify-center flex-grow text-center py-12">
                    <div class="w-14 h-14 rounded-2xl bg-brand-light dark:bg-brand-blue/20 flex items-center justify-center text-brand-blue dark:text-blue-400 mb-4">
                        <span class="material-symbols-outlined">lock</span>
                    </div>
                    <p class="font-heading font-bold text-base text-gray-900 dark:text-white">Sesi perbaikan absen telah selesai dan data telah dikunci.</p>
                    <p class="text-xs text-gray-400 dark:text-gray-400 font-medium mt-1">Daftar Hadir Sementara tidak dapat diubah lagi.</p>
                </div>
            @else
            <div class="flex items-center justify-between mb-6 md:mb-8">
                <div>
                    <h3 class="font-heading font-bold text-base md:text-lg text-gray-900 dark:text-white">Daftar Hadir Sementara</h3>
                    <p class="text-xs text-gray-400 dark:text-gray-400 font-medium mt-1">Log presensi terbaru akan muncul paling atas</p>
                </div>
            </div>

            <div class="overflow-x-auto flex-grow -mx-4 md:mx-0 px-4 md:px-0">
                <table class="w-full text-left border-collapse min-w-[500px]">
                    <thead>
                        <tr>
                            <th class="px-4 py-4 font-bold text-xs text-gray-400 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 w-16">No</th>
                            <th class="px-4 py-4 font-bold text-xs text-gray-400 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">Nama / NIM</th>
                            <th class="px-4 py-4 font-bold text-xs text-gray-400 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 w-32">Status</th>
                            <th class="px-4 py-4 font-bold text-xs text-gray-400 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 w-24 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                        @forelse ($records as $index => $record)
                            <tr wire:key="absensi-{{ $record->id }}" class="hover:bg-gray-50/50 dark:hover:bg-gray-700/50 transition-colors group">
                                <td class="px-4 py-4 text-sm font-bold text-gray-400 dark:text-gray-500">{{ $index + 1 }}</td>
                                <td class="px-4 py-4">
                                    <p class="font-bold text-gray-900 dark:text-white text-sm">{{ $record->anggota?->nama_lengkap ?? '-' }}</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-400 font-medium mt-0.5">{{ $record->anggota?->nim }}</p>
                                </td>
                                <td class="px-4 py-4">
                                    <x-status-badge :status="$record->status" />
                                </td>
                                <td class="px-4 py-4 text-center">
                                    @if ($this->canEditAbsensi())
                                        <button type="button" onclick="openEditStatusModal({{ $record->id }}, '{{ addslashes($record->anggota?->nama_lengkap ?? '') }}')"
                                            class="w-8 h-8 mx-auto rounded-xl bg-transparent text-gray-400 dark:text-gray-400 flex items-center justify-center hover:bg-brand-light dark:hover:bg-gray-600 hover:text-brand-blue dark:hover:text-blue-400 transition-colors opacity-50 group-hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-brand-blue/50"
                                            title="Edit Status">
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-12 text-center text-sm font-medium text-gray-400 dark:text-gray-500">
                                    Belum ada data absensi untuk sesi ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    @if ($this->canEditAbsensi())
    <!-- Edit Status Modal -->
    <div id="editStatusModal" class="modal-overlay fixed inset-0 z-[60] flex items-center justify-center px-4">
        <div class="absolute inset-0 bg-gray-900/40 dark:bg-gray-900/70 backdrop-blur-sm transition-opacity" onclick="closeModal('editStatusModal')"></div>
        <div class="modal-content relative bg-white dark:bg-gray-800 rounded-3xl w-full max-w-sm shadow-2xl overflow-hidden border border-gray-100 dark:border-gray-700">
            <div class="px-6 py-5 flex items-center justify-between border-b border-gray-50 dark:border-gray-700/50">
                <h3 class="font-heading font-bold text-lg text-gray-900 dark:text-white">Edit Status Kehadiran</h3>
                <button type="button" onclick="closeModal('editStatusModal')" class="w-8 h-8 rounded-xl bg-blue-50 dark:bg-gray-700/50 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white flex items-center justify-center transition-colors focus:outline-none">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
            <div class="px-6 py-5">
                <p id="modalStudentName" class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-5">Nama Anggota</p>
                <input type="hidden" id="modalAbsensiId">
                <div class="space-y-3 flex flex-col items-center">
                    <button type="button" onclick="updateStatus('hadir')" class="w-full flex items-center justify-center gap-2 py-3.5 px-4 rounded-xl border border-green-500/50 dark:border-green-500/30 text-green-600 dark:text-green-400 font-semibold text-sm hover:bg-green-50 dark:hover:bg-green-900/20 transition-colors">
                        <span class="material-symbols-outlined text-[18px]">check_circle</span>
                        Hadir
                    </button>
                    <button type="button" onclick="updateStatus('izin')" class="w-full flex items-center justify-center gap-2 py-3.5 px-4 rounded-xl border border-blue-400/50 dark:border-blue-500/30 text-blue-500 dark:text-blue-400 font-semibold text-sm hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors">
                        <span class="material-symbols-outlined text-[18px]">edit_calendar</span>
                        Izin
                    </button>
                    <button type="button" onclick="updateStatus('sakit')" class="w-full flex items-center justify-center gap-2 py-3.5 px-4 rounded-xl border border-yellow-400/60 dark:border-yellow-500/30 text-yellow-500 dark:text-yellow-400 font-semibold text-sm hover:bg-yellow-50 dark:hover:bg-yellow-900/20 transition-colors">
                        <span class="material-symbols-outlined text-[18px]">medical_services</span>
                        Sakit
                    </button>
                    <button type="button" onclick="updateStatus('alfa')" class="w-full flex items-center justify-center gap-2 py-3.5 px-4 rounded-xl border border-red-400/50 dark:border-red-500/30 text-red-500 dark:text-red-400 font-semibold text-sm hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                        <span class="material-symbols-outlined text-[18px]">cancel</span>
                        Alfa
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Tutup Absen Confirmation Modal -->
    @if ($jadwalId)
    <template x-teleport="body">
        <div x-cloak x-show="showCloseModal" x-transition.opacity.duration.300ms
            class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 backdrop-blur-sm px-4">
            <div class="absolute inset-0" @click="showCloseModal = false"></div>
            <div x-cloak x-show="showCloseModal" x-transition.scale.origin.center.duration.200ms
                class="relative bg-white rounded-[2rem] p-6 sm:p-8 max-w-sm w-full text-center shadow-2xl">
                <div class="bg-red-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 text-red-500">
                    <svg class="w-8 h-8" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-extrabold text-gray-900 mb-2">Tutup Sesi Absensi?</h3>
                <p class="text-sm text-gray-500 mb-8">Tindakan ini akan menghentikan proses absensi secara permanen. Anggota yang berstatus <strong class="text-gray-700">Belum</strong> akan otomatis diubah menjadi <strong class="text-gray-700">Alfa</strong>.</p>
                <div class="flex flex-col-reverse sm:flex-row gap-3">
                    <button type="button" @click="showCloseModal = false"
                        class="w-full py-3.5 px-4 bg-white border border-gray-300 rounded-xl text-sm font-bold text-gray-700 hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <form action="{{ route('jadwal.tutup', $jadwalId) }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit"
                            class="w-full py-3.5 px-4 bg-[#EF4444] hover:bg-red-600 text-white rounded-xl text-sm font-bold shadow-[0_8px_20px_rgba(239,68,68,0.25)] flex items-center justify-center gap-2 transition-all">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/>
                            </svg>
                            Ya, Tutup
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </template>
    @endif

    @push('scripts')
        <script>
            window.openEditStatusModal = function (absensiId, name) {
                document.getElementById('modalStudentName').textContent = name;
                document.getElementById('modalAbsensiId').value = absensiId;
                openModal('editStatusModal');
            };

            window.updateStatus = function (status) {
                // parseInt digunakan untuk mengubah tipe data string ("52") menjadi integer murni (52)
                const absensiId = parseInt(document.getElementById('modalAbsensiId').value);
                
                Livewire.dispatch('changeStatus', { absensiId: absensiId, status: status });
                closeModal('editStatusModal');
            };
        </script>
    @endpush
</div>