<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;

/**
 * Takes an uploaded image and returns something safe to keep.
 *
 * Two rules do most of the work here. The type is decided by reading the file,
 * never by trusting its name, and every image is re-encoded rather than stored
 * as sent. Re-encoding means a file that is partly a valid JPEG and partly
 * something else comes out the far side as only the image, because the parts
 * that were not pixels do not survive being decoded and written again. That
 * also strips EXIF, which quietly carries GPS coordinates on phone photos.
 */
class ImageIngest
{
    /** Formats we accept, by real MIME type. */
    private const ALLOWED = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

    /**
     * Widths generated for responsive delivery, smallest first.
     *
     * WordPress ships thumbnail/medium/medium_large/large and lets the browser
     * pick with srcset. The same idea, with the widths chosen for where they
     * are actually used: 400 covers the feed thumbnail on a retina screen, 800
     * the phone-width hero, 1200 the desktop hero and the link preview, 1600
     * the same hero on a retina desktop.
     */
    public const WIDTHS = [400, 800, 1200, 1600];

    /**
     * The link preview crop.
     *
     * Facebook, X, LinkedIn, Slack, Discord and iMessage all unfurl at 1.91:1,
     * so one 1200x630 rendition satisfies every one of them. Cropped rather
     * than scaled, because a letterboxed preview reads as a mistake.
     *
     * Written as JPEG, alone among everything here. The site itself is served
     * WebP because every browser reads it, but a link preview is fetched by a
     * scraper rather than a browser, and WebP support across those is still
     * uneven: LinkedIn's in particular is unreliable, and older WhatsApp
     * clients drop it. A scraper that cannot read the image does not fall back
     * to another one, it just posts the link with no picture at all. JPEG is
     * read by all of them, and a few tens of kilobytes is a cheap price for a
     * preview that always renders.
     */
    public const SOCIAL = [1200, 630];

    /** The extension of the preview crop. Deliberately not webp. */
    public const SOCIAL_EXTENSION = 'jpg';

    /**
     * Widths for a portrait.
     *
     * An avatar is never painted larger than about 96 CSS pixels here, so the
     * cover widths would ship a file several times bigger than the box it goes
     * in. 192 covers a retina byline, 400 the largest place a face appears.
     */
    public const AVATAR_WIDTHS = [96, 192, 400];

    public function __construct(private ImageManager $images) {}

