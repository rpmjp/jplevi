<p class="not-prose my-7">
    <a href="{{ $url }}"
       class="inline-block px-6 py-3 font-mono text-[0.72rem] font-semibold uppercase tracking-label transition-colors
              {{ $style === 'outline'
                 ? 'border border-ink-ink text-ink-ink hover:border-brand hover:text-brand'
                 : 'border border-brand bg-brand text-white hover:border-brand-soft hover:bg-brand-soft' }}">
        {{ $label }}
    </a>
</p>
