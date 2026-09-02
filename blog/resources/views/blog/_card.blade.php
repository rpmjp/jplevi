{{--
    One row in the feed.

    The layout is the one every reading feed has converged on: byline, headline,
    standfirst, then a meta line, with the featured image held to a fixed box on
    the right so a column of rows keeps a straight edge whatever shape the
    pictures are. Shared by the index, the topic archives and the author pages,
    so all three stay in step.

    Links are separate rather than one wrapper, because a link cannot contain
    another link and the byline has to stay clickable.
--}}
@php
    $cover = \App\Models\PostCover::url($post->cover_path, 400);
    $srcset = \App\Models\PostCover::srcset($post->cover_path);
    $primary = $post->categories->first();
@endphp

<article class="group border-b border-paper-3 py-8 first:pt-0">
    <div class="flex items-start gap-5 sm:gap-8">
        <div class="min-w-0 flex-1">

            {{-- Byline --}}
            <div class="flex flex-wrap items-center gap-x-2 gap-y-1 font-sans text-[0.82rem] text-ink-body">
                <x-avatar :name="$post->author->name" :size="24" />
                @if($primary)
                    <span class="text-ink-soft">In</span>
                    <a href="{{ route('blog.topic', $primary) }}" class="font-medium text-ink-ink transition-colors hover:text-brand">{{ $primary->name }}</a>
                @endif
                <span class="text-ink-soft">by</span>
                <a href="{{ route('blog.author', $post->author) }}" class="font-medium text-ink-ink transition-colors hover:text-brand">{{ $post->author->name }}</a>
                @if($post->published_at)
                    <span class="text-ink-soft">&middot;</span>
                    <time datetime="{{ $post->published_at->toDateString() }}" class="text-ink-soft">{{ $post->published_at->format('M j') }}</time>
                @endif
            </div>

            {{-- Headline and standfirst. One target, because a reader aiming at
                 either of them means the same thing. --}}
            <a href="{{ route('blog.show', $post) }}" class="mt-3 block">
                <h2 class="font-grotesk text-[1.35rem] font-extrabold leading-[1.15] tracking-tight2 text-ink-ink transition-colors group-hover:text-brand sm:text-[1.7rem]">
                    {{ $post->title }}
                </h2>
                @if($post->excerpt)
                    <p class="mt-2 line-clamp-2 font-sans text-[0.95rem] leading-[1.45] text-ink-soft sm:text-[1rem]">
                        {{ $post->excerpt }}
                    </p>
                @endif
            </a>

            {{-- Meta --}}
            <div class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-2 font-mono text-[0.72rem] text-ink-soft">
                <span class="inline-flex items-center gap-1.5">
                    <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>
                    </svg>
                    {{ $post->reading_minutes }} min read
                </span>

                @if(($post->comments_count ?? 0) > 0)
                    <a href="{{ route('blog.show', $post) }}#comments" class="inline-flex items-center gap-1.5 transition-colors hover:text-brand">
                        <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M21 12a8 8 0 0 1-8 8H7l-4 3v-4.6A8 8 0 0 1 11 4h2a8 8 0 0 1 8 8Z"/>
                        </svg>
                        {{ $post->comments_count }}
                    </a>
                @endif

                @foreach($post->categories->skip($primary ? 1 : 0)->take(2) as $extra)
                    <a href="{{ route('blog.topic', $extra) }}"
                       class="rounded-full border border-paper-4 px-2.5 py-0.5 transition-colors hover:border-ink-ink hover:text-ink-ink">{{ $extra->name }}</a>
                @endforeach
            </div>
        </div>

        {{-- Featured image. A fixed box, cropped to fill, so the column edge
             stays straight. Lazy below the fold and sized so a phone is never
             sent the desktop rendition. --}}
        @if($cover)
            <a href="{{ route('blog.show', $post) }}" tabindex="-1" aria-hidden="true"
               class="block h-[5.5rem] w-[5.5rem] shrink-0 overflow-hidden bg-paper-2 sm:h-[8.4rem] sm:w-[12.5rem]">
                <img src="{{ $cover }}" @if($srcset) srcset="{{ $srcset }}" @endif
                     sizes="(max-width: 640px) 88px, 200px"
                     alt="" loading="lazy" decoding="async" width="200" height="134"
                     class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-[1.03]">
            </a>
        @else
            {{-- The slot is held open rather than collapsed. A post without a
                 cover otherwise runs the full width and breaks the right edge
                 of the column, which reads as a broken row rather than as a
                 post that happens to have no picture. Hidden on phones, where
                 there is no width to spare. --}}
            <div aria-hidden="true" class="hidden shrink-0 sm:block sm:w-[12.5rem]"></div>
        @endif
    </div>
</article>
