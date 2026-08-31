<?php

namespace App\Filament\Admin\Resources\Posts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Filters\TrashedFilter;
use App\Models\Post;
use Filament\Actions\Action;
use Filament\Tables\Table;

class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('preview')
                    ->icon('heroicon-m-eye')
                    ->label('Preview link')
                    ->visible(fn (Post $r) => $r->status !== 'published')
                    ->modalHeading('Share this draft')
                    ->modalDescription('Anyone with this link can read the draft. It stops working once the post is published.')
                    ->modalSubmitAction(false)
                    ->modalContent(fn (Post $r) => view('filament.preview-link', [
                        'url' => route('blog.show', $r).'?preview='.$r->preview_token,
                    ])),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
