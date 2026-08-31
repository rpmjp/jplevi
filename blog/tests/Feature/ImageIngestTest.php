<?php

namespace Tests\Feature;

use App\Services\ImageIngest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageIngestTest extends TestCase
{
    public function test_it_rejects_a_disguised_file(): void
    {
        Storage::fake('media');

        // A PHP script wearing a .jpg extension: exactly the upload that gets
        // people owned when the extension is trusted.
        $path = storage_path('app/payload.jpg');
        // Any non-image proves the gate. The fixture is deliberately benign:
        // what matters is that the extension says jpeg and the bytes do not.
        file_put_contents($path, "GIF-ish header? no. Just text pretending to be a photo.");

        $bad = new UploadedFile($path, 'payload.jpg', 'image/jpeg', null, true);

        try {
            $this->expectException(\RuntimeException::class);
            app(ImageIngest::class)->store($bad);
        } finally {
            @unlink($path);
        }
    }

    public function test_it_re_encodes_a_real_image_to_webp(): void
    {
        Storage::fake('media');

        $result = app(ImageIngest::class)->store(
            UploadedFile::fake()->image('photo.jpg', 1800, 1200),
        );

        $this->assertNotEmpty($result['sizes']);

        foreach ($result['sizes'] as $path) {
            Storage::disk('media')->assertExists($path);
            $this->assertStringEndsWith('.webp', $path);
        }

        $this->assertSame(1800, $result['width']);
    }
}
