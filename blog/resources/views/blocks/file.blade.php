<p class="not-prose my-7">
    <a href="{{ $url }}" download
       class="inline-flex items-baseline gap-3 border border-ink-ink px-5 py-3 transition-colors hover:border-brand">
        <span class="font-mono text-[0.66rem] uppercase tracking-label text-brand">Download</span>
        <span class="font-sans text-[0.95rem] text-ink-ink">{{ $label }}</span>
        @if($note)<span class="font-mono text-[0.7rem] text-ink-soft">{{ $note }}</span>@endif
    </a>
</p>
