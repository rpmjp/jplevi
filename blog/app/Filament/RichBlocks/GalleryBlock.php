<?php

namespace App\Filament\RichBlocks;

use App\Models\Media;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;

/**
 * A set of images from the media library.
 *
 * Chosen from the library rather than uploaded again, so the alt text written
 * once is the alt text used everywhere the image appears.
 */
class GalleryBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'gallery';
    }

    public static function getLabel(): string
    {
        return 'Gallery';
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action
            ->modalHeading('Gallery')
            ->schema([
                Select::make('media')
                    ->label('Images')
                    ->multiple()
                    ->required()
                    ->options(fn () => Media::latest()->pluck('alt', 'id'))
                    ->helperText('Alt text comes with each image, so it is never missing here.'),

                Select::make('columns')
                    ->options([2 => 'Two across', 3 => 'Three across'])
                    ->default(2),

                TextInput::make('caption')->maxLength(200),
            ]);
    }

    public static function toPreviewHtml(array $config): string
    {
        return '<p><strong>Gallery</strong> of '.count($config['media'] ?? []).' images</p>';
    }

    public static function toHtml(array $config, array $data): ?string
    {
        $items = Media::findMany($config['media'] ?? []);

        if ($items->isEmpty()) {
            return null;
        }

        return view('blocks.gallery', [
            'items' => $items,
            'columns' => (int) ($config['columns'] ?? 2),
            'caption' => $config['caption'] ?? null,
        ])->render();
    }
}
