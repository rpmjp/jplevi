<?php echo '<?xml version="1.0" encoding="UTF-8"?>'."\n"; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ route('blog.index') }}</loc>
        <changefreq>weekly</changefreq>
    </url>
    @foreach($topics as $topic)
    <url>
        <loc>{{ route('blog.topic', $topic) }}</loc>
        <changefreq>weekly</changefreq>
    </url>
    @endforeach
    @foreach($posts as $post)
    <url>
        <loc>{{ route('blog.show', $post) }}</loc>
        <lastmod>{{ $post->updated_at->toAtomString() }}</lastmod>
    </url>
    @endforeach
</urlset>
