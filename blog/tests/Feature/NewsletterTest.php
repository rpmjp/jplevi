<?php

namespace Tests\Feature;

use App\Mail\ConfirmSubscription;
use App\Models\Subscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class NewsletterTest extends TestCase
{
    use RefreshDatabase;

    public function test_signing_up_does_not_subscribe_anyone_until_they_confirm(): void
    {
        Mail::fake();

        $this->post('/newsletter/subscribe', ['email' => 'reader@example.com'])
            ->assertSessionHas('status');

        Mail::assertQueued(ConfirmSubscription::class);

        // On the list, but not mailable. That distinction is the whole point.
        $this->assertDatabaseHas('subscribers', ['email' => 'reader@example.com', 'confirmed_at' => null]);
        $this->assertSame(0, Subscriber::mailable()->count());

        $subscriber = Subscriber::first();
        $this->get('/newsletter/confirm/'.$subscriber->confirm_token)->assertOk();

        $this->assertSame(1, Subscriber::mailable()->count());
    }

    public function test_a_used_confirmation_link_cannot_be_replayed(): void
    {
        Mail::fake();
        $this->post('/newsletter/subscribe', ['email' => 'reader@example.com']);

        $token = Subscriber::first()->confirm_token;
        $this->get('/newsletter/confirm/'.$token)->assertOk();
        $this->get('/newsletter/confirm/'.$token)->assertNotFound();
    }

    public function test_one_click_unsubscribe_works_without_a_session_or_csrf_token(): void
    {
        $subscriber = Subscriber::create(['email' => 'reader@example.com']);
        $subscriber->confirm();

        $this->post('/newsletter/unsubscribe/'.$subscriber->unsubscribe_token)
            ->assertOk()
            ->assertSee('Unsubscribed');

        $this->assertSame(0, Subscriber::mailable()->count());
    }

    public function test_the_honeypot_rejects_a_bot(): void
    {
        Mail::fake();

        $this->post('/newsletter/subscribe', [
            'email' => 'bot@example.com',
            'company' => 'filled in by a bot',
        ])->assertSessionHasErrors('company');

        Mail::assertNothingQueued();
        $this->assertDatabaseCount('subscribers', 0);
    }

    public function test_signing_up_twice_does_not_reveal_that_an_address_is_known(): void
    {
        Mail::fake();
        $subscriber = Subscriber::create(['email' => 'reader@example.com']);
        $subscriber->confirm();

        $this->post('/newsletter/subscribe', ['email' => 'reader@example.com'])
            ->assertSessionHas('status');

        // No second mail to someone already confirmed, and no different answer.
        Mail::assertNothingQueued();
    }
}
