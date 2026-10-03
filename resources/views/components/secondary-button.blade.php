<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-surface2 border border-line rounded-md font-semibold text-xs text-ink uppercase tracking-widest hover:bg-surface focus:outline-none focus:ring-2 focus:ring-neon transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
