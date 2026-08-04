@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 bg-gray-50 focus:bg-white focus:border-brand-blue focus:ring-brand-blue rounded-xl px-4 py-3 text-gray-900 placeholder-gray-400 shadow-sm transition-colors duration-200']) }}>
