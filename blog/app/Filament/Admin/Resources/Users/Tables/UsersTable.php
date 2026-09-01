<?php

namespace App\Filament\Admin\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')->searchable()->sortable()
                    ->description(fn (User $r) => $r->email),
                TextColumn::make('roles.name')->badge()
                    ->color(fn (string $state) => match ($state) {
                        'admin' => 'danger',
                        'editor' => 'warning',
                        'author' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('posts_count')->counts('posts')->label('Posts')->sortable(),
                TextColumn::make('created_at')->label('Joined')->date('j M Y')->sortable(),
            ])
            ->filters([
                SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->label('Role'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    // Removing the last admin locks everyone out of the panel
                    // permanently. That is a one way door, so it is closed.
                    ->before(function (User $record, DeleteAction $action) {
                        if (self::wouldRemoveLastAdmin($record)) {
                            Notification::make()
                                ->title('That is the only admin')
                                ->body('Give someone else the admin role first, or nobody can reach the panel.')
                                ->danger()->send();

                            $action->cancel();
                        }
                    }),
            ]);
    }

    public static function wouldRemoveLastAdmin(User $user): bool
    {
        return $user->hasRole('admin')
            && Role::findByName('admin')->users()->count() <= 1;
    }
}
