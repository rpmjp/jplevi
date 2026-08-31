<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompletenessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    private function live(User $author, string $title = 'A note', string $body = '<p>Body.</p>'): Post
    {
        return Post::create([
            'user_id' => $author->id, 'title' => $title, 'body' => $body,
            'status' => 'published', 'published_at' => now()->subHour(),
        ]);
    }

    public function test_author_pages_list_only_that_authors_published_work(): void
    {
        $a = User::factory()->create(['name' => 'Robert Jean Pierre']);
        $b = User::factory()->create(['name' => 'Someone Else']);

        $this->live($a, 'Mine');
        $this->live($b, 'Theirs');

        $this->get(route('blog.author', $a))
            ->assertOk()->assertSee('Mine')->assertDontSee('Theirs');
    }

    public function test_a_long_post_gets_a_contents_list_with_linkable_headings(): void
    {
        $body = '<h2>First part</h2><p>x</p><h2>Second part</h2><p>y</p><h2>Third part</h2><p>z</p>';
        $post = $this->live(User::factory()->create(), 'Long one', $body);

        $this->get('/blog/'.$post->slug)
            ->assertOk()
            ->assertSee('Contents')
            ->assertSee('id="first-part"', false)
            ->assertSee('href="#third-part"', false);
    }

    public function test_a_short_post_gets_no_contents_list(): void
    {
        $post = $this->live(User::factory()->create(), 'Short one', '<h2>Only heading</h2><p>x</p>');

        $this->get('/blog/'.$post->slug)->assertOk()->assertDontSee('Contents');
    }

    public function test_a_reader_can_see_and_delete_everything_held_about_them(): void
    {
        $reader = User::factory()->create();
        $reader->syncRoles(['subscriber']);
        $post = $this->live(User::factory()->create());

        Comment::create(['post_id' => $post->id, 'user_id' => $reader->id, 'body' => 'Mine', 'status' => 'approved']);

        $this->actingAs($reader)->get('/account')
            ->assertOk()->assertSee($reader->email)->assertSee('Mine');

        $this->actingAs($reader)->delete('/account')->assertRedirect(route('blog.index'));

        $this->assertDatabaseMissing('users', ['id' => $reader->id]);
        // Comments go with the account: removal means removal.
        $this->assertDatabaseCount('comments', 0);
    }

    public function test_an_author_cannot_delete_their_account_from_the_reader_page(): void
    {
        $author = User::factory()->create();
        $author->syncRoles(['author']);

        $this->actingAs($author)->delete('/account')->assertForbidden();
        $this->assertDatabaseHas('users', ['id' => $author->id]);
    }

    public function test_media_requires_alt_text_at_the_database_level(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        \App\Models\Media::create(['path' => 'library/x.webp']);
    }
}
