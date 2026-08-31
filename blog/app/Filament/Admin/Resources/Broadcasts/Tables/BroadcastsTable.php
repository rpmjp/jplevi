<?php

namespace App\Filament\Admin\Resources\Broadcasts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use App\Jobs\SendBroadcast;
use App\Models\Broadcast;
use App\Models\Subscriber;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Table;

class BroadcastsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('send')
                    ->icon('heroicon-m-paper-airplane')
                    ->color('success')
                    ->visible(fn (Broadcast $r) => $r->status === 'draft')
                    ->requiresConfirmation()
                    ->modalHeading('Send this issue')
                    ->modalDescription(fn () => 'It goes to '.Subscriber::mailable()->count().' confirmed subscribers. There is no recall.')
                    ->action(function (Broadcast $r) {
                        SendBroadcast::dispatch($r);
                        Notification::make()->title('Queued for sending')->success()->send();
                    }),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
