<figure class="not-prose my-9">
    <div class="grid gap-3 {{ $columns === 3 ? 'sm:grid-cols-3' : 'sm:grid-cols-2' }}">
        @foreach($items as $item)
            <img src="{{ Storage::url($item->path) }}" alt="{{ $item->alt }}" loading="lazy"
                 class="w-full border border-paper-3">
        @endforeach
    </div>
    @if($caption)
        <figcaption class="mt-3 font-mono text-[0.72rem] text-ink-soft">{{ $caption }}</figcaption>
    @endif
</figure>