    /**
     * @param  array<int,int>  $widths  Renditions to write. Never upscaled.
     * @param  string|null  $name  A descriptive stem for the filename, usually
     *                               the name of the file as uploaded. Search
     *                               engines read filenames, so throwing a
     *                               readable one away and writing only an
     *                               identifier gives up a signal for nothing.
     *                               A collision-proof suffix is added either
     *                               way, so this never has to be unique.
     * @param  bool  $square  Crop to a square rather than preserving the shape.
     *                        An avatar is painted in a circle, so a portrait
     *                        that is not square arrives with its sides sliced
     *                        off by the mask instead of by us.
     * @param  bool  $social  Whether to cut the 1200x630 link preview. Wanted
     *                        for anything that gets shared, pointless for a
     *                        face that only ever appears inside the page.
     * @return array{basename:string, path:string, width:int, height:int, sizes:array<int,string>}
     */
    public function store(UploadedFile $file, string $directory = 'posts', ?array $widths = null, bool $social = true, bool $square = false, ?string $name = null): array
    {
        $widths ??= self::WIDTHS;

        $path = $file->getRealPath();

        // An empty file, reported plainly. A browser hands over zero bytes when
        // the picture was dragged straight out of another web page, or when it
        // lives in cloud storage and has not been downloaded to the machine
        // yet. Neither is obvious from a failed upload, so it is said here.
        if (! $path || filesize($path) === 0) {
            throw new \RuntimeException(
                'That file arrived empty. If you dragged it out of a browser tab or another app, '
                .'save it to the computer first and choose the saved file.'
            );
        }

        // Read the magic bytes rather than asking the upload what it is.
        // getMimeType() can be led by the extension; finfo looks at content.
        $head = (string) file_get_contents($path, length: 4096);
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($head) ?: 'unknown';

        if (! in_array($mime, self::ALLOWED, true)) {
            throw new \RuntimeException("Unsupported image type: {$mime}");
        }

        $this->guardAgainstRunningOutOfMemory($path);

        try {
            $image = $this->images->decodePath($file->getRealPath());
        } catch (\Throwable $e) {
            // Passed the type check but will not decode: treat as hostile.
            throw new \RuntimeException('Could not decode the image.', previous: $e);
        }
        // A readable stem where one was given, then a ULID so two files
        // called chart.png can never land on each other. Trimmed, because a
        // filename is not a place to put a sentence.
        $stem = Str::of((string) $name)
            ->beforeLast('.')
            ->slug()
            ->limit(60, '')
            ->trim('-')
            ->value();

        $basename = ($stem !== '' ? $stem.'-' : '').Str::ulid()->toString();
        $sizes = [];

        foreach ($widths as $width) {
            // Never upscale. A 900px original gets 400 and 800 and stops there,
            // so srcset never offers the browser a blurrier file as a bigger
            // one. The smallest width is always written, so there is something
            // to serve even from a tiny original.
            $source = $square ? min($image->width(), $image->height()) : $image->width();

            if ($source < $width && $width !== $widths[0]) {
                continue;
            }

            $resized = $square
                ? (clone $image)->cover($width, $width)
                : (clone $image)->scaleDown(width: $width);

            $path = "{$directory}/{$basename}-{$width}.webp";

            Storage::disk('media')->put($path, (string) $resized->encode(new WebpEncoder(quality: 82)));
            $sizes[$width] = $path;
        }

        if ($social) {
            // cover() fills the box and trims the overflow, so the preview is
            // always exactly 1.91:1 whatever shape the original was.
            [$w, $h] = self::SOCIAL;

            Storage::disk('media')->put(
                "{$directory}/{$basename}-social.".self::SOCIAL_EXTENSION,
                // Flattened onto white first: JPEG has no alpha, and a cutout
                // encoded straight to it comes out with a black background.
                (string) (clone $image)->cover($w, $h)
                    ->fillTransparentAreas('ffffff')
                    ->encode(new JpegEncoder(quality: 82, progressive: true)),
            );
        }

        return [
            'basename' => $basename,
            // What goes in the database: everything else is derived from it.
            'path' => "{$directory}/{$basename}",
            'width' => $image->width(),
            'height' => $image->height(),
            'sizes' => $sizes,
        ];
    }

    /**
     * Refuses an image that cannot be decoded within the memory available.
     *
     * Dimensions come from the file header, which costs nothing to read, so an
     * image too large to open is turned away before anything tries to open it.
     * Without this the process is killed part way through and the browser is
     * left showing a failed upload with no reason attached, which is the worst
     * possible way to find out.
     *
     * The pixel count is what matters, not the file size. A 20MB photograph
     * decodes to about the same memory as a 2MB one of the same dimensions,
     * because both become four bytes a pixel once open.
     */
    private function guardAgainstRunningOutOfMemory(string $path): void
    {
        $size = @getimagesize($path);

        if (! $size) {
            throw new \RuntimeException('That file could not be read as an image.');
        }

        [$width, $height] = $size;

        // Four bytes a pixel while open, and the resize holds the source and
        // the new copy at the same time. The margin covers the difference
        // between that arithmetic and what the extension actually does.
        $needed = (int) ($width * $height * 4 * 2.4);
        $free = self::memoryLimit() - memory_get_usage(true);

        if ($needed <= $free) {
            return;
        }

        $largest = (int) sqrt(max($free, 1) / (4 * 2.4));

        throw new \RuntimeException(sprintf(
            'That image is %s×%s, which is too large to process on this server. '
            .'Anything up to about %s×%s is fine. Resizing it before uploading will fix it, '
            .'and will cost nothing in quality: the largest copy kept here is %spx wide.',
            number_format($width), number_format($height),
            number_format($largest), number_format($largest),
            number_format(max(self::WIDTHS)),
        ));
    }

    /** The memory ceiling in bytes, with no limit treated as a generous one. */
    private static function memoryLimit(): int
    {
        $limit = trim((string) ini_get('memory_limit'));

        if ($limit === '' || $limit === '-1') {
            return 1024 * 1024 * 1024;
        }

        $unit = strtolower(substr($limit, -1));
        $value = (int) $limit;

        return match ($unit) {
            'g' => $value * 1024 ** 3,
            'm' => $value * 1024 ** 2,
            'k' => $value * 1024,
            default => $value,
        };
    }
}
