<?php

namespace App\Filament\Admin\Resources\Posts\Pages;

use App\Filament\Admin\Resources\Posts\PostResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    /**
     * Categories and every other relationship are attached after the record is
     * saved, so the page cache flush that rides on saving has already run by
     * then. Editing only a post's categories would otherwise leave the old ones
     * on the public page until the cache expired an hour later.
     */
    protected function afterSave(): void
    {
        \App\Http\Middleware\CacheResponse::flush();
    }
}
