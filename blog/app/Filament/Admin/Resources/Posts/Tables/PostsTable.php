<?php

namespace App\Filament\Admin\Resources\Posts\Tables;

use App\Models\Post;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

/**
 * The screen you live in.
 *
 * Almost everything here exists so a small change does not mean opening the
 * editor: quick edit in the row, bulk edit across a selection, and counts on
 * the status tabs so the state of the blog reads at a glance.
 */
class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('published_at', 'desc')
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn (Post $r) => $r->slug)
                    ->limit(60),

                TextColumn::make('author.name')->label('Author')->sortable()->toggleable(),

                TextColumn::make('categories.name')->badge()->label('Categories')->toggleable(),

                TextColumn::make('comments_count')
                    ->counts('comments')
                    ->label('Comments')
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'primary' : 'gray')
                    ->url(fn (Post $r) => '/blog/admin/comments?tableFilters[post][value]='.$r->id),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'published' => 'success',
                        'scheduled' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('published_at')
                    ->label('Date')
                    ->dateTime('j M Y, H:i')
                    ->sortable()
                    ->placeholder('Not scheduled'),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'draft' => 'Draft',
                    'scheduled' => 'Scheduled',
                    'published' => 'Published',
                ]),
                SelectFilter::make('categories')->relationship('categories', 'name')->multiple(),
                SelectFilter::make('user_id')->relationship('author', 'name')->label('Author'),
                TrashedFilter::make(),
            ], layout: FiltersLayout::AboveContent)
            ->recordActions([
                Action::make('quickEdit')
                    ->label('Quick edit')
                    ->icon('heroicon-m-pencil-square')
                    ->modalWidth('2xl')
                    ->fillForm(fn (Post $r) => [
                        'title' => $r->title,
                        'slug' => $r->slug,
                        'published_at' => $r->published_at,
                        'user_id' => $r->user_id,
                        'status' => $r->status,
                        'audience' => $r->audience,
                        'categories' => $r->categories->pluck('id')->all(),
                        'comments_open' => $r->comments_open,
                    ])
                    ->schema([
                        TextInput::make('title')->required(),
                        TextInput::make('slug')->required()
                            ->helperText('Changing this leaves a permanent redirect behind.'),
                        Select::make('status')->options([
                            'draft' => 'Draft', 'scheduled' => 'Scheduled', 'published' => 'Published',
                        ])->required(),
                        DateTimePicker::make('published_at')->seconds(false),
                        Select::make('user_id')->relationship('author', 'name')->label('Author')->required(),
                        Select::make('audience')->options([
                            'buyers' => 'Buyers', 'engineers' => 'Engineers', 'both' => 'Both',
                        ])->required(),
                        Select::make('categories')->relationship('categories', 'name')->multiple()->preload(),
                        Toggle::make('comments_open'),
                    ])
                    ->action(function (Post $record, array $data) {
                        $record->update(collect($data)->except('categories')->all());
                        $record->categories()->sync($data['categories'] ?? []);

                        Notification::make()->title('Saved')->success()->send();
                    }),

                Action::make('preview')
                    ->label('Preview')
                    ->icon('heroicon-m-eye')
                    ->url(fn (Post $r) => $r->status === 'published'
                        ? route('blog.show', $r)
                        : route('blog.show', $r).'?preview='.$r->preview_token)
                    ->openUrlInNewTab(),

                EditAction::make(),
                DeleteAction::make()->label('Trash'),
                RestoreAction::make(),
                ForceDeleteAction::make()->label('Delete permanently'),
            ])
            ->toolbarActions([
                BulkAction::make('bulkEdit')
                    ->label('Bulk edit')
                    ->icon('heroicon-m-pencil')
                    ->schema([
                        Select::make('status')->options([
                            'draft' => 'Draft', 'scheduled' => 'Scheduled', 'published' => 'Published',
                        ])->placeholder('Leave unchanged'),
                        Select::make('user_id')->relationship('author', 'name')->label('Author')->placeholder('Leave unchanged'),
                        Select::make('audience')->options([
                            'buyers' => 'Buyers', 'engineers' => 'Engineers', 'both' => 'Both',
                        ])->placeholder('Leave unchanged'),
                        Select::make('add_categories')
                            ->label('Add categories')
                            ->multiple()->preload()
                            ->relationship('categories', 'name')
                            // Added, never replaced: a bulk action that silently
                            // wiped categories would be very hard to undo.
                            ->helperText('Added to what is already there. Nothing is removed.'),
                        Toggle::make('comments_open')->default(null),
                    ])
                    ->action(function (Collection $records, array $data) {
                        $fields = collect($data)
                            ->only(['status', 'user_id', 'audience', 'comments_open'])
                            ->filter(fn ($v) => $v !== null && $v !== '')
                            ->all();

                        foreach ($records as $record) {
                            if ($fields) {
                                $record->update($fields);
                            }

                            if (filled($data['add_categories'] ?? null)) {
                                $record->categories()->syncWithoutDetaching($data['add_categories']);
                            }
                        }

                        Notification::make()->title($records->count().' posts updated')->success()->send();
                    }),
            ]);
    }
}
