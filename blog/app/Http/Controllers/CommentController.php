<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class CommentController extends Controller
{
    public function store(Request $request, Post $post)
    {
        abort_unless($post->comments_open, 403, 'Comments are closed on this post.');

        $user = $request->user();

        if (Block::blocks($user->email, $request->ip())) {
            // Say nothing useful: a blocked user learning they are blocked
            // just starts again from a new address.
            return back()->with('comment_status', 'Thanks, your comment is awaiting review.');
        }

        $key = 'comment:'.$user->id;

        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->withErrors(['body' => 'You are posting too quickly. Try again in a few minutes.']);
        }

        RateLimiter::hit($key, 600);

        $data = $request->validate([
            'body' => ['required', 'string', 'min:2', 'max:4000'],
            'parent_id' => ['nullable', 'integer', 'exists:comments,id'],
            'website' => ['prohibited'],
        ]);

        // Replies go one level deep: a reply to a reply attaches to its parent.
        $parent = $data['parent_id'] ?? null
            ? Comment::find($data['parent_id'])
            : null;

        Comment::create([
            'post_id' => $post->id,
            'user_id' => $user->id,
            'parent_id' => $parent?->parent_id ?? $parent?->id,
            'body' => $data['body'],
            'author_ip' => $request->ip(),
            'status' => 'pending',
        ]);

        return back()->with('comment_status', 'Thanks, your comment is awaiting review.');
    }
}
