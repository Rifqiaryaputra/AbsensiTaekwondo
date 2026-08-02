<div wire:poll.5s="refreshJadwal">
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 xl:gap-8 items-start">

        <div class="xl:col-span-5 flex flex-col gap-5 md:gap-6 w-full">
            <!-- Active Schedule Alert -->
            @if ($jadwalInfo)
                <div class="bg-brand-light dark:bg-brand-blue/20 rounded-2xl md:rounded-3xl p-4 md:p-5 flex items-center justify-center md:justify-start gap-4 border border-brand-blue/10 dark:border-brand-blue/30 transition-colors duration-300">
                    <div class="w-10 h-10 rounded-full bg-white dark:bg-gray-800 flex items-center justify-center text-brand-blue dark:text-blue-400 shrink-0 shadow-sm transition-colors duration-300">
                        <span class="material-symbols-outlined">notifications_active</span>
                    </div>
                    <div>
                        <p class="font-bold text-brand-blue dark:text-blue-300 text-sm">Jadwal Aktif: {{ $jadwalInfo }}</p>
                        <p class="text-xs text-brand-blue/70 dark:text-blue-400/70 font-semibold mt-1">(Batas tutup absen: {{ $batasTutup }})</p>
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
                <div class="bg-gray-900 rounded-2xl md:rounded-3xl relative overflow-hidden shadow-inner">
                    <div id="qr-reader" wire:ignore class="w-full aspect-square [&_video]:rounded-2xl"></div>
                    <p id="qr-reader-hint" class="text-center text-white/50 text-xs font-medium tracking-wider uppercase py-3">Arahkan QR Code anggota ke kamera...</p>
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

            <!-- Stats -->
            <div class="grid grid-cols-2 gap-3 md:gap-4">
                <div class="bg-status-hadir-bg dark:bg-green-900/20 rounded-2xl md:rounded-3xl p-5 flex flex-col items-center justify-center text-center transition-all hover:-translate-y-1 duration-300">
                    <span class="font-heading font-extrabold text-[42px] leading-none text-status-hadir-text dark:text-green-400 mb-2">{{ $this->countStatus('hadir') }}</span>
                    <p class="text-[15px] font-bold text-gray-700 dark:text-gray-200 leading-tight">Hadir</p>
                </div>
                <div class="bg-status-izin-bg dark:bg-blue-900/20 rounded-2xl md:rounded-3xl p-5 flex flex-col items-center justify-center text-center transition-all hover:-translate-y-1 duration-300">
                    <span class="font-heading font-extrabold text-[42px] leading-none text-status-izin-text dark:text-blue-400 mb-2">{{ $this->countStatus('izin') }}</span>
                    <p class="text-[15px] font-bold text-gray-700 dark:text-gray-200 leading-tight">Izin</p>
                </div>
                <div class="bg-status-sakit-bg dark:bg-yellow-900/20 rounded-2xl md:rounded-3xl p-5 flex flex-col items-center justify-center text-center transition-all hover:-translate-y-1 duration-300">
                    <span class="font-heading font-extrabold text-[42px] leading-none text-status-sakit-text dark:text-yellow-400 mb-2">{{ $this->countStatus('sakit') }}</span>
                    <p class="text-[15px] font-bold text-gray-700 dark:text-gray-200 leading-tight">Sakit</p>
                </div>
                <div class="bg-status-alfa-bg dark:bg-red-900/20 rounded-2xl md:rounded-3xl p-5 flex flex-col items-center justify-center text-center transition-all hover:-translate-y-1 duration-300">
                    <span class="font-heading font-extrabold text-[42px] leading-none text-status-alfa-text dark:text-red-400 mb-2">{{ $this->countStatus('alfa') }}</span>
                    <p class="text-[15px] font-bold text-gray-700 dark:text-gray-200 leading-tight">Alfa</p>
                </div>
            </div>
        </div>

        <div class="xl:col-span-7 bg-white dark:bg-gray-800 rounded-2xl md:rounded-3xl p-6 md:p-8 shadow-card flex flex-col border border-gray-50/50 dark:border-gray-700/50 transition-colors duration-300">
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
                                    <button type="button" onclick="openEditStatusModal({{ $record->id }}, '{{ addslashes($record->anggota?->nama_lengkap ?? '') }}')"
                                        class="w-8 h-8 mx-auto rounded-xl bg-transparent text-gray-400 dark:text-gray-400 flex items-center justify-center hover:bg-brand-light dark:hover:bg-gray-600 hover:text-brand-blue dark:hover:text-blue-400 transition-colors opacity-50 group-hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-brand-blue/50"
                                        title="Edit Status">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </button>
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
        </div>
    </div>

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

    @push('scripts')
        <script>
            window.openEditStatusModal = function (absensiId, name) {
                document.getElementById('modalStudentName').textContent = name;
                document.getElementById('modalAbsensiId').value = absensiId;
                openModal('editStatusModal');
            };

            window.updateStatus = function (status) {
                const absensiId = document.getElementById('modalAbsensiId').value;
                Livewire.dispatch('changeStatus', { absensiId: absensiId, status: status });
                closeModal('editStatusModal');
            };
        </script>
    @endpush
</div>
