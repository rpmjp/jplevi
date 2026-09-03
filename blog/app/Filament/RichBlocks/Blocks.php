<?php

namespace App\Filament\RichBlocks;

use App\Models\User;

/**
 * The editor's blocks, in one place.
 *
 * The list has to be identical in two places that are easy to let drift apart:
 * the writing screen, which offers the blocks, and the renderer, which turns
 * what was written back into HTML. A block missing from the second list is not
 * an error anywhere. It is simply absent from the published page, which is how
 * an author loses an afternoon's work without being told.
 */
class Blocks
{
    /** Everything the renderer must know about. */
    public static function all(): array
    {
        return [
            HtmlBlock::class,
            CalloutBlock::class,
            EmbedBlock::class,
            ButtonBlock::class,
            PullQuoteBlock::class,
            GalleryBlock::class,
            MediaTextBlock::class,
            FileBlock::class,
            TabsBlock::class,
            AccordionBlock::class,
            ReadMoreBlock::class,
        ];
    }

    /**
     * What a given writer is offered.
     *
     * Custom HTML is administrators only, the way WordPress gates it behind
     * unfiltered_html. Note that this narrows the editor, never the renderer: a
     * post that already contains a block still renders it, whoever opens it.
     */
    public static function offeredTo(?User $user): array
    {
        return array_values(array_filter(
            self::all(),
            fn (string $block) => $block !== HtmlBlock::class || $user?->hasRole('admin'),
        ));
    }
}
