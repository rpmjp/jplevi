{{-- No inline handlers: the policy forbids them, so behaviour comes from the
     bundled script reading these attributes. Without it, every panel shows,
     which is worse looking but still entirely readable. --}}
<div class="not-prose my-9" data-tabs id="{{ $id }}">
    <div class="flex flex-wrap gap-1 border-b border-paper-4" role="tablist">
        @foreach($panels as $i => $panel)
            <button type="button" role="tab"
                    data-tab-target="{{ $id }}-{{ $i }}"
                    aria-selected="{{ $i === 0 ? 'true' : 'false' }}"
                    aria-controls="{{ $id }}-{{ $i }}"
                    class="border-b-2 px-4 py-2.5 font-mono text-[0.7rem] uppercase tracking-label transition-colors
                           {{ $i === 0 ? 'border-brand text-ink-ink' : 'border-transparent text-ink-soft hover:text-ink-ink' }}">
                {{ $panel['label'] }}
            </button>
        @endforeach
    </div>
    @foreach($panels as $i => $panel)
        <div id="{{ $id }}-{{ $i }}" role="tabpanel" data-tab-panel
             class="whitespace-pre-line pt-5 font-sans text-[0.95rem] leading-relaxed text-ink-body"
             @if($i > 0) hidden @endif>{{ $panel['body'] }}</div>
    @endforeach
</div>
