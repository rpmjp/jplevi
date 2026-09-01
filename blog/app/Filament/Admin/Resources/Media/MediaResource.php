<?php

namespace App\Filament\Admin\Resources\Media;

use App\Models\Media;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MediaResource extends Resource
{
    protected static string | \UnitEnum | null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 3;

    protected static ?string $model = Media::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationLabel = 'Media';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            FileUpload::make('path')
                ->image()
                ->required()
                ->directory('library')
                ->maxSize(10240)
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                ->columnSpanFull(),

            TextInput::make('alt')
                ->required()
                ->maxLength(200)
                ->label('Alt text')
                ->columnSpanFull()
                // Required, not encouraged. An image nobody described is an
                // image some readers simply do not get.
                ->helperText('Describe the image for anyone who cannot see it. Required.'),

            TextInput::make('caption')->maxLength(200)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('path')->label('')->height(56),
                TextColumn::make('alt')->label('Alt text')->wrap()->limit(80)->searchable(),
                TextColumn::make('caption')->limit(40)->toggleable(),
                TextColumn::make('created_at')->since()->label('Added')->sortable(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMedia::route('/'),
        ];
    }
}
