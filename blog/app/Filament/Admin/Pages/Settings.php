<?php

namespace App\Filament\Admin\Pages;

use App\Settings as Store;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string|\UnitEnum|null $navigationGroup = 'Configuration';

    protected string $view = 'filament.pages.settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(Store::all());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Reading')->schema([
                    TextInput::make('posts_per_page')->numeric()->minValue(5)->maxValue(50)->required(),
                    Toggle::make('feed_full_text')
                        ->label('Send full posts in the feed')
                        ->helperText('Off sends the excerpt, which brings readers to the site.'),
                ])->columns(2),

                Section::make('Discussion')->schema([
                    Toggle::make('comments_open_by_default'),
                    TextInput::make('comments_close_after_days')
                        ->numeric()->minValue(0)
                        ->label('Close comments after (days)')
                        ->helperText('Zero leaves them open. A quiet old thread attracts spam far more reliably than conversation.'),
                    TextInput::make('moderation_email')
                        ->email()
                        ->label('Send moderation notices to')
                        ->helperText('Empty sends to every admin and editor.'),
                ])->columns(2),

                Section::make('Writing')->schema([
                    Select::make('default_audience')->options([
                        'buyers' => 'Buyers', 'engineers' => 'Engineers', 'both' => 'Both',
                    ])->required(),
                    TextInput::make('site_tagline')->maxLength(200)->columnSpanFull(),
                ])->columns(2),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')->label('Save')->submit('save'),
        ];
    }

    public function save(): void
    {
        Store::put($this->form->getState());

        Notification::make()->title('Saved')->success()->send();
    }
}
