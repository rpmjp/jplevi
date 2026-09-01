<figure class="not-prose my-10 border-y border-ink-ink py-7">
    <blockquote class="font-display text-[clamp(1.4rem,3.4vw,2.1rem)] font-bold uppercase leading-[1.05] tracking-tight2 text-ink-ink">
        {{ $text }}
    </blockquote>
    @if($attribution)
        <figcaption class="mt-4 font-mono text-[0.7rem] uppercase tracking-label text-ink-soft">{{ $attribution }}</figcaption>
    @endif
</figure>
