<?php

namespace App\Filament\RichBlocks;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;

/**
 * A note, a warning, or a result.
 *
 * A research write up needs somewhere to say "this is the caveat" without
 * shouting it in bold, and somewhere to state the finding so a skimming reader
 * lands on it. Three tones, no colour picker: an author choosing arbitrary
 * colours is how a publication stops looking like one publication.
 */
class CalloutBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'callout';
    }

    public static function getLabel(): string
    {
        return 'Callout';
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action
            ->modalHeading('Callout')
            ->schema([
                Select::make('tone')
                    ->options([
                        'note' => 'Note',
                        'warning' => 'Warning',
                        'result' => 'Result',
                    ])
                    ->default('note')
                    ->required(),

                TextInput::make('title')
                    ->maxLength(120)
                    ->helperText('Optional. Left empty, the tone is the heading.'),

                Textarea::make('body')
                    ->rows(4)
                    ->required(),
            ]);
    }

    public static function toPreviewHtml(array $config): string
    {
        $tone = ucfirst($config['tone'] ?? 'note');
        $title = $config['title'] ?? '';

        return view('filament.blocks.callout-preview', [
            'tone' => $tone,
            'title' => $title,
            'body' => $config['body'] ?? '',
        ])->render();
    }

    public static function toHtml(array $config, array $data): string
    {
        return view('blocks.callout', [
            'tone' => $config['tone'] ?? 'note',
            'title' => $config['title'] ?? null,
            'body' => $config['body'] ?? '',
        ])->render();
    }
}
