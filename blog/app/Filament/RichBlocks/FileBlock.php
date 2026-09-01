<?php

namespace App\Filament\RichBlocks;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;

/** A dataset or a notebook, offered as a download. */
class FileBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'file';
    }

    public static function getLabel(): string
    {
        return 'File download';
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action
            ->modalHeading('File download')
            ->schema([
                FileUpload::make('path')
                    ->required()
                    ->directory('downloads')
                    ->maxSize(20480)
                    ->acceptedFileTypes([
                        'text/csv', 'application/json', 'application/pdf', 'application/zip',
                        'application/x-ipynb+json', 'text/plain',
                    ])
                    ->helperText('CSV, JSON, PDF, ZIP, notebook or plain text.'),

                TextInput::make('label')->required()->maxLength(120),
                TextInput::make('note')->maxLength(160)->helperText('Optional. Size, row count, licence.'),
            ]);
    }

    public static function toPreviewHtml(array $config): string
    {
        return '<p><strong>Download:</strong> '.e($config['label'] ?? '').'</p>';
    }

    public static function toHtml(array $config, array $data): ?string
    {
        if (blank($config['path'] ?? null)) {
            return null;
        }

        return view('blocks.file', [
            'url' => \Illuminate\Support\Facades\Storage::url($config['path']),
            'label' => $config['label'] ?? 'Download',
            'note' => $config['note'] ?? null,
        ])->render();
    }
}
