<?php

namespace App\Filament\Admin\Resources\Media\Pages;

use App\Filament\Admin\Resources\Media\MediaResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListMedia extends ListRecords
{
    protected static string $resource = MediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('upload')
                ->label('Upload')
                ->icon('heroicon-m-arrow-up-tray')
                ->modalHeading('Add to the library')
                ->modalSubmitActionLabel('Upload')
                ->schema([MediaResource::uploadField()])
                ->action(function (array $data) {
                    $added = 0;
                    $failed = [];

                    // One at a time, and a file that will not decode is
                    // reported by name rather than taking the whole batch down
                    // with it. Losing nineteen good uploads to one bad file is
                    // the thing that makes bulk upload not worth using.
                    foreach ($data['files'] ?? [] as $file) {
                        try {
                            MediaResource::ingest($file);
                            $added++;
                        } catch (\Throwable $e) {
                            $failed[] = $file->getClientOriginalName();
                            report($e);
                        }
                    }

                    if ($added) {
                        Notification::make()
                            ->success()
                            ->title($added.' '.str('image')->plural($added).' added')
                            ->body('Give each one a description so readers who cannot see it still get it.')
                            ->send();
                    }

                    if ($failed) {
                        Notification::make()
                            ->warning()
                            ->title('Some files were not added')
                            ->body(implode(', ', $failed).' could not be read as an image.')
                            ->persistent()
                            ->send();
                    }
                }),
        ];
    }
}
