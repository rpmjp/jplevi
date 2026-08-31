<?php

namespace App\Filament\Admin\Resources\Comments\Tables;

use App\Models\Block;
use App\Models\Comment;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

/**
 * The moderation queue.
 *
 * Storing comments is easy; this screen is the actual work, so it is built to
 * clear a day in one pass: newest pending first, bulk approve and reject, and
 * blocking a person without leaving the page.
 */
class CommentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('author.name')->label('From')->searchable()->sortable(),
                TextColumn::make('body')->label('Comment')->wrap()->limit(160)->searchable(),
                TextColumn::make('post.title')->label('On')->limit(40)->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'warning',
                    }),
                TextColumn::make('created_at')->since()->sortable()->label('When'),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'pending' => 'Pending',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                ])->default('pending'),
            ])
            ->recordActions([
                Action::make('approve')
                    ->icon('heroicon-m-check')
                    ->color('success')
                    ->visible(fn (Comment $c) => $c->status !== 'approved')
                    ->action(function (Comment $c) {
                        $c->update(['status' => 'approved']);

                        // Only now is a reply visible, so only now is it worth
                        // telling the person it answers.
                        $parentAuthor = $c->parent_id
                            ? Comment::find($c->parent_id)?->author
                            : null;

                        if ($parentAuthor && $parentAuthor->isNot($c->author)) {
                            $parentAuthor->notify(new \App\Notifications\NewComment($c, isReply: true));
                        }
                    }),

                Action::make('reject')
                    ->icon('heroicon-m-x-mark')
                    ->color('danger')
                    ->visible(fn (Comment $c) => $c->status !== 'rejected')
                    ->action(fn (Comment $c) => $c->update(['status' => 'rejected'])),

                Action::make('block')
                    ->icon('heroicon-m-no-symbol')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalDescription('Blocks this address. Their future comments are dropped without telling them.')
                    ->action(function (Comment $c) {
                        Block::firstOrCreate(
                            ['value' => $c->author->email],
                            ['type' => 'email', 'reason' => 'Blocked from moderation'],
                        );
                        $c->update(['status' => 'rejected']);
                    }),
            ])
            ->toolbarActions([
                BulkAction::make('approve')
                    ->icon('heroicon-m-check')
                    ->color('success')
                    ->action(fn (Collection $records) => $records->each->update(['status' => 'approved'])),

                BulkAction::make('reject')
                    ->icon('heroicon-m-x-mark')
                    ->color('danger')
                    ->action(fn (Collection $records) => $records->each->update(['status' => 'rejected'])),
            ]);
    }
}
