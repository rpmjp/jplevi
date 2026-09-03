<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\ImageManager;

/**
 * Puts a photograph on a person's account.
 *
 * Shared by the seeder and the console command so the framing decision is made
 * once. A portrait made for a page, where it is printed large, is the wrong
 * crop for a byline: dropped whole into a circle forty pixels across, the face
 * is a smudge in the middle of a suit. This finds the subject and crops to the
 * head before anything is resized.
 */
class AvatarImport
{
    public function __construct(private ImageManager $images, private ImageIngest $ingest) {}

    /** @return string The stored path, as written to the user. */
    public function forUser(User $user, string $file): string
    {
        if (! is_file($file)) {
            throw new \RuntimeException("No such file: {$file}");
        }

        $copy = tempnam(sys_get_temp_dir(), 'portrait').'.png';

        try {
            // trim() removes the uniform border, which on a cutout is exactly
            // the transparent margin, leaving the subject filling the frame. On
            // an ordinary photograph it does nothing, which is the right answer.
            $image = $this->images->decodePath($file)->trim();

            // A conventional headshot: a square a little under two thirds the
            // width of the subject, centred on them, taken from the crown down.
            $side = (int) round($image->width() * 0.58);
            $x = max(0, (int) round(($image->width() - $side) / 2));

            $image->crop($side, min($side, $image->height()), $x, 0)
                ->encode(new PngEncoder())
                ->save($copy);

            $user->avatar_path = $this->ingest->store(
                new UploadedFile($copy, 'portrait.png', 'image/png', test: true),
                'avatars',
                ImageIngest::AVATAR_WIDTHS,
                social: false,
                square: true,
            )['path'];

            $user->save();

            return $user->avatar_path;
        } finally {
            @unlink($copy);
        }
    }
}
