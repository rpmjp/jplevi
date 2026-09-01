<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(120),

            TextInput::make('email')
                ->email()
                ->required()
                ->unique(ignoreRecord: true)
                ->helperText('They sign in with this.'),

            Select::make('roles')
                ->relationship('roles', 'name')
                ->multiple()
                ->preload()
                ->required()
                ->helperText('Author writes their own. Editor publishes anyone. Admin also manages people.'),

            TextInput::make('password')
                ->password()
                ->revealable()
                ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                ->dehydrated(fn ($state) => filled($state))
                // Never set someone else's password silently: a created account
                // gets a random one and an invitation to change it.
                ->default(fn () => Str::password(20))
                ->helperText('Generated. Give it to them once, and have them change it.')
                ->required(fn (string $operation) => $operation === 'create'),
        ])->columns(2);
    }
}
