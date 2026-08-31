<?php echo '<?xml version="1.0" encoding="UTF-8"?>'."\n"; ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
    <title>JP Levi AI</title>
    <link>{{ route('blog.index') }}</link>
    <description>Notes on AI, machine learning, and building software for small businesses.</description>
    <language>en</language>
    <atom:link href="{{ route('blog.feed') }}" rel="self" type="application/rss+xml"/>
    @foreach($posts as $post)
    <item>
        <title>{{ $post->title }}</title>
        <link>{{ route('blog.show', $post) }}</link>
        <guid isPermaLink="true">{{ route('blog.show', $post) }}</guid>
        <pubDate>{{ $post->published_at->toRfc2822String() }}</pubDate>
        <author>{{ $post->author->name }}</author>
        <description>{{ $post->excerpt }}</description>
    </item>
    @endforeach
</channel>
</rss>
