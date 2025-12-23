<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Influencer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Tests\TestCase;

class MigrateMediaToS3Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Fake both storage disks
        Storage::fake('public');
        Storage::fake('linode');
    }

    #[Test]
    public function it_shows_no_media_message_when_source_disk_is_empty(): void
    {
        $this->artisan('collabconnect:migrate-media', ['--force' => true])
            ->expectsOutput("No media found on disk 'public'. Nothing to migrate.")
            ->assertExitCode(0);
    }

    #[Test]
    public function it_displays_media_in_dry_run_mode_without_migrating(): void
    {
        $user = User::factory()->create();
        $influencer = Influencer::factory()->create(['user_id' => $user->id]);

        // Add media to influencer
        $influencer->addMedia(UploadedFile::fake()->image('profile.jpg', 200, 200))
            ->toMediaCollection('profile_image');

        $this->artisan('collabconnect:migrate-media', ['--dry-run' => true])
            ->expectsOutput("Found 1 media items on 'public' disk.")
            ->expectsOutput('DRY RUN MODE - No changes will be made.')
            ->assertExitCode(0);

        // Verify media was NOT moved
        $this->assertDatabaseHas('media', [
            'model_type' => Influencer::class,
            'model_id' => $influencer->id,
            'disk' => 'public',
        ]);
    }

    #[Test]
    public function it_migrates_media_from_local_to_s3(): void
    {
        $user = User::factory()->create();
        $influencer = Influencer::factory()->create(['user_id' => $user->id]);

        // Add media to influencer
        $influencer->addMedia(UploadedFile::fake()->image('profile.jpg', 200, 200))
            ->toMediaCollection('profile_image');

        $media = $influencer->getFirstMedia('profile_image');
        $originalPath = $media->getPathRelativeToRoot();

        // Ensure file exists on source disk
        Storage::disk('public')->assertExists($originalPath);

        $this->artisan('collabconnect:migrate-media', ['--force' => true])
            ->expectsOutput("Found 1 media items on 'public' disk.")
            ->assertExitCode(0);

        // Verify media record was updated
        $media->refresh();
        $this->assertEquals('linode', $media->disk);
        $this->assertEquals('linode', $media->conversions_disk);

        // Verify file exists on destination disk
        Storage::disk('linode')->assertExists($originalPath);
    }

    #[Test]
    public function it_deletes_source_files_when_delete_after_flag_is_set(): void
    {
        $user = User::factory()->create();
        $influencer = Influencer::factory()->create(['user_id' => $user->id]);

        // Add media to influencer
        $influencer->addMedia(UploadedFile::fake()->image('profile.jpg', 200, 200))
            ->toMediaCollection('profile_image');

        $media = $influencer->getFirstMedia('profile_image');
        $originalPath = $media->getPathRelativeToRoot();

        // Ensure file exists on source disk
        Storage::disk('public')->assertExists($originalPath);

        $this->artisan('collabconnect:migrate-media', ['--force' => true, '--delete-after' => true])
            ->assertExitCode(0);

        // Verify file was deleted from source disk
        Storage::disk('public')->assertMissing($originalPath);

        // Verify file exists on destination disk
        Storage::disk('linode')->assertExists($originalPath);
    }

    #[Test]
    public function it_handles_multiple_media_items(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $influencer = Influencer::factory()->create(['user_id' => $user1->id]);
        $business = Business::factory()->create();
        $business->users()->attach($user2->id, ['role' => 'owner']);

        // Add media to influencer
        $influencer->addMedia(UploadedFile::fake()->image('profile.jpg', 200, 200))
            ->toMediaCollection('profile_image');

        $influencer->addMedia(UploadedFile::fake()->image('banner.jpg', 1200, 400))
            ->toMediaCollection('banner_image');

        // Add media to business
        $business->addMedia(UploadedFile::fake()->image('logo.png', 300, 300))
            ->toMediaCollection('logo');

        $this->artisan('collabconnect:migrate-media', ['--force' => true])
            ->expectsOutput("Found 3 media items on 'public' disk.")
            ->assertExitCode(0);

        // Verify all media records were updated
        $this->assertEquals(3, Media::where('disk', 'linode')->count());
        $this->assertEquals(0, Media::where('disk', 'public')->count());
    }

    #[Test]
    public function it_skips_already_migrated_files(): void
    {
        $user = User::factory()->create();
        $influencer = Influencer::factory()->create(['user_id' => $user->id]);

        // Add media to influencer
        $influencer->addMedia(UploadedFile::fake()->image('profile.jpg', 200, 200))
            ->toMediaCollection('profile_image');

        $media = $influencer->getFirstMedia('profile_image');
        $originalPath = $media->getPathRelativeToRoot();

        // Pre-create the file on destination to simulate already migrated
        Storage::disk('linode')->put($originalPath, 'dummy content');

        $this->artisan('collabconnect:migrate-media', ['--force' => true])
            ->assertExitCode(0);

        // Media record should still be on public since we skipped
        $media->refresh();
        $this->assertEquals('public', $media->disk);
    }

    #[Test]
    public function it_fails_for_invalid_source_disk(): void
    {
        $this->artisan('collabconnect:migrate-media', ['--from' => 'nonexistent', '--force' => true])
            ->expectsOutput("Source disk 'nonexistent' is not configured.")
            ->assertExitCode(1);
    }

    #[Test]
    public function it_fails_for_invalid_destination_disk(): void
    {
        $this->artisan('collabconnect:migrate-media', ['--to' => 'nonexistent', '--force' => true])
            ->expectsOutput("Destination disk 'nonexistent' is not configured.")
            ->assertExitCode(1);
    }

    #[Test]
    public function it_allows_custom_source_and_destination_disks(): void
    {
        // Create custom disks for this test
        config(['filesystems.disks.custom_source' => [
            'driver' => 'local',
            'root' => storage_path('app/custom_source'),
        ]]);

        config(['filesystems.disks.custom_dest' => [
            'driver' => 'local',
            'root' => storage_path('app/custom_dest'),
        ]]);

        Storage::fake('custom_source');
        Storage::fake('custom_dest');

        $this->artisan('collabconnect:migrate-media', [
            '--from' => 'custom_source',
            '--to' => 'custom_dest',
            '--force' => true,
        ])
            ->expectsOutput("No media found on disk 'custom_source'. Nothing to migrate.")
            ->assertExitCode(0);
    }
}
