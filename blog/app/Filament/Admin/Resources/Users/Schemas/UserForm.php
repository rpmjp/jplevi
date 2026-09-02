<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use App\Models\Rendition;
use App\Services\ImageIngest;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Support\Str;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(120),

            // Shown beside every post they write and every comment they leave.
            // Cut to three small widths rather than the cover set, and with no
            // 1.91:1 crop: a face is never a link preview.
            FileUpload::make('avatar_path')
                ->label('Profile photo')
                ->image()
                ->avatar()
                ->imageEditor()
                ->maxSize(4096)
                ->helperText('Square works best. Left empty, their initials are used instead.')
                ->saveUploadedFileUsing(fn (TemporaryUploadedFile $file) => app(ImageIngest::class)->store(
                    $file,
                    'avatars',
                    ImageIngest::AVATAR_WIDTHS,
                    social: false,
                    square: true,
                )['path'])
                ->getUploadedFileUsing(fn (?string $file) => filled($file) ? [
                    'name' => basename($file),
                    'url' => Rendition::url($file, 192),
                ] : null)
                ->deleteUploadedFileUsing(function (?string $file) {
                    foreach (ImageIngest::AVATAR_WIDTHS as $width) {
                        Storage::disk('media')->delete("{$file}-{$width}.webp");
                    }
                }),

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
