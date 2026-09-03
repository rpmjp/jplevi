<?php

namespace App\Filament\Admin\Resources\Media;

use App\Models\Media;
use App\Services\ImageIngest;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

/**
 * The image library.
 *
 * Uploads go through ImageIngest onto the media disk, the same as post covers,
 * rather than to the public one. That is what gives every image a direct link
 * worth pasting into a post: it is served by a controller from outside the web
 * root, re-encoded, stripped of EXIF, and available at four widths behind one
 * stable address that names the image rather than one rendition of it.
 */
class MediaResource extends Resource
{
    protected static ?string $recordTitleAttribute = 'title';

    protected static string | \UnitEnum | null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 3;

    protected static ?string $model = Media::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationLabel = 'Media';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(2)->schema([
                TextInput::make('title')
                    ->maxLength(200)
                    ->helperText('What this image is called in the library. Not shown to readers.'),

                TextInput::make('alt')
                    ->label('Alt text')
                    ->maxLength(200)
                    // Not required at the database any more, so that a folder
                    // of images can be uploaded in one go. Still the thing that
                    // decides whether some readers get the image at all, which
                    // is why the library flags every one that is missing it.
                    ->helperText('Describe the image for anyone who cannot see it. Left empty, the image is treated as decoration and skipped entirely.'),

                TextInput::make('caption')
                    ->maxLength(200)
                    ->helperText('Printed under the image. Left empty, none is shown.'),

                Textarea::make('description')
                    ->rows(3)
                    ->helperText('Notes for yourself. Never published.'),
            ]),

            Section::make('Direct link')
                ->description('The address of this image. Paste it anywhere, or take the whole tag to drop it into a Custom HTML block with its widths and description already set.')
                ->schema([
                    TextInput::make('direct_url')
                        ->label('URL')
                        ->readOnly()
                        ->copyable()
                        ->dehydrated(false)
                        ->afterStateHydrated(fn ($component, $record) => $component->state($record?->url())),

                    Textarea::make('embed_code')
                        ->label('Image tag')
                        ->readOnly()
                        ->rows(6)
                        ->dehydrated(false)
                        ->extraInputAttributes(['style' => 'font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: .78rem'])
                        ->afterStateHydrated(fn ($component, $record) => $component->state($record?->embedCode())),
                ])
                ->hidden(fn ($record) => $record === null),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('path')
                    ->label('')
                    ->height(56)
                    ->getStateUsing(fn (Media $record) => $record->url(400)),

                TextColumn::make('title')
                    ->searchable()
                    ->weight('medium')
                    ->description(fn (Media $record) => $record->alt ?: 'No description yet')
                    ->color(fn (Media $record) => blank($record->alt) ? 'warning' : null),

                TextColumn::make('caption')->limit(40)->toggleable()->searchable(),

                TextColumn::make('dimensions')
                    ->label('Size')
                    ->state(fn (Media $record) => collect([$record->dimensions(), $record->fileSize()])->filter()->implode(' · '))
                    ->toggleable(),

                // The point of the library. Copyable rather than merely shown,
                // because reading a URL off a screen and typing it back in is
                // how a link ends up with a character missing.
                TextColumn::make('direct_url')
                    ->label('Link')
                    ->state(fn (Media $record) => $record->url())
                    ->copyable()
                    ->copyMessage('Link copied')
                    ->limit(30)
                    ->tooltip(fn (Media $record) => $record->url())
                    ->toggleable(),

                // Shows a label, copies the markup. copyableState is what
                // separates the two, so the cell stays readable while what
                // lands on the clipboard is the whole tag with its widths and
                // description already filled in.
                TextColumn::make('embed')
                    ->label('Tag')
                    ->state('Copy <img>')
                    ->badge()
                    ->color('gray')
                    ->copyable()
                    ->copyableState(fn (Media $record) => $record->embedCode())
                    ->copyMessage('Image tag copied')
                    ->toggleable(),

                TextColumn::make('created_at')->since()->label('Added')->sortable(),
            ])
            ->filters([
                Filter::make('undescribed')
                    ->label('Missing alt text')
                    ->query(fn ($query) => $query->undescribed()),
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make(),
            ])
            ->toolbarActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * Puts one uploaded file into the library.
     *
     * Shared by the bulk upload action and anything else that needs it, so the
     * ingest settings live in one place rather than being restated per caller.
     */
    public static function ingest(UploadedFile $file): Media
    {
        $original = $file->getClientOriginalName();

        // Bucketed by month, the way WordPress buckets uploads by year and
        // month, so the library does not become one directory with tens of
        // thousands of files in it. Flattened into a single segment because the
        // media route keeps its path to one directory and one filename, and
        // widening that to accept slashes is how a traversal gets in.
        $stored = app(ImageIngest::class)->store(
            $file,
            'library-'.now()->format('Y-m'),
            name: $original,
        );

        return Media::create([
            'path' => $stored['path'],
            'original_name' => $original,
            'width' => $stored['width'],
            'height' => $stored['height'],
            'bytes' => Storage::disk('media')->size($stored['sizes'][array_key_last($stored['sizes'])]),
        ]);
    }

    /** The upload field, configured once for whoever presents it. */
    public static function uploadField(): FileUpload
    {
        return FileUpload::make('files')
            ->label('Images')
            ->multiple()
            ->image()
            ->required()
            ->maxFiles(30)
            ->maxSize(10240)
            ->reorderable(false)
            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
            // Kept as uploads rather than written to a disk, so ImageIngest
            // sees the original file and decides where it lands.
            ->storeFiles(false)
            ->helperText('Several at once is fine. Each becomes its own entry, named after its file, and you describe them afterwards.');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMedia::route('/'),
        ];
    }
}
