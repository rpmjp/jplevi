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
     * @param  bool  $square  Crop to a square rather than preserving the shape.
     *                        An avatar is painted in a circle, so a portrait
     *                        that is not square arrives with its sides sliced
     *                        off by the mask instead of by us.
     * @param  bool  $social  Whether to cut the 1200x630 link preview. Wanted
     *                        for anything that gets shared, pointless for a
     *                        face that only ever appears inside the page.
     * @return array{basename:string, path:string, width:int, height:int, sizes:array<int,string>}
     */
    public function store(UploadedFile $file, string $directory = 'posts', ?array $widths = null, bool $social = true, bool $square = false): array
    {
        $widths ??= self::WIDTHS;

        // Read the magic bytes rather than asking the upload what it is.
        // getMimeType() can be led by the extension; finfo looks at content.
        $head = (string) file_get_contents($file->getRealPath(), length: 4096);
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($head) ?: 'unknown';

        if (! in_array($mime, self::ALLOWED, true)) {
            throw new \RuntimeException("Unsupported image type: {$mime}");
        }

        try {
            $image = $this->images->decodePath($file->getRealPath());
        } catch (\Throwable $e) {
            // Passed the type check but will not decode: treat as hostile.
            throw new \RuntimeException('Could not decode the image.', previous: $e);
        }
        $basename = Str::ulid()->toString();
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
}
