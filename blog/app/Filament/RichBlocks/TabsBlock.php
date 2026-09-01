<?php

namespace App\Filament\RichBlocks;

use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Illuminate\Support\Str;

/**
 * Tabs, for showing three approaches without three screens of scrolling.
 *
 * The markup carries data attributes and no inline handlers, because the
 * content security policy refuses inline script and that policy is what stops a
 * comment becoming somebody else's JavaScript. The bundled script wires it up.
 */
class TabsBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'tabs';
    }

    public static function getLabel(): string
    {
        return 'Tabs';
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action
            ->modalHeading('Tabs')
            ->modalWidth('2xl')
            ->schema([
                Repeater::make('panels')
                    ->label('Tabs')
                    ->minItems(2)
                    ->maxItems(6)
                    ->defaultItems(2)
                    ->schema([
                        TextInput::make('label')->required()->maxLength(40),
                        Textarea::make('body')->rows(5)->required(),
                    ]),
            ]);
    }

    public static function toPreviewHtml(array $config): string
    {
        $labels = collect($config['panels'] ?? [])->pluck('label')->filter()->implode(' / ');

        return '<p><strong>Tabs:</strong> '.e($labels).'</p>';
    }

    public static function toHtml(array $config, array $data): ?string
    {
        $panels = collect($config['panels'] ?? [])->filter(fn ($p) => filled($p['label'] ?? null));

        if ($panels->isEmpty()) {
            return null;
        }

        return view('blocks.tabs', [
            'id' => 'tabs-'.Str::random(6),
            'panels' => $panels->values(),
        ])->render();
    }
}
