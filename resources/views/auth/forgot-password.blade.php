<x-guest-layout>
    <div class="bg-white w-full max-w-[420px] rounded-[2.5rem] p-8 sm:p-10 shadow-[0_10px_40px_rgba(0,0,0,0.04)]">
    <!-- Header -->
    <div class="text-center mb-8">
        <h2 class="text-3xl font-extrabold text-[#111827] mb-1">Lupa Kata Sandi?</h2>
        <p class="text-sm font-medium text-[#6B7280]">Kami akan mengirimkan tautan untuk mengatur ulang kata sandi Anda.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-bold text-[#374151] mb-2">Alamat Email</label>
            <div class="relative">
                <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                </svg>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="Masukkan Email Anda"
                    class="w-full bg-[#F9FAFB] border border-[#E5E7EB] text-[#4B5563] text-sm rounded-2xl focus:ring-2 focus:ring-[#3D5EE1] focus:border-transparent block pl-12 p-3.5 outline-none transition-all">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Submit -->
        <button type="submit"
            class="w-full text-white bg-[#3D5EE1] hover:bg-[#324fc2] font-bold rounded-2xl text-sm px-5 py-3.5 shadow-[0_8px_20px_rgba(61,94,225,0.3)] transition-all flex justify-center items-center gap-2">
            Kirim Tautan Reset
        </button>

        <p class="text-center">
            <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-[#3D5EE1] hover:text-blue-800 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"></path>
                </svg>
                Kembali ke halaman masuk
            </a>
        </p>
    </form>
    </div>
</x-guest-layout>
