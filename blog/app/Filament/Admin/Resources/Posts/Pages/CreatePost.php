<?php

namespace App\Filament\Admin\Resources\Posts\Pages;

use App\Filament\Admin\Resources\Posts\PostResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    /**
     * Categories and every other relationship are attached after the record is
     * saved, so the page cache flush that rides on saving has already run by
     * then. Editing only a post's categories would otherwise leave the old ones
     * on the public page until the cache expired an hour later.
     */
    protected function afterCreate(): void
    {
        \App\Http\Middleware\CacheResponse::flush();
    }
}
