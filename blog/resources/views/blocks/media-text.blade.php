<div class="not-prose my-9 grid gap-6 sm:grid-cols-2 sm:items-center">
    <img src="{{ Storage::url($media->path) }}" alt="{{ $media->alt }}" loading="lazy"
         class="w-full border border-paper-3 {{ $side === 'right' ? 'sm:order-2' : '' }}">
    <div class="font-sans text-[0.95rem] leading-relaxed text-ink-body">{{ $text }}</div>
</div>
