@props([
    'icon' => 'group',
    'label' => '',
    'value' => '0',
    'iconBg' => 'bg-brand-light dark:bg-brand-blue/20',
    'iconText' => 'text-brand-blue',
])

<div class="bg-white dark:bg-gray-800 rounded-2xl md:rounded-3xl p-4 md:p-6 flex flex-col sm:flex-row items-start sm:items-center gap-3 md:gap-5 shadow-card hover:-translate-y-1 transition-all duration-300">
    <div class="w-12 h-12 md:w-16 md:h-16 rounded-full {{ $iconBg }} flex items-center justify-center {{ $iconText }} shrink-0">
        <span class="material-symbols-outlined text-[24px] md:text-[28px]">{{ $icon }}</span>
    </div>
    <div>
        <p class="text-[9px] md:text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-0.5 md:mb-1">{{ $label }}</p>
        <p class="font-heading font-bold text-2xl md:text-3xl text-gray-900 dark:text-white">{{ $value }}</p>
    </div>
</div>
