<?php

namespace App\Filament\Admin\Resources\Broadcasts\Schemas;

use App\Models\Post;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BroadcastForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('The inbox')->schema([
                TextInput::make('subject')
                    ->required()
                    ->maxLength(160)
                    ->helperText('Write it like a person, not a campaign.'),

                TextInput::make('preheader')
                    ->maxLength(160)
                    ->helperText('The grey line the inbox shows after the subject. Wasting it is a wasted open.'),
            ])->columns(2),

            Section::make('The issue')->schema([
                RichEditor::make('intro')
                    ->toolbarButtons([['bold', 'italic', 'link']])
                    ->helperText('A sentence or two before the posts. Optional.'),

                Select::make('posts')
                    ->relationship('posts', 'title')
                    ->multiple()
                    ->preload()
                    ->options(fn () => Post::published()->latest('published_at')->pluck('title', 'id'))
                    ->helperText('Each one renders with its cover image, excerpt and a link back to the post.'),

                RichEditor::make('body')
                    ->toolbarButtons([
                        ['bold', 'italic', 'link'],
                        ['h2', 'blockquote'],
                        ['bulletList', 'orderedList'],
                    ])
                    ->helperText('Anything after the posts. The unsubscribe footer is added automatically.'),
            ])->columns(1),
        ]);
    }
}
