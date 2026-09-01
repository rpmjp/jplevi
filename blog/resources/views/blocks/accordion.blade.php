<div class="not-prose my-9">
    @foreach($items as $item)
        <details class="border-b border-paper-4 py-4">
            <summary class="cursor-pointer font-sans text-[0.98rem] font-medium text-ink-ink">{{ $item['question'] }}</summary>
            <p class="mt-3 whitespace-pre-line font-sans text-[0.94rem] leading-relaxed text-ink-body">{{ $item['answer'] }}</p>
        </details>
    @endforeach
</div>
