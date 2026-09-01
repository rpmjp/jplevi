<?php

namespace App\Filament\RichBlocks;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;

class ButtonBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'cta';
    }

    public static function getLabel(): string
    {
        return 'Button';
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action
            ->modalHeading('Button')
            ->schema([
                TextInput::make('label')->required()->maxLength(60),
                TextInput::make('url')->url()->required(),
                Select::make('style')
                    ->options(['solid' => 'Solid', 'outline' => 'Outline'])
                    ->default('solid'),
            ]);
    }

    public static function toPreviewHtml(array $config): string
    {
        return '<p><strong>Button:</strong> '.e($config['label'] ?? '').'</p>';
    }

    public static function toHtml(array $config, array $data): string
    {
        return view('blocks.button', [
            'label' => $config['label'] ?? 'Read more',
            'url' => $config['url'] ?? '#',
            'style' => $config['style'] ?? 'solid',
        ])->render();
    }
}
