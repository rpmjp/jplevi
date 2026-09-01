<?php

namespace App\Filament\Admin\Resources\Posts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

/**
 * The writing screen.
 *
 * Tabbed rather than one long form: writing, publishing and search metadata are
 * three different jobs, and only the first one is done every time.
 */
class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make()->columnSpanFull()->tabs([

                Tab::make('Write')->schema([
                    TextInput::make('title')
                        ->required()
                        ->maxLength(180)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (string $operation, $state, callable $set) {
                            // Only auto-slug before publication. Changing the
                            // slug afterwards is deliberate and creates a
                            // redirect, so it should not happen by accident.
                            if ($operation === 'create') {
                                $set('slug', Str::slug($state));
                            }
                        }),

                    TextInput::make('slug')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->helperText('Changing this on a published post leaves a permanent redirect behind.'),

                    Textarea::make('excerpt')
                        ->rows(2)
                        ->maxLength(320)
                        ->helperText('Shown on the index and used as the fallback meta description.'),

                    RichEditor::make('body')
                        ->columnSpanFull()
                        ->customBlocks([
                            \App\Filament\RichBlocks\CalloutBlock::class,
                            \App\Filament\RichBlocks\EmbedBlock::class,
                            \App\Filament\RichBlocks\ButtonBlock::class,
                            \App\Filament\RichBlocks\PullQuoteBlock::class,
                            \App\Filament\RichBlocks\GalleryBlock::class,
                            \App\Filament\RichBlocks\MediaTextBlock::class,
                            \App\Filament\RichBlocks\FileBlock::class,
                            \App\Filament\RichBlocks\TabsBlock::class,
                            \App\Filament\RichBlocks\AccordionBlock::class,
                            \App\Filament\RichBlocks\ReadMoreBlock::class,
                        ])
                        ->toolbarButtons([
                            ['bold', 'italic', 'strike', 'underline', 'highlight', 'link'],
                            ['h2', 'h3', 'lead', 'small'],
                            ['blockquote', 'codeBlock', 'details'],
                            ['bulletList', 'orderedList'],
                            ['table', 'grid', 'horizontalRule'],
                            ['alignStart', 'alignCenter', 'alignEnd'],
                            ['attachFiles', 'customBlocks'],
                            ['clearFormatting', 'undo', 'redo'],
                        ]),
                ])->columns(2),

                Tab::make('Publish')->schema([
                    Select::make('status')
                        ->required()
                        ->default('draft')
                        ->live()
                        ->options([
                            'draft' => 'Draft',
                            'scheduled' => 'Scheduled',
                            'published' => 'Published',
                        ]),

                    DateTimePicker::make('published_at')
                        ->seconds(false)
                        ->helperText('Leave empty to publish immediately.')
                        ->requiredIf('status', 'scheduled'),

                    Select::make('user_id')
                        ->relationship('author', 'name')
                        ->required()
                        ->default(fn () => auth()->id())
                        ->label('Author'),

                    Select::make('categories')
                        ->relationship('categories', 'name')
                        ->multiple()
                        ->preload()
                        ->createOptionForm([
                            TextInput::make('name')->required(),
                        ]),

                    Select::make('audience')
                        ->options(['buyers' => 'Buyers', 'engineers' => 'Engineers', 'both' => 'Both'])
                        ->default(fn () => \App\Settings::get('default_audience'))
                        ->required()
                        ->helperText('Who this is written for. Readers can filter by it.'),

                    FileUpload::make('cover_path')
                        ->image()
                        ->imageEditor()
                        ->directory('covers')
                        ->label('Cover image'),

                    TextInput::make('cover_alt')
                        ->label('Cover alt text')
                        ->helperText('Describe the image for anyone who cannot see it.'),
                ])->columns(2),

                Tab::make('Share')->schema([
                    \Filament\Forms\Components\Toggle::make('share_socially')
                        ->label('Post to social when published')
                        ->default(true),

                    Textarea::make('social_message')
                        ->rows(3)
                        ->maxLength(400)
                        ->label('What to say')
                        ->helperText('A good headline is rarely a good social post. The link is appended automatically. Left empty, the excerpt is used.'),

                    \Filament\Forms\Components\Toggle::make('comments_open')
                        ->label('Comments open')
                        ->default(true),
                ])->columns(1),

                Tab::make('Search')->schema([
                    TextInput::make('meta_title')
                        ->maxLength(70)
                        ->helperText('Defaults to the title.'),

                    Textarea::make('meta_description')
                        ->rows(2)
                        ->maxLength(320)
                        ->helperText('Defaults to the excerpt.'),

                    TextInput::make('canonical_url')
                        ->url()
                        ->helperText('Set only if this was published somewhere else first.'),
                ])->columns(1),
            ]),
        ]);
    }
}
