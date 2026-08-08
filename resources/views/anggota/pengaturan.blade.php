<x-app-layout title="Pengaturan - Sistem Absensi UKM Taekwondo" mobile-header-title="Pengaturan" mobile="static">

    <div class="max-w-[768px] mx-auto space-y-6 md:space-y-8 animate-[fadeIn_0.3s_ease-in-out]" x-data="{ showLogoutModal: false }">
        <x-page-header title="Pengaturan Keamanan" subtitle="Perbarui kata sandi akun Anda secara berkala untuk menjaga keamanan data." />

        <!-- Settings Card: Ubah Password -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-50 dark:border-gray-700 p-6 md:p-8 shadow-card flex flex-col transition-colors duration-300">
            <div class="flex items-center gap-3 mb-6 md:mb-8 border-b border-gray-100 dark:border-gray-700 pb-4 transition-colors">
                <div class="w-10 h-10 rounded-full bg-brand-light dark:bg-brand-blue/20 flex items-center justify-center text-brand-blue dark:text-brand-light transition-colors">
                    <span class="material-symbols-outlined">lock_reset</span>
                </div>
                <div>
                    <h2 class="font-heading font-bold text-lg text-gray-900 dark:text-white transition-colors">Ubah Kata Sandi</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium transition-colors">Minimal 8 karakter untuk keamanan maksimal.</p>
                </div>
            </div>

            @if (session('success'))
                <div class="mb-5 flex items-center gap-2 bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-900/40 text-green-600 dark:text-green-400 text-sm font-semibold px-4 py-3 rounded-xl transition-colors">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('anggota.pengaturan.password') }}" class="space-y-5 md:space-y-6">
                @csrf
                @method('PATCH')

                <div class="space-y-2 relative">
                    <label class="block text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider transition-colors" for="current_password">Kata Sandi Saat Ini</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 text-[20px] transition-colors">lock</span>
                        <input class="w-full pl-11 pr-12 py-3 md:py-3.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl md:rounded-2xl font-medium text-sm text-gray-800 dark:text-white focus:outline-none focus:border-brand-blue dark:focus:border-brand-blue/50 focus:ring-4 focus:ring-brand-light dark:focus:ring-brand-blue/20 transition-all @error('current_password') border-red-400 dark:border-red-500 @enderror" id="current_password" name="current_password" type="password" required placeholder="Masukkan kata sandi lama">
                        <button type="button" onclick="togglePassword('current_password', 'icon_old')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 hover:text-brand-blue dark:hover:text-brand-light focus:outline-none transition-colors">
                            <span id="icon_old" class="material-symbols-outlined text-[20px]">visibility</span>
                        </button>
                    </div>
                    @error('current_password')
                        <p class="text-xs text-red-500 dark:text-red-400 font-semibold mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2 relative">
                    <label class="block text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider transition-colors" for="new_password">Kata Sandi Baru</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 text-[20px] transition-colors">key</span>
                        <input class="w-full pl-11 pr-12 py-3 md:py-3.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl md:rounded-2xl font-medium text-sm text-gray-800 dark:text-white focus:outline-none focus:border-brand-blue dark:focus:border-brand-blue/50 focus:ring-4 focus:ring-brand-light dark:focus:ring-brand-blue/20 transition-all @error('new_password') border-red-400 dark:border-red-500 @enderror" id="new_password" name="new_password" type="password" required minlength="8" placeholder="Minimal 8 karakter">
                        <button type="button" onclick="togglePassword('new_password', 'icon_new')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 hover:text-brand-blue dark:hover:text-brand-light focus:outline-none transition-colors">
                            <span id="icon_new" class="material-symbols-outlined text-[20px]">visibility</span>
                        </button>
                    </div>
                    @error('new_password')
                        <p class="text-xs text-red-500 dark:text-red-400 font-semibold mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2 relative">
                    <label class="block text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider transition-colors" for="new_password_confirmation">Ulangi Kata Sandi Baru</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 text-[20px] transition-colors">verified_user</span>
                        <input class="w-full pl-11 pr-12 py-3 md:py-3.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl md:rounded-2xl font-medium text-sm text-gray-800 dark:text-white focus:outline-none focus:border-brand-blue dark:focus:border-brand-blue/50 focus:ring-4 focus:ring-brand-light dark:focus:ring-brand-blue/20 transition-all @error('new_password') border-red-400 dark:border-red-500 @enderror" id="new_password_confirmation" name="new_password_confirmation" type="password" required minlength="8" placeholder="Ketik ulang kata sandi baru">
                        <button type="button" onclick="togglePassword('new_password_confirmation', 'icon_confirm')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 hover:text-brand-blue dark:hover:text-brand-light focus:outline-none transition-colors">
                            <span id="icon_confirm" class="material-symbols-outlined text-[20px]">visibility</span>
                        </button>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full md:w-auto min-h-[48px] px-8 py-3.5 bg-brand-blue text-white rounded-xl md:rounded-2xl font-semibold text-sm hover:bg-brand-hover shadow-lg shadow-brand-blue/30 transition-all flex items-center justify-center gap-2 focus:outline-none">
                        <span class="material-symbols-outlined text-[20px]">save</span>
                        Simpan Kata Sandi
                    </button>
                </div>
            </form>
        </div>

        <!-- Logout Trigger (mobile only) -->
    <button type="button" @click="showLogoutModal = true"
        class="w-full flex items-center justify-center gap-2 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 font-semibold py-3 px-4 rounded-xl border border-red-100 dark:border-red-900/40 active:bg-red-100 dark:active:bg-red-900/40 transition-colors block md:hidden">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
            <polyline points="16 17 21 12 16 7"></polyline>
            <line x1="21" y1="12" x2="9" y2="12"></line>
        </svg>
        Logout
    </button>

    <!-- Logout Confirmation Modal -->
    <div x-show="showLogoutModal" style="display: none;" x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-slate-800 dark:border dark:border-slate-700 rounded-3xl p-6 w-[90%] max-w-sm text-center shadow-xl">
            <div class="bg-red-50 dark:bg-red-500/10 text-red-500 w-16 h-16 rounded-full mx-auto flex items-center justify-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
            </div>

            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Konfirmasi Keluar?</h3>
            <p class="text-sm text-gray-500 dark:text-gray-300 mb-6">Apakah Anda yakin ingin keluar dari sistem? Anda harus login kembali untuk masuk.</p>

            <div class="flex gap-3">
                <button type="button" @click="showLogoutModal = false"
                    class="flex-1 py-3 bg-gray-50 dark:bg-slate-700 text-gray-700 dark:text-gray-200 font-semibold rounded-xl hover:bg-gray-100 dark:hover:bg-slate-600 transition-colors">
                    Batal
                </button>
                <form method="POST" action="{{ route('logout') }}" class="flex-1" @submit="showLogoutModal = false">
                    @csrf
                    <button type="submit" class="w-full py-3 bg-red-500 text-white font-semibold rounded-xl hover:bg-red-600 transition-colors">
                        Ya, Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
    </div>

</x-app-layout>
