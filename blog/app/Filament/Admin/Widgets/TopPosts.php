<?php

namespace App\Filament\Admin\Widgets;

use App\Models\PageView;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

/**
 * What is actually read, and where those readers came from.
 */
class TopPosts extends TableWidget
{
    protected static ?string $heading = 'Most read, last 30 days';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PageView::query()
                    ->selectRaw('min(id) as id, path, sum(views) as total')
                    ->whereNotNull('post_id')
                    ->where('viewed_on', '>=', now()->subDays(30)->toDateString())
                    ->groupBy('path')
                    ->orderByDesc('total'),
            )
            ->columns([
                TextColumn::make('path')->label('Post')->limit(60),
                TextColumn::make('total')->label('Reads')->numeric()->sortable(),
            ])
            ->paginated([10]);
    }
}
