<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @php
            $pengaturan = \App\Models\PengaturanProfil::instance();
            $faviconPath = $pengaturan->logo_unit_kegiatan && file_exists(public_path($pengaturan->logo_unit_kegiatan))
                ? asset($pengaturan->logo_unit_kegiatan)
                : null;
        @endphp
        <link rel="icon" href="{{ $faviconPath ?: asset('favicon.ico') }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col lg:flex-row bg-white">

            <!-- Left: Brand / Hero Panel (hidden on mobile) -->
            <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-gradient-to-br from-brand-blue via-indigo-900 to-gray-900">
                <!-- Taekwondo background image -->
                <div class="absolute inset-0 bg-cover bg-center opacity-30"
                    style="background-image: url('https://images.unsplash.com/photo-1555597673-b21d5c935865?q=80&w=1200');"></div>

                <!-- Gradient overlay -->
                <div class="absolute inset-0 bg-gradient-to-br from-brand-blue/80 via-indigo-900/85 to-gray-900/90"></div>

                <!-- Decorative blurred shapes -->
                <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-white/5 blur-2xl"></div>
                <div class="absolute bottom-1/4 -left-24 w-80 h-80 rounded-full bg-white/5 blur-2xl"></div>
                <div class="absolute top-1/2 right-0 w-40 h-40 rounded-full bg-brand-light/10 blur-xl"></div>

                <div class="relative z-10 flex flex-col justify-between p-14 xl:p-16 w-full">
                    <a href="/" class="flex items-center gap-3">
                        <x-application-logo class="w-11 h-11 text-white drop-shadow-lg" />
                        <div>
                            <span class="block font-heading font-bold text-white text-lg leading-tight">UKM Taekwondo</span>
                            <span class="block text-xs text-indigo-200">Sistem Absensi Mahasiswa</span>
                        </div>
                    </a>

                    <div>
                        <h2 class="font-heading font-bold text-white text-4xl xl:text-5xl leading-tight max-w-md">
                            Sistem Informasi UKM Taekwondo
                        </h2>
                        <p class="mt-5 text-indigo-100/90 text-base leading-relaxed max-w-md">
                            Kelola kehadiran latihan, perizinan, dan jadwal anggota UKM Taekwondo dalam satu platform yang cepat dan terpercaya.
                        </p>

                        <ul class="mt-8 space-y-4">
                            @foreach ([
                                ['title' => 'Absensi QR Code', 'desc' => 'Catat kehadiran latihan secara cepat dan akurat.'],
                                ['title' => 'Manajemen Izin', 'desc' => 'Ajukan dan kelola izin latihan langsung dari akun Anda.'],
                                ['title' => 'Rekap Kehadiran', 'desc' => 'Pantau statistik dan riwayat kehadiran anggota.'],
                            ] as $feature)
                                <li class="flex items-start gap-3">
                                    <span class="mt-0.5 w-6 h-6 rounded-full bg-white/15 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </span>
                                    <div>
                                        <p class="font-semibold text-white">{{ $feature['title'] }}</p>
                                        <p class="text-sm text-indigo-100/80">{{ $feature['desc'] }}</p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <p class="text-sm text-indigo-200/60">&copy; {{ date('Y') }} UKM Taekwondo &mdash; Sistem Absensi</p>
                </div>
            </div>

            <!-- Right: Form Area -->
            <div class="flex-1 flex flex-col justify-center items-center px-6 py-10 sm:px-12 bg-white">
                <div class="w-full max-w-md">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
