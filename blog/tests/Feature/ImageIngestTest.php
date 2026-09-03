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

    public function test_an_empty_file_is_refused_with_a_reason_a_person_can_act_on(): void
    {
        Storage::fake('media');

        // A browser hands over zero bytes when a picture is dragged out of
        // another web page, or when it lives in cloud storage and has not been
        // downloaded yet. The upload just fails, and nothing says why.
        $path = storage_path('app/empty.png');
        file_put_contents($path, '');

        try {
            $this->expectException(\RuntimeException::class);
            $this->expectExceptionMessageMatches('/arrived empty/');
            app(ImageIngest::class)->store(new UploadedFile($path, 'empty.png', 'image/png', null, true));
        } finally {
            @unlink($path);
        }
    }

    public function test_an_image_too_large_to_decode_says_so_before_trying(): void
    {
        Storage::fake('media');

        $file = UploadedFile::fake()->image('big.jpg', 4000, 3000);
        $original = ini_get('memory_limit');

        // The guard compares what the image needs against what is left. Rather
        // than allocating gigabytes to prove it fires, the ceiling is lowered
        // to just above what the test process is already using, leaving less
        // headroom than a 12 megapixel image needs. The arithmetic under test
        // is the same either way, and PHP refuses a limit below current usage,
        // so it is derived rather than hardcoded.
        ini_set('memory_limit', (int) ceil(memory_get_usage(true) / 1048576) + 8 .'M');

        try {
            app(ImageIngest::class)->store($file);
            $this->fail('An image beyond the memory ceiling should have been refused.');
        } catch (\RuntimeException $e) {
            // Dimensions, and a size that would work. A refusal that does not
            // say what would have worked leaves the author guessing.
            $this->assertStringContainsString('4,000×3,000', $e->getMessage());
            $this->assertStringContainsString('too large to process', $e->getMessage());
            $this->assertMatchesRegularExpression('/up to about [\d,]+×[\d,]+/', $e->getMessage());
        } finally {
            ini_set('memory_limit', $original);
        }
    }

    public function test_an_image_within_the_ceiling_is_processed_normally(): void
    {
        Storage::fake('media');

        $result = app(ImageIngest::class)->store(UploadedFile::fake()->image('fine.jpg', 1800, 1200));

        $this->assertNotEmpty($result['sizes']);
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
