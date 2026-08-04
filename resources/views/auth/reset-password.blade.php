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
        <h1 class="font-heading font-bold text-3xl text-gray-900">Atur Ulang Kata Sandi</h1>
        <p class="text-gray-500 mt-2">Buat kata sandi baru untuk akun Anda.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1.5 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" placeholder="nama@contoh.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1.5 w-full" type="password" name="password" required autocomplete="new-password" placeholder="&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1.5 w-full"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password"
                                placeholder="&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <x-primary-button class="w-full">
            {{ __('Reset Password') }}
        </x-primary-button>

        <p class="text-center text-sm text-gray-500">
            <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 font-semibold text-brand-blue hover:text-brand-hover transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"></path>
                </svg>
                {{ __('Kembali ke halaman masuk') }}
            </a>
        </p>
    </form>
</x-guest-layout>
