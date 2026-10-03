@props(['disabled' => false])
<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full bg-surface2 border-line text-ink placeholder-muted rounded focus:border-neon focus:ring-neon']) }}>
