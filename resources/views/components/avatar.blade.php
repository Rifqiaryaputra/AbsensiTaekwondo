@props([
    'name' => '',
    'class' => 'w-10 h-10 text-sm',
])

@php
    $initial = $name ? mb_strtoupper(mb_substr($name, 0, 1)) : '?';
@endphp

<div class="rounded-full {{ $class }} bg-brand-blue text-white flex items-center justify-center font-heading font-bold shadow-md shrink-0">
    {{ $initial }}
</div>
