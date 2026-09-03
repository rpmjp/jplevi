{{--
    Inline styles rather than classes: this markup is inserted into the editor
    canvas, which does not carry the admin theme's stylesheet.
--}}
<div style="border:1px solid #dcdcde;border-radius:2px">
    <div style="display:flex;align-items:center;gap:.5rem;padding:.4rem .7rem;background:#f0f0f1;border-bottom:1px solid #dcdcde;font-size:.68rem;text-transform:uppercase;letter-spacing:.12em;color:#50575e">
        Custom HTML
    </div>

    @foreach($problems as $problem)
        <p style="margin:0;padding:.6rem .7rem;background:#fcf0f1;border-bottom:1px solid #dcdcde;font-size:.78rem;color:#8a2424">
            {{ $problem }}
        </p>
    @endforeach

    {{-- Rendered, because seeing what it looks like is the whole point. --}}
    <div style="padding:.9rem .7rem">{!! $html !!}</div>

    <details style="border-top:1px solid #dcdcde">
        <summary style="cursor:pointer;padding:.45rem .7rem;font-size:.7rem;color:#50575e">Source</summary>
        <pre style="margin:0;padding:.7rem;overflow-x:auto;background:#1d2327;color:#f0f0f1;font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:.75rem;line-height:1.55"><code>{{ $html }}</code></pre>
    </details>
</div>
