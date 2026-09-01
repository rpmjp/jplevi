@php($tones = [
    'note'    => ['border-brand',  'Note'],
    'warning' => ['border-ember',  'Warning'],
    'result'  => ['border-[#146C33]', 'Result'],
])
@php([$border, $default] = $tones[$tone] ?? $tones['note'])
<aside class="not-prose my-8 border-l-2 {{ $border }} bg-paper-2/60 px-5 py-4">
    <p class="font-mono text-[0.66rem] uppercase tracking-label text-ink-soft">{{ $title ?: $default }}</p>
    <p class="mt-2 font-sans text-[0.95rem] leading-relaxed text-ink-body">{{ $body }}</p>
</aside>
