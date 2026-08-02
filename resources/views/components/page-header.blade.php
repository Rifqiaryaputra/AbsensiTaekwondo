@props([
    'title' => '',
    'subtitle' => '',
])

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4 mb-2">
    <div>
        <h1 class="font-heading font-extrabold text-3xl text-gray-900 dark:text-white tracking-tight">{{ $title }}</h1>
        @if ($subtitle)
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 font-medium">{{ $subtitle }}</p>
        @endif
    </div>
    @if ($slot->isNotEmpty())
        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
            {{ $slot }}
        </div>
    @endif
</div>
