{{--
    Where to go next, placed above the comments rather than below them.

    A reader who has finished the piece decides in a second or two whether there
    is anything else here for them, and by the bottom of a comment thread that
    decision has already been made. Cards rather than a list, because the
    featured image is doing most of the persuading.
--}}
@if($related->isNotEmpty())
<section aria-labelledby="keep-reading" class="mx-auto mt-20 max-w-5xl px-6 sm:px-10">
    <div class="border-t border-ink-ink pt-8">
        <h2 id="keep-reading" class="font-grotesk text-[1.6rem] font-extrabold tracking-tight2 text-ink-ink">Keep reading</h2>

        {{-- Written out rather than interpolated: Tailwind reads these files as
             plain text to decide which classes to generate, so a class name
             built at runtime is a class name that never gets compiled. --}}
        <ul class="mt-8 grid gap-x-7 gap-y-10 sm:grid-cols-2 {{ $related->count() >= 4 ? 'lg:grid-cols-4' : 'lg:grid-cols-3' }}">
            @foreach($related as $other)
                @php
                    $cover = \App\Models\Rendition::url($other->cover_path, 400);
                    $srcset = \App\Models\Rendition::srcset($other->cover_path);
                    $topic = $other->categories->first();
                @endphp
                <li class="group">
                    <a href="{{ route('blog.show', $other) }}" class="block">
                        <div class="aspect-[3/2] overflow-hidden bg-paper-2">
                            @if($cover)
                                <img src="{{ $cover }}" @if($srcset) srcset="{{ $srcset }}" @endif
                                     sizes="(max-width: 640px) 100vw, (max-width: 1024px) 45vw, 22vw"
                                     alt="" loading="lazy" decoding="async" width="400" height="267"
                                     class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-[1.04]">
                            @else
                                {{-- No image is better than a stock one. The block
                                     keeps the grid aligned and carries the topic. --}}
                                <div class="flex h-full items-end bg-night p-4">
                                    <span class="font-mono text-[0.66rem] uppercase tracking-label text-paper/50">{{ $topic?->name ?? 'Notes' }}</span>
                                </div>
                            @endif
                        </div>

                        @if($topic)
                            <p class="mt-4 font-mono text-[0.66rem] uppercase tracking-label text-brand">{{ $topic->name }}</p>
                        @endif

                        <h3 class="mt-2 font-grotesk text-[1.05rem] font-extrabold leading-[1.2] tracking-tight2 text-ink-ink transition-colors group-hover:text-brand">
                            {{ $other->title }}
                        </h3>

                        <p class="mt-2.5 font-mono text-[0.7rem] text-ink-soft">
                            {{ $other->published_at?->format('M j, Y') }} &middot; {{ $other->reading_minutes }} min
                        </p>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</section>
@endif
