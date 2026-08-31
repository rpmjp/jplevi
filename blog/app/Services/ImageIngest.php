<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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

    /** Widths generated for responsive delivery. */
    private const WIDTHS = [480, 960, 1600];

    public function __construct(private ImageManager $images) {}

    /**
     * @return array{basename:string, width:int, height:int, sizes:array<int,string>}
     */
    public function store(UploadedFile $file, string $directory = 'posts'): array
    {
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

        foreach (self::WIDTHS as $width) {
            if ($image->width() < $width && $width !== self::WIDTHS[0]) {
                continue;
            }

            $resized = (clone $image)->scaleDown(width: $width);
            $path = "{$directory}/{$basename}-{$width}.webp";

            Storage::disk('media')->put($path, (string) $resized->encode(new WebpEncoder(quality: 82)));
            $sizes[$width] = $path;
        }

        return [
            'basename' => $basename,
            'width' => $image->width(),
            'height' => $image->height(),
            'sizes' => $sizes,
        ];
    }
}
