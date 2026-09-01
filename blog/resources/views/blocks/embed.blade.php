<figure class="not-prose my-9">
    <div class="relative w-full" style="padding-top:56.25%">
        <iframe src="{{ $src }}" title="{{ $caption ?: 'Embedded video' }}"
                loading="lazy" allowfullscreen
                referrerpolicy="strict-origin-when-cross-origin"
                class="absolute inset-0 h-full w-full border border-paper-4"></iframe>
    </div>
    @if($caption)
        <figcaption class="mt-3 font-mono text-[0.72rem] text-ink-soft">{{ $caption }}</figcaption>
    @endif
</figure>
