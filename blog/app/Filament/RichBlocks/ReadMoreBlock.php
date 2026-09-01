<?php

namespace App\Filament\RichBlocks;

use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;

/**
 * Marks where the lead ends.
 *
 * The index shows everything above this instead of a truncated excerpt, which
 * means a listing reads as a written opening rather than a sentence cut off.
 */
class ReadMoreBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'read-more';
    }

    public static function getLabel(): string
    {
        return 'Read more break';
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action
            ->modalHeading('Read more break')
            ->modalDescription('Everything above this point is used as the lead on the index.')
            ->schema([]);
    }

    public static function toPreviewHtml(array $config): string
    {
        return '<hr><p><small>Read more break</small></p>';
    }

    public static function toHtml(array $config, array $data): string
    {
        return '<!--more-->';
    }
}
