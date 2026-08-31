<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::published()
            ->with(['tags', 'author'])
            ->when($request->string('tag')->toString(), fn ($q, $slug) => $q->whereHas('tags', fn ($t) => $t->where('slug', $slug)))
            ->when($request->string('q')->toString(), function ($q, $term) {
                // MySQL full text would be faster, but LIKE keeps this working
                // identically on SQLite locally and MySQL in production.
                $like = '%'.str_replace('%', '\%', $term).'%';
                $q->where(fn ($w) => $w->where('title', 'like', $like)
                    ->orWhere('excerpt', 'like', $like)
                    ->orWhere('body', 'like', $like));
            })
            ->latest('published_at')
            ->paginate(15);

        // Filtered in PHP rather than with HAVING: the tag list is small, and
        // HAVING without GROUP BY is a MySQL nicety that SQLite rejects.
        $tags = Tag::withCount(['posts' => fn ($q) => $q->published()])
            ->get()
            ->filter(fn (Tag $tag) => $tag->posts_count > 0)
            ->sortByDesc('posts_count')
            ->values();

        return view('blog.index', compact('posts', 'tags'));
    }

    public function show(Request $request, string $slug)
    {
        $post = Post::with(['tags', 'author'])->where('slug', $slug)->firstOrFail();

        // A draft is readable only with its own preview token, so a link can be
        // shared for review without the post being public.
        $preview = false;

        $isLive = $post->status === 'published'
            && $post->published_at !== null
            && ! $post->published_at->isFuture();

        if (! $isLive) {
            abort_unless($request->query('preview') === $post->preview_token, 404);
            $preview = true;
        }

        $related = Post::published()
            ->whereKeyNot($post->id)
            ->whereHas('tags', fn ($q) => $q->whereIn('tags.id', $post->tags->pluck('id')))
            ->latest('published_at')
            ->limit(4)
            ->get();

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $post->title,
            'description' => $post->meta_description ?: $post->excerpt,
            'datePublished' => $post->published_at?->toIso8601String(),
            'dateModified' => $post->updated_at?->toIso8601String(),
            'author' => ['@type' => 'Person', 'name' => $post->author->name],
            'publisher' => ['@type' => 'Organization', 'name' => 'JP LEVI INC.'],
            'mainEntityOfPage' => route('blog.show', $post),
        ];

        $comments = $post->comments()
            ->approved()
            ->whereNull('parent_id')
            ->with(['author', 'replies.author'])
            ->oldest()
            ->get();

        return view('blog.show', compact('post', 'related', 'schema', 'preview', 'comments'));
    }

    public function feed()
    {
        $posts = Post::published()->with('author')->latest('published_at')->limit(30)->get();

        return response()->view('blog.feed', compact('posts'))
            ->header('Content-Type', 'application/rss+xml; charset=utf-8');
    }

    public function sitemap()
    {
        $posts = Post::published()->latest('published_at')->get();

        return response()->view('blog.sitemap', compact('posts'))
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }
}
