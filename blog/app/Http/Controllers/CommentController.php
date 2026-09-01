<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Notifications\NewComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class CommentController extends Controller
{
    public function store(Request $request, Post $post)
    {
        abort_unless($post->acceptsComments(), 403, 'Comments are closed on this post.');

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

        $comment = Comment::create([
            'post_id' => $post->id,
            'user_id' => $user->id,
            'parent_id' => $parent?->parent_id ?? $parent?->id,
            'body' => $data['body'],
            'author_ip' => $request->ip(),
            'status' => 'pending',
        ]);

        // Moderators hear about everything; the person being replied to hears
        // about it only once the reply is approved, which happens in the panel.
        foreach (User::role(['admin', 'editor'])->get() as $moderator) {
            $moderator->notify(new NewComment($comment));
        }

        return back()->with('comment_status', 'Thanks, your comment is awaiting review.');
    }
}
