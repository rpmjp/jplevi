<?php

namespace App\Filament\Admin\Resources\Categories\Tables;

use App\Models\Category;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')->searchable()->sortable()
                    ->description(fn (Category $r) => $r->parent?->name ? 'in '.$r->parent->name : null),
                TextColumn::make('slug')->color('gray')->toggleable(),
                TextColumn::make('posts_count')->counts('posts')->label('Posts')->sortable(),
                IconColumn::make('indexed')
                    ->label('In search')
                    ->boolean()
                    ->state(fn (Category $r) => $r->shouldBeIndexed())
                    ->tooltip(fn (Category $r) => $r->shouldBeIndexed()
                        ? 'Indexed'
                        : 'Held back until '.Category::INDEX_THRESHOLD.' published posts'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
