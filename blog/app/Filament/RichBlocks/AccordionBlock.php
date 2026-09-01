<?php

namespace App\Filament\RichBlocks;

use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;

/**
 * A stack of collapsibles.
 *
 * Built on native details and summary, so it opens and closes with no
 * JavaScript at all and still works if the script never loads.
 */
class AccordionBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'accordion';
    }

    public static function getLabel(): string
    {
        return 'Accordion';
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action
            ->modalHeading('Accordion')
            ->modalWidth('2xl')
            ->schema([
                Repeater::make('items')
                    ->minItems(1)
                    ->defaultItems(2)
                    ->schema([
                        TextInput::make('question')->required()->maxLength(140),
                        Textarea::make('answer')->rows(4)->required(),
                    ]),
            ]);
    }

    public static function toPreviewHtml(array $config): string
    {
        return '<p><strong>Accordion:</strong> '.count($config['items'] ?? []).' items</p>';
    }

    public static function toHtml(array $config, array $data): ?string
    {
        $items = collect($config['items'] ?? [])->filter(fn ($i) => filled($i['question'] ?? null));

        return $items->isEmpty() ? null : view('blocks.accordion', ['items' => $items])->render();
    }
}
