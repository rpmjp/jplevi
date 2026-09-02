<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::published()
            ->with(['categories', 'author'])
            ->withCount(['comments' => fn ($q) => $q->approved()])
            ->when($request->string('topic')->toString(), fn ($q, $slug) => $q->whereHas('categories', fn ($c) => $c->where('slug', $slug)))
            ->when($request->string('audience')->toString(), fn ($q, $a) => $q->whereIn('audience', [$a, 'both']))
            ->when($request->string('q')->toString(), function ($q, $term) {
                // MySQL full text would be faster, but LIKE keeps this working
                // identically on SQLite locally and MySQL in production.
                $like = '%'.str_replace('%', '\%', $term).'%';
                $q->where(fn ($w) => $w->where('title', 'like', $like)
                    ->orWhere('excerpt', 'like', $like)
                    ->orWhere('body', 'like', $like));
            })
            ->latest('published_at')
            ->paginate(\App\Settings::get('posts_per_page'));

        // Filtered in PHP rather than with HAVING: the tag list is small, and
        // HAVING without GROUP BY is a MySQL nicety that SQLite rejects.
        $topics = Category::withCount(['posts' => fn ($q) => $q->published()])
            ->get()
            ->filter(fn (Category $c) => $c->posts_count > 0)
            ->sortByDesc('posts_count')
            ->values();

        return view('blog.index', compact('posts', 'topics'));
    }

    public function show(Request $request, string $slug)
    {
        $post = Post::with(['categories', 'author'])->where('slug', $slug)->firstOrFail();

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

        $related = self::related($post);

        $schema = array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $post->title,
            'description' => $post->meta_description ?: $post->excerpt,
            'datePublished' => $post->published_at?->toIso8601String(),
            'dateModified' => $post->updated_at?->toIso8601String(),
            'wordCount' => str_word_count(strip_tags((string) $post->body)),
            'timeRequired' => 'PT'.$post->reading_minutes.'M',
            'articleSection' => $post->categories->pluck('name')->all(),
            'keywords' => $post->categories->pluck('name')->implode(', '),
            'inLanguage' => 'en-US',
            'author' => array_filter([
                '@type' => 'Person',
                'name' => $post->author->name,
                'url' => route('blog.author', $post->author),
                'image' => $post->author->avatarUrl(400),
            ]),
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'JP LEVI INC.',
                'url' => 'https://jplevi.com',
            ],
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $post->canonical_url ?: route('blog.show', $post),
            ],
        ]);

        if ($image = \App\Models\Rendition::social($post->cover_path)) {
            $schema['image'] = [$image];
        }

        $comments = $post->comments()
            ->approved()
            ->whereNull('parent_id')
            ->with(['author', 'replies.author'])
            ->oldest()
            ->get();

        // The break is an instruction to the index, not content.
        $toc = self::headings(str_replace('<!--more-->', '', $post->body ?? ''));

        return view('blog.show', compact('post', 'related', 'schema', 'preview', 'comments', 'toc'));
    }

    /**
     * Pulls h2s out of a post and gives each one an id, so a long piece gets a
     * contents list and every heading is directly linkable.
     *
     * @return array{html:string, items:array<int,array{id:string,text:string}>}
     */
    public static function headings(string $html): array
    {
        if (! str_contains($html, '<h2')) {
            return ['html' => $html, 'items' => []];
        }

        $items = [];

        $html = preg_replace_callback('/<h2([^>]*)>(.*?)<\/h2>/is', function ($m) use (&$items) {
            $text = trim(strip_tags($m[2]));
            $id = \Illuminate\Support\Str::slug($text) ?: 'section-'.(count($items) + 1);
            $items[] = ['id' => $id, 'text' => $text];

            return '<h2 id="'.$id.'"'.$m[1].'>'.$m[2].'</h2>';
        }, $html) ?? $html;

        return ['html' => $html, 'items' => $items];
    }

    /**
     * Four posts to carry a reader onwards.
     *
     * Same topics first, because that is the strongest signal we have that they
     * will care. Topped up with whatever is newest when the overlap runs short,
     * so the row under a post is never one lonely card, and never empty on a
     * post that has no category on it yet.
     *
     * @return \Illuminate\Support\Collection<int,Post>
     */
    private static function related(Post $post, int $want = 4): \Illuminate\Support\Collection
    {
        $ids = $post->categories->pluck('id');

        $matching = $ids->isEmpty() ? collect() : Post::published()
            ->whereKeyNot($post->id)
            ->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $ids))
            ->with('categories')
            ->latest('published_at')
            ->limit($want)
            ->get();

        if ($matching->count() >= $want) {
            return $matching;
        }

        $filler = Post::published()
            ->whereKeyNot($post->id)
            ->whereNotIn('id', $matching->pluck('id'))
            ->with('categories')
            ->latest('published_at')
            ->limit($want - $matching->count())
            ->get();

        return $matching->concat($filler);
    }

    public function topic(Category $category)
    {
        $posts = Post::published()
            ->whereHas('categories', fn ($q) => $q->whereKey($category->id))
            ->with(['categories', 'author'])
            ->withCount(['comments' => fn ($q) => $q->approved()])
            ->latest('published_at')
            ->paginate(15);

        return view('blog.topic', [
            'category' => $category,
            'posts' => $posts,
            'children' => $category->children()->withCount(['posts' => fn ($q) => $q->published()])->get(),
            // Held back until the archive is worth landing on, which is the
            // whole reason this page does not simply always get indexed.
            'indexed' => $category->shouldBeIndexed(),
        ]);
    }

    public function author(User $user)
    {
        $posts = Post::published()
            ->where('user_id', $user->id)
            ->with(['categories', 'author'])
            ->withCount(['comments' => fn ($q) => $q->approved()])
            ->latest('published_at')
            ->paginate(15);

        return view('blog.author', compact('user', 'posts'));
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

        // Only archives worth landing on. A thin one in the sitemap is an
        // explicit invitation to index a page we asked not to be indexed.
        $topics = Category::all()->filter->shouldBeIndexed();

        return response()->view('blog.sitemap', compact('posts', 'topics'))
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }
}
