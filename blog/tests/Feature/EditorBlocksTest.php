<?php

namespace Tests\Feature;

use App\Filament\RichBlocks\ButtonBlock;
use App\Filament\RichBlocks\CalloutBlock;
use App\Filament\RichBlocks\EmbedBlock;
use App\Filament\RichBlocks\AccordionBlock;
use App\Filament\RichBlocks\PullQuoteBlock;
use App\Filament\RichBlocks\ReadMoreBlock;
use App\Filament\RichBlocks\TabsBlock;
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

    public function test_tabs_carry_no_inline_handlers(): void
    {
        $html = TabsBlock::toHtml(['panels' => [
            ['label' => 'LightGBM', 'body' => 'Gradient boosting.'],
            ['label' => 'Neural net', 'body' => 'Slower, no better here.'],
        ]], []);

        $this->assertStringContainsString('LightGBM', $html);
        $this->assertStringContainsString('data-tab-target', $html);
        $this->assertStringContainsString('role="tablist"', $html);

        // The policy forbids inline script. Behaviour comes from the bundle.
        $this->assertStringNotContainsString('onclick', $html);
        $this->assertStringNotContainsString('<script', $html);
    }

    public function test_only_the_first_tab_panel_starts_visible(): void
    {
        $html = TabsBlock::toHtml(['panels' => [
            ['label' => 'One', 'body' => 'First.'],
            ['label' => 'Two', 'body' => 'Second.'],
            ['label' => 'Three', 'body' => 'Third.'],
        ]], []);

        $this->assertSame(2, substr_count($html, 'hidden'));
    }

    public function test_an_accordion_needs_no_javascript_at_all(): void
    {
        $html = AccordionBlock::toHtml(['items' => [
            ['question' => 'How long does it take?', 'answer' => 'Four to twelve weeks.'],
        ]], []);

        // Native details and summary: it opens even if the bundle never loads.
        $this->assertStringContainsString('<details', $html);
        $this->assertStringContainsString('<summary', $html);
        $this->assertStringNotContainsString('onclick', $html);
    }

    public function test_a_pull_quote_escapes_what_was_typed(): void
    {
        $html = PullQuoteBlock::toHtml([
            'text' => 'The model was answering a question <nobody> had.',
            'attribution' => 'A client, last March',
        ], []);

        $this->assertStringContainsString('A client, last March', $html);
        $this->assertStringNotContainsString('<nobody>', $html);
    }

    public function test_blocks_with_nothing_in_them_render_nothing(): void
    {
        $this->assertNull(TabsBlock::toHtml(['panels' => []], []));
        $this->assertNull(AccordionBlock::toHtml(['items' => []], []));
        $this->assertNull(\App\Filament\RichBlocks\GalleryBlock::toHtml(['media' => []], []));
        $this->assertNull(\App\Filament\RichBlocks\FileBlock::toHtml([], []));
    }
}
