<?php

namespace App\Filament\RichBlocks;

use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;

/** A line worth setting large. Distinct from a block quote, which cites. */
class PullQuoteBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'pull-quote';
    }

    public static function getLabel(): string
    {
        return 'Pull quote';
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action->modalHeading('Pull quote')->schema([
            Textarea::make('text')->rows(3)->required()->maxLength(280),
            TextInput::make('attribution')->maxLength(120),
        ]);
    }

    public static function toPreviewHtml(array $config): string
    {
        return '<p><em>'.e($config['text'] ?? '').'</em></p>';
    }

    public static function toHtml(array $config, array $data): string
    {
        return view('blocks.pull-quote', [
            'text' => $config['text'] ?? '',
            'attribution' => $config['attribution'] ?? null,
        ])->render();
    }
}
