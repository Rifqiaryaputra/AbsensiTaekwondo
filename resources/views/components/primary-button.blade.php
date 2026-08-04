<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-6 py-3 bg-brand-blue hover:bg-brand-hover active:bg-brand-hover border border-transparent rounded-xl font-semibold text-sm text-white shadow-md shadow-brand-blue/25 hover:shadow-lg hover:shadow-brand-blue/30 transition-all duration-200 hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-brand-blue focus:ring-offset-2']) }}>
    {{ $slot }}
</button>
