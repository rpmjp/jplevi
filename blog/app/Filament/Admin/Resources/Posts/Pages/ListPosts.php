<?php

namespace App\Filament\Admin\Resources\Posts\Pages;

use App\Filament\Admin\Resources\Posts\PostResource;
use Filament\Actions\CreateAction;
use App\Models\Post;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    /**
     * Status tabs carrying live counts.
     *
     * The number is the point: a dropdown tells you a filter exists, a badge
     * tells you three things are waiting.
     */
    public function getTabs(): array
    {
        $count = fn (?string $status, bool $trashed = false) => function () use ($status, $trashed) {
            $query = $trashed ? Post::onlyTrashed() : Post::query();

            return $status ? $query->where('status', $status)->count() : $query->count();
        };

        return [
            'all' => Tab::make('All')->badge($count(null)),

            'published' => Tab::make('Published')
                ->badge($count('published'))
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'published')),

            'scheduled' => Tab::make('Scheduled')
                ->badge($count('scheduled'))
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'scheduled')),

            'draft' => Tab::make('Draft')
                ->badge($count('draft'))
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'draft')),

            'trash' => Tab::make('Trash')
                ->badge($count(null, trashed: true))
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $q) => $q->onlyTrashed()),
        ];
    }
}
