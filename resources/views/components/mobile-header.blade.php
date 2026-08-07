@props([
    'title' => '',
    'type' => 'drawer',
])

<div class="md:hidden fixed top-0 left-0 right-0 bg-white/85 dark:bg-gray-800/85 backdrop-blur-lg border-b border-gray-100 dark:border-gray-700 z-40 px-5 py-3 flex items-center justify-between shadow-sm transition-colors duration-300">
    <div class="flex items-center gap-3">
        <div class="w-9 h-9 bg-brand-blue text-white rounded-xl flex items-center justify-center font-heading font-bold text-sm shadow-md">UT</div>
        <div>
            <h1 class="font-heading font-bold text-gray-900 dark:text-white text-sm leading-tight">{{ $title }}</h1>
            <p class="text-[10px] text-gray-500 dark:text-gray-400 font-medium">UKM Taekwondo</p>
        </div>
    </div>
    @if ($type === 'drawer')
        <button type="button" onclick="toggleMenu()" class="text-gray-500 dark:text-gray-400 hover:text-brand-blue dark:hover:text-brand-light transition-colors focus:outline-none p-1">
            <span class="material-symbols-outlined text-[28px]">menu</span>
        </button>
    @else
        <button type="button" onclick="toggleDarkMode()" class="text-gray-500 dark:text-gray-300 hover:text-brand-blue dark:hover:text-brand-light transition-colors focus:outline-none w-10 h-10 rounded-full bg-gray-50 dark:bg-gray-700 flex items-center justify-center shadow-sm" title="Mode Gelap">
            <span class="material-symbols-outlined text-[22px]" id="mobileDarkModeIcon">dark_mode</span>
        </button>
    @endif
</div>
