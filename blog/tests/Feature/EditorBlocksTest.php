<?php

namespace Tests\Feature;

use App\Filament\RichBlocks\ButtonBlock;
use App\Filament\RichBlocks\CalloutBlock;
use App\Filament\RichBlocks\EmbedBlock;
use App\Filament\RichBlocks\ReadMoreBlock;
use Tests\TestCase;

class EditorBlocksTest extends TestCase
{
    public function test_embeds_accept_youtube_and_vimeo_and_refuse_everything_else(): void
    {
        $this->assertSame('youtube', EmbedBlock::parse('https://www.youtube.com/watch?v=dQw4w9WgXcQ')['provider']);
        $this->assertSame('youtube', EmbedBlock::parse('https://youtu.be/dQw4w9WgXcQ')['provider']);
        $this->assertSame('vimeo', EmbedBlock::parse('https://vimeo.com/76979871')['provider']);

        // Refused here rather than rendering a blank frame the policy blocks.
        $this->assertNull(EmbedBlock::parse('https://example.com/video'));
        $this->assertNull(EmbedBlock::parse('https://www.tiktok.com/@x/video/123'));
        $this->assertNull(EmbedBlock::parse(null));
    }

    public function test_an_embed_renders_the_domain_the_policy_permits(): void
    {
        $html = EmbedBlock::toHtml(['url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'caption' => 'A demo'], []);

        // youtube-nocookie, not youtube.com: the frame-src list allows only that.
        $this->assertStringContainsString('youtube-nocookie.com/embed/dQw4w9WgXcQ', $html);
        $this->assertStringContainsString('A demo', $html);
        $this->assertStringNotContainsString('src="https://www.youtube.com', $html);
    }

    public function test_a_callout_escapes_what_an_author_types(): void
    {
        $html = CalloutBlock::toHtml([
            'tone' => 'warning',
            'title' => 'Careful',
            'body' => '<script>alert(1)</script>',
        ], []);

        $this->assertStringContainsString('Careful', $html);
        $this->assertStringNotContainsString('<script>', $html);
    }

    public function test_a_button_renders_both_styles(): void
    {
        $solid = ButtonBlock::toHtml(['label' => 'Read it', 'url' => 'https://jplevi.com', 'style' => 'solid'], []);
        $outline = ButtonBlock::toHtml(['label' => 'Read it', 'url' => 'https://jplevi.com', 'style' => 'outline'], []);

        $this->assertStringContainsString('bg-brand', $solid);
        $this->assertStringNotContainsString('bg-brand', $outline);
    }

    public function test_the_read_more_break_emits_a_marker_the_index_can_split_on(): void
    {
        $this->assertSame('<!--more-->', ReadMoreBlock::toHtml([], []));
    }

    public function test_the_lead_stops_at_the_break_and_the_post_never_shows_the_marker(): void
    {
        $post = \App\Models\Post::make([
            'title' => 'A note',
            'body' => '<p>The opening paragraph.</p><!--more--><p>The rest of it.</p>',
        ]);

        $this->assertStringContainsString('The opening paragraph.', $post->lead());
        $this->assertStringNotContainsString('The rest of it.', $post->lead());
        $this->assertStringNotContainsString('<!--more-->', $post->lead());
    }
}
