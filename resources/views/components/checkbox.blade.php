@props(['disabled' => false])
<input type="checkbox" @disabled($disabled) {{ $attributes->merge(['class' => 'rounded bg-surface2 border-line text-neon focus:ring-neon']) }}>
