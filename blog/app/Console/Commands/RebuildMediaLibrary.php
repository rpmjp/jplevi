<?php

namespace App\Console\Commands;

use App\Filament\Admin\Resources\Media\MediaResource;
use App\Models\Media;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Brings images uploaded before the library moved onto the new pipeline.
 *
 * The old upload wrote the file straight to the default disk, which on this
 * install is the private one, under a name that was only an identifier. Those
 * images have no public URL at all and no smaller renditions, so a link to one
 * cannot be shared and a page showing one sends the full file to a phone.
 *
 * This re-ingests each of them: re-encoded to WebP at four widths, filed under
 * the month, and named after whatever description it already carries, so the
 * filename says something. The original is left where it is rather than
 * deleted, so a run that goes wrong costs nothing.
 *
 *   php artisan media:rebuild --dry-run
 *   php artisan media:rebuild
 */
class RebuildMediaLibrary extends Command
{
    protected $signature = 'media:rebuild {--dry-run : List what would change and stop}';

    protected $description = 'Move pre-existing library images onto the responsive pipeline';

    public function handle(): int
    {
        // A path carrying an extension names one file on the old disk. A path
        // without one is already a basename on the new pipeline.
        $legacy = Media::all()->filter(fn (Media $m) => (bool) pathinfo((string) $m->path, PATHINFO_EXTENSION));

        if ($legacy->isEmpty()) {
            $this->info('Nothing to do: every image is already on the current pipeline.');

            return self::SUCCESS;
        }

        $this->line("Found {$legacy->count()} image(s) from the old library.");

        $moved = 0;

        foreach ($legacy as $media) {
            $source = $this->locate($media->path);

            if (! $source) {
                $this->warn("  skipped  {$media->path} — file not found on any disk");

                continue;
            }

            // The description doubles as the filename, which is the whole point
            // of keeping one: a file called offense-frequency.webp says
            // something a ULID never will.
            $name = Str::slug((string) ($media->alt ?: $media->title)) ?: 'image';
            $extension = pathinfo($media->path, PATHINFO_EXTENSION);

            if ($this->option('dry-run')) {
                $this->line("  would move  {$media->path}  ->  library-".now()->format('Y-m')."/{$name}-…");
                $moved++;

                continue;
            }

            try {
                // Captured before the update, so the log says where the image
                // came from rather than echoing back where it has just gone.
                $from = $media->path;

                $copy = tempnam(sys_get_temp_dir(), 'media').'.'.$extension;
                file_put_contents($copy, file_get_contents($source));

                $fresh = MediaResource::ingest(
                    new UploadedFile($copy, "{$name}.{$extension}", null, test: true),
                );

                // Carry the words over, then drop the row the ingest created:
                // the original row is what any existing reference points at.
                $media->update([
                    'path' => $fresh->path,
                    'original_name' => $fresh->original_name,
                    'width' => $fresh->width,
                    'height' => $fresh->height,
                    'bytes' => $fresh->bytes,
                    'title' => $media->title ?: $fresh->title,
                ]);

                $fresh->delete();
                @unlink($copy);

                $this->info("  moved  {$from}  ->  {$media->path}");
                $moved++;
            } catch (\Throwable $e) {
                $this->error("  failed  {$media->path} — {$e->getMessage()}");
            }
        }

        $this->newLine();
        $this->info($this->option('dry-run') ? "{$moved} would be moved." : "{$moved} moved.");
        $this->comment('The original files were left in place. Delete them once the library looks right.');

        return self::SUCCESS;
    }

    /** The old upload could have landed on any of these, depending on config. */
    private function locate(string $path): ?string
    {
        foreach (['local', 'public'] as $disk) {
            if (Storage::disk($disk)->exists($path)) {
                return Storage::disk($disk)->path($path);
            }
        }

        return null;
    }
}
