<button {{ $attributes->merge(['class' => 'bg-neon hover:bg-neon-dim text-night px-4 py-2 rounded font-semibold text-sm']) }}>
    {{ $slot }}
</button>
