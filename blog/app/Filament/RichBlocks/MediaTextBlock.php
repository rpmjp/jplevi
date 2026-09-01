<?php

namespace App\Filament\RichBlocks;

use App\Models\Media;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;

/** A figure with its explanation beside it rather than under it. */
class MediaTextBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'media-text';
    }

    public static function getLabel(): string
    {
        return 'Image and text';
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action
            ->modalHeading('Image beside text')
            ->schema([
                Select::make('media_id')
                    ->label('Image')
                    ->required()
                    ->options(fn () => Media::latest()->pluck('alt', 'id')),

                Select::make('side')
                    ->options(['left' => 'Image on the left', 'right' => 'Image on the right'])
                    ->default('left'),

                Textarea::make('text')->rows(5)->required(),
            ]);
    }

    public static function toPreviewHtml(array $config): string
    {
        return '<p><strong>Image and text:</strong> '.e(\Illuminate\Support\Str::limit($config['text'] ?? '', 80)).'</p>';
    }

    public static function toHtml(array $config, array $data): ?string
    {
        $media = Media::find($config['media_id'] ?? null);

        if (! $media) {
            return null;
        }

        return view('blocks.media-text', [
            'media' => $media,
            'side' => $config['side'] ?? 'left',
            'text' => $config['text'] ?? '',
        ])->render();
    }
}
