<?php

namespace Tests\Feature;

use App\Filament\Admin\Resources\Media\MediaResource;
use App\Models\Media;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Tests\TestCase;

class MediaLibraryTest extends TestCase
{
    use RefreshDatabase;

    private function upload(string $name = 'Crime Heatmap DayHour.png', int $w = 1800, int $h = 1200): Media
    {
        return MediaResource::ingest(
            TemporaryUploadedFile::fake()->image($name, $w, $h),
        );
    }

    public function test_an_upload_keeps_a_readable_filename_and_lands_in_a_dated_folder(): void
    {
        Storage::fake('media');

        $media = $this->upload();

        // The filename is a ranking signal in its own right, so throwing it
        // away for an identifier gives one up for nothing.
        $this->assertStringContainsString('crime-heatmap-dayhour', $media->path);
        // And a suffix, so two files of the same name cannot collide.
        $this->assertMatchesRegularExpression('/crime-heatmap-dayhour-[0-9A-Z]{26}$/', $media->path);
        // Bucketed by month, the way WordPress buckets by year and month.
        $this->assertStringStartsWith('library-'.now()->format('Y-m').'/', $media->path);
    }

    public function test_the_direct_link_resolves_and_is_public(): void
    {
        Storage::fake('media');

        $media = $this->upload();

        // A crawler, or anyone the link is pasted to, arrives with no session.
        $this->get($media->url())->assertOk()->assertHeader('Content-Type', 'image/webp');
    }

    public function test_the_image_tag_carries_its_widths_and_description(): void
    {
        Storage::fake('media');

        $media = $this->upload();
        $media->update(['alt' => 'A heatmap of offences by day and hour.', 'caption' => 'Two years of reports.']);

        $tag = $media->fresh()->embedCode();

        $this->assertStringContainsString('srcset=', $tag);
        $this->assertStringContainsString('sizes=', $tag);
        $this->assertStringContainsString('alt="A heatmap of offences by day and hour."', $tag);
        $this->assertStringContainsString('width="1800" height="1200"', $tag);
        $this->assertStringContainsString('loading="lazy"', $tag);
        // A caption makes it a figure, so the words sit with the picture.
        $this->assertStringContainsString('<figcaption>Two years of reports.</figcaption>', $tag);
    }

    public function test_the_filename_becomes_a_title_so_nothing_is_nameless(): void
    {
        Storage::fake('media');

        $this->assertSame('Crime Heatmap Dayhour', $this->upload()->title);
    }

    public function test_images_still_missing_a_description_can_be_found(): void
    {
        Storage::fake('media');

        $described = $this->upload('described.png');
        $described->update(['alt' => 'Described.']);

        $this->upload('undescribed.png');
        $this->upload('also-undescribed.png');

        $this->assertSame(2, Media::undescribed()->count());

        // Grouped, so the filter can be combined with anything else without
        // the or quietly widening the result back out.
        $this->assertSame(1, Media::undescribed()->where('original_name', 'undescribed.png')->count());
    }

    public function test_the_library_is_reachable_by_staff(): void
    {
        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create();
        $admin->syncRoles(['admin']);

        $this->actingAs($admin)
            ->get(route('filament.admin.resources.media.index'))
            ->assertOk();
    }
}
