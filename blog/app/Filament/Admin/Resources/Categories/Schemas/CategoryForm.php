<?php

namespace App\Filament\Admin\Resources\Categories\Schemas;

use App\Models\Category;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->required()
                ->maxLength(80)
                ->live(onBlur: true)
                ->afterStateUpdated(fn (string $operation, $state, callable $set) => $operation === 'create'
                    ? $set('slug', Str::slug($state))
                    : null),

            TextInput::make('slug')->required()->unique(ignoreRecord: true),

            Select::make('parent_id')
                ->label('Inside')
                ->relationship('parent', 'name', fn ($query, ?Category $record) => $query
                    ->whereNull('parent_id')
                    ->when($record, fn ($q) => $q->whereKeyNot($record->id)))
                ->searchable()
                ->helperText('One level only. Deeper trees are a filing system nobody maintains.'),

            Textarea::make('intro')
                ->rows(3)
                ->columnSpanFull()
                ->maxLength(600)
                ->helperText('Shown on the archive page. Without one the archive is a list of links, which is not worth landing on.'),
        ])->columns(2);
    }
}
