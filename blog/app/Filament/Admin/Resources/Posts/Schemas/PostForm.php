<?php

namespace App\Filament\Admin\Resources\Posts\Schemas;

use App\Models\Rendition;
use App\Services\ImageIngest;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

/**
 * The writing screen.
 *
 * Laid out the way WordPress lays it out, because that is the shape anybody who
 * has written a post before already knows: the writing fills the left, and the
 * decisions about the post sit in panels down the right where they stay visible
 * while you write.
 *
 * The earlier version had these behind tabs, which meant the featured image, the
 * categories and the publish date could only be reached by leaving the editor.
 * That is the wrong trade. A tab is right for something you do occasionally; the
 * sidebar is right for the things that are part of finishing every post.
 */
class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            // columnSpanFull matters here: the resource page puts the schema in
            // a two column grid of its own, so without it this whole layout is
            // squeezed into half the page and the sidebar ends up a sliver.
            Grid::make(3)->columnSpanFull()->schema([

                /* ---- The writing ---------------------------------------- */
                Group::make()->columnSpan(['default' => 3, 'lg' => 2])->schema([

                    Section::make()->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(180)
                            ->live(onBlur: true)
                            ->placeholder('Add title')
                            ->hiddenLabel()
                            // Styled in the admin theme. An attribute hook
                            // rather than a guessed id: the form is mounted by
                            // Livewire, so the generated id is not something to
                            // write a stylesheet against.
                            ->extraInputAttributes(['data-post-title' => 'true'])
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
                            // Generated, never assembled. url('/blog') put a
                            // second /blog on the end of an APP_URL that
                            // already carried one; asking the router for the
                            // index gives whatever the posts actually sit under.
                            ->prefix(rtrim(route('blog.index'), '/').'/')
                            ->helperText('Changing this on a published post leaves a permanent redirect behind.'),

                        RichEditor::make('body')
                            ->hiddenLabel()
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
                    ]),

                    Section::make('Excerpt')
                        ->description('Shown on the index and used as the fallback meta description. Left empty, the opening of the post is used instead.')
                        ->collapsible()
                        ->schema([
                            Textarea::make('excerpt')
                                ->hiddenLabel()
                                ->rows(3)
                                ->maxLength(320),
                        ]),

                    Section::make('Search appearance')
                        ->description('What Google shows. Both fall back to the title and the excerpt, so this is only worth filling in when you want something different.')
                        ->collapsible()
                        ->collapsed()
                        ->schema([
                            TextInput::make('meta_title')
                                ->maxLength(70)
                                ->helperText('Defaults to the title. Around 60 characters is where Google starts trimming.'),

                            Textarea::make('meta_description')
                                ->rows(2)
                                ->maxLength(320)
                                ->helperText('Defaults to the excerpt.'),

                            TextInput::make('canonical_url')
                                ->url()
                                ->helperText('Set only if this was published somewhere else first.'),
                        ]),
                ]),

                /* ---- The sidebar ---------------------------------------- */
                Group::make()->columnSpan(['default' => 3, 'lg' => 1])->schema([

                    Section::make('Publish')
                        ->icon('heroicon-m-paper-airplane')
                        ->schema([
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
                                ->label('Date')
                                ->seconds(false)
                                ->helperText('Leave empty to publish immediately.')
                                ->requiredIf('status', 'scheduled'),

                            Select::make('user_id')
                                ->relationship('author', 'name')
                                ->required()
                                ->default(fn () => auth()->id())
                                ->label('Author'),
                        ]),

                    /* The panel this was missing.

                       Uploads go through ImageIngest rather than straight to
                       disk. That re-encodes the file, writes the four widths the
                       index and the article hero pick from, and cuts the 1200x630
                       crop that link previews unfurl. What lands in the column is
                       the basename; every URL is derived from it. */
                    Section::make('Featured image')
                        ->icon('heroicon-m-photo')
                        ->description('Used on the index, at the top of the post, and in every link preview.')
                        ->schema([
                            FileUpload::make('cover_path')
                                ->hiddenLabel()
                                ->image()
                                ->imageEditor()
                                ->imageEditorAspectRatios(['16:9', '1.91:1', '4:3', null])
                                ->maxSize(8192)
                                ->panelAspectRatio('16:9')
                                ->panelLayout('integrated')
                                ->uploadingMessage('Resizing and cropping…')
                                ->helperText('1600px wide or more gives the sharpest result. Four sizes and a 1200x630 share crop are made for you.')
                                ->saveUploadedFileUsing(
                                    fn (TemporaryUploadedFile $file) => app(ImageIngest::class)->store($file, 'covers')['path'],
                                )
                                ->getUploadedFileUsing(fn (?string $file) => filled($file) ? [
                                    'name' => basename($file),
                                    'url' => Rendition::url($file, 800),
                                ] : null)
                                ->deleteUploadedFileUsing(function (?string $file) {
                                    // Every rendition of one upload, including the crop.
                                    foreach ([...ImageIngest::WIDTHS, 'social'] as $suffix) {
                                        Storage::disk('media')->delete("{$file}-{$suffix}.webp");
                                    }
                                }),

                            TextInput::make('cover_alt')
                                ->label('Alt text')
                                ->helperText('Describe the image for anyone who cannot see it. Left empty, it is treated as decoration and skipped by screen readers.'),
                        ]),

                    Section::make('Categories')
                        ->icon('heroicon-m-folder')
                        ->schema([
                            Select::make('categories')
                                ->hiddenLabel()
                                ->relationship('categories', 'name')
                                ->multiple()
                                ->preload()
                                ->createOptionForm([
                                    TextInput::make('name')->required(),
                                ])
                                ->helperText('The first one is shown in the byline and above the headline.'),

                            Select::make('audience')
                                ->options(['buyers' => 'Buyers', 'engineers' => 'Engineers', 'both' => 'Both'])
                                ->default(fn () => \App\Settings::get('default_audience'))
                                ->required()
                                ->helperText('Who this is written for. Readers can filter by it.'),
                        ]),

                    Section::make('Discussion')
                        ->icon('heroicon-m-chat-bubble-left-right')
                        ->collapsible()
                        ->schema([
                            Toggle::make('comments_open')
                                ->label('Allow comments')
                                ->default(true),
                        ]),

                    Section::make('Sharing')
                        ->icon('heroicon-m-megaphone')
                        ->collapsible()
                        ->collapsed()
                        ->schema([
                            Toggle::make('share_socially')
                                ->label('Post to social when published')
                                ->default(true),

                            Textarea::make('social_message')
                                ->rows(3)
                                ->maxLength(400)
                                ->label('What to say')
                                ->helperText('A good headline is rarely a good social post. Feeds fold at roughly 200 characters behind a "more" link, so the first two lines have to carry it. The article link is appended for you, and the featured image is posted with it. Left empty, the excerpt is used.'),
                        ]),
                ]),
            ]),
        ]);
    }
}
