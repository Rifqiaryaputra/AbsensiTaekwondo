<x-guest-layout>
    <!-- Mobile brand header -->
    <div class="lg:hidden flex items-center gap-3 mb-8">
        <x-application-logo class="w-10 h-10 text-brand-blue" />
        <div>
            <p class="font-heading font-bold text-gray-900 leading-tight">UKM Taekwondo</p>
            <p class="text-xs text-gray-500">Sistem Absensi Mahasiswa</p>
        </div>
    </div>

    <div class="mb-8">
        <h1 class="font-heading font-bold text-3xl text-gray-900">Selamat Datang!</h1>
        <p class="text-gray-500 mt-2">Silakan masuk untuk melanjutkan ke sistem absensi.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1.5 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@contoh.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between">
                <x-input-label for="password" :value="__('Password')" />

                @if (Route::has('password.request'))
                    <a class="text-sm font-semibold text-brand-blue hover:text-brand-hover transition-colors" href="{{ route('password.request') }}">
                        {{ __('Lupa kata sandi?') }}
                    </a>
                @endif
            </div>

            <x-text-input id="password" class="block mt-1.5 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password"
                            placeholder="&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-brand-blue shadow-sm focus:ring-brand-blue focus:ring-offset-0" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Ingat saya') }}</span>
            </label>
        </div>

        <x-primary-button class="w-full">
            {{ __('Masuk') }}
        </x-primary-button>
    </form>
</x-guest-layout>
