<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

/**
 * Tells a moderator a comment is waiting, or an author that someone replied.
 *
 * Queued, so a slow mail provider never delays the reader who just posted.
 */
class NewComment extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Comment $comment, public bool $isReply = false) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $post = $this->comment->post;

        return (new MailMessage)
            ->subject($this->isReply
                ? 'Someone replied to you on '.$post->title
                : 'A comment is waiting on '.$post->title)
            ->greeting($this->isReply ? 'You have a reply' : 'A comment needs review')
            ->line($this->comment->author->name.' wrote:')
            ->line(Str::limit($this->comment->body, 400))
            ->action(
                $this->isReply ? 'Read it' : 'Moderate',
                $this->isReply ? route('blog.show', $post).'#comments' : url('/admin/comments'),
            );
    }
}
