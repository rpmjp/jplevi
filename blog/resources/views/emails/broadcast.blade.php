{{--
    Newsletter layout.

    Tables and inline styles throughout: email clients have no reliable support
    for grid, flex, or external stylesheets, and Outlook still renders through
    Word. Web fonts are declared but every stack falls back to something real,
    because most clients will ignore them.
--}}
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>{{ $broadcast->subject }}</title>
</head>
<body style="margin:0;padding:0;background:#F5F3EF;">

{{-- Preview text: what the inbox shows after the subject line. --}}
<div style="display:none;max-height:0;overflow:hidden;opacity:0;">
    {{ $broadcast->preheader ?: Str::limit(strip_tags($broadcast->intro ?? ''), 120) }}
</div>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F5F3EF;">
<tr><td align="center" style="padding:32px 16px;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;background:#F5F3EF;">

    {{-- Masthead --}}
    <tr><td style="padding-bottom:22px;border-bottom:2px solid #0B0B0C;">
        <a href="{{ url('/') }}" style="text-decoration:none;">
            <span style="font-family:'Archivo Narrow',Arial Narrow,Arial,sans-serif;font-weight:800;font-size:20px;letter-spacing:-0.5px;text-transform:uppercase;color:#0B0B0C;">JP Levi AI</span>
        </a>
        <span style="float:right;font-family:'IBM Plex Mono',Consolas,monospace;font-size:10px;letter-spacing:2px;text-transform:uppercase;color:#5E6069;">Notes</span>
    </td></tr>

    @if($broadcast->intro)
        <tr><td style="padding:30px 0 0;">
            <div style="font-family:Archivo,Helvetica,Arial,sans-serif;font-size:17px;line-height:1.55;color:#0B0B0C;">
                {!! $broadcast->intro !!}
            </div>
        </td></tr>
    @endif

    {{-- The posts themselves --}}
    @foreach($broadcast->posts as $post)
        @php($cover = \App\Models\Rendition::url($post->cover_path))
        <tr><td style="padding:34px 0 0;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-top:1px solid #CFC9BC;">
                @if($cover)
                    <tr><td style="padding:22px 0 0;">
                        <a href="{{ route('blog.show', $post) }}">
                            <img src="{{ $cover }}" width="600" alt="{{ $post->cover_alt ?? '' }}"
                                 style="display:block;width:100%;max-width:600px;height:auto;border:0;">
                        </a>
                    </td></tr>
                @endif
                <tr><td style="padding:20px 0 0;">
                    <div style="font-family:'IBM Plex Mono',Consolas,monospace;font-size:10px;letter-spacing:2px;text-transform:uppercase;color:#5E6069;">
                        {{ $post->published_at?->format('j M Y') }} &middot; {{ $post->reading_minutes }} min read
                    </div>
                    <a href="{{ route('blog.show', $post) }}" style="text-decoration:none;">
                        <div style="margin-top:10px;font-family:'Archivo Narrow',Arial Narrow,Arial,sans-serif;font-weight:800;font-size:26px;line-height:1.05;letter-spacing:-0.6px;text-transform:uppercase;color:#0B0B0C;">
                            {{ $post->title }}
                        </div>
                    </a>
                    @if($post->excerpt)
                        <div style="margin-top:12px;font-family:Archivo,Helvetica,Arial,sans-serif;font-size:15px;line-height:1.6;color:#33343A;">
                            {{ $post->excerpt }}
                        </div>
                    @endif
                    <div style="margin-top:18px;">
                        <a href="{{ route('blog.show', $post) }}"
                           style="display:inline-block;background:#1B3EF0;color:#ffffff;text-decoration:none;padding:11px 20px;font-family:'IBM Plex Mono',Consolas,monospace;font-size:11px;letter-spacing:2px;text-transform:uppercase;">
                            Read it
                        </a>
                    </div>
                </td></tr>
            </table>
        </td></tr>
    @endforeach

    @if($broadcast->body)
        <tr><td style="padding:34px 0 0;border-top:1px solid #CFC9BC;">
            <div style="font-family:Archivo,Helvetica,Arial,sans-serif;font-size:15px;line-height:1.65;color:#33343A;">
                {!! $broadcast->body !!}
            </div>
        </td></tr>
    @endif

    {{-- Footer. The unsubscribe link is not optional and is never hidden. --}}
    <tr><td style="padding:38px 0 0;border-top:2px solid #0B0B0C;">
        <div style="padding-top:16px;font-family:'IBM Plex Mono',Consolas,monospace;font-size:11px;line-height:1.7;color:#5E6069;">
            You confirmed a subscription at jplevi.com. Nothing else is ever sent to this address.<br>
            <a href="{{ $unsubscribeUrl }}" style="color:#5E6069;">Unsubscribe in one click</a><br><br>
            JP LEVI INC. &middot; North Brunswick, New Jersey
        </div>
    </td></tr>

</table>
</td></tr>
</table>
</body>
</html>
