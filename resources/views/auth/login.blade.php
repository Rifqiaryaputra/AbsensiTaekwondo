<x-guest-layout>
    <div class="bg-white w-full max-w-[420px] rounded-[2.5rem] p-8 sm:p-10 shadow-[0_10px_40px_rgba(0,0,0,0.04)]">
        <!-- Header -->
        <h2 class="text-3xl font-extrabold text-[#111827] text-center mb-1">Masuk</h2>
        <p class="text-sm font-medium text-[#6B7280] text-center mb-8">Sistem Absensi UKM Taekwondo</p>

        <!-- Session Status -->
        @if (session('status'))
            <div class="mb-6 p-4 rounded-2xl bg-green-50 border border-green-100 text-green-600 text-sm font-bold text-center">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-sm font-bold text-[#374151] mb-2">Email</label>
                <div class="relative">
                    <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                    </svg>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="Masukkan Email Anda"
                        class="w-full bg-[#F9FAFB] border text-[#4B5563] text-sm rounded-2xl focus:ring-2 focus:ring-[#3D5EE1] focus:border-transparent block pl-12 p-3.5 outline-none transition-all @error('email') border-red-400 @else border-[#E5E7EB] @enderror">
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <label for="password" class="block text-sm font-bold text-[#374151] mb-2">Password</label>
                <div class="relative" x-data="{ show: false }">
                    <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                    </svg>
                    <input id="password" :type="show ? 'text' : 'password'" name="password" required autocomplete="current-password" placeholder="&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;"
                        class="w-full bg-[#F9FAFB] border text-[#4B5563] text-sm rounded-2xl focus:ring-2 focus:ring-[#3D5EE1] focus:border-transparent block pl-12 p-3.5 outline-none transition-all @error('password') border-red-400 @else border-[#E5E7EB] @enderror">
                    <div @click="show = !show" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 cursor-pointer hover:text-gray-600 transition-colors">
                        <svg x-show="!show" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd"></path>
                            <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z"></path>
                        </svg>
                        <svg x-show="show" style="display: none;" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>

                <div class="flex justify-end mt-2">
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm font-bold text-[#3D5EE1] hover:text-blue-800 transition-colors">Lupa kata sandi?</a>
                    @endif
                </div>

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Submit -->
            <button type="submit"
                class="w-full text-white bg-[#3D5EE1] hover:bg-[#324fc2] font-bold rounded-2xl text-sm px-5 py-3.5 shadow-[0_8px_20px_rgba(61,94,225,0.3)] transition-all flex justify-center items-center gap-2 mt-8">
                Masuk
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M15 12H3"></path>
                </svg>
            </button>
        </form>
    </div>
</x-guest-layout>
