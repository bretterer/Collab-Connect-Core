<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MigrateMediaToS3 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'collabconnect:migrate-media
                            {--from=public : Source disk to migrate from}
                            {--to=linode : Destination disk to migrate to}
                            {--delete-after : Delete source files after successful migration}
                            {--dry-run : Show what would be migrated without making changes}
                            {--force : Skip confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate media files from one storage disk to another (e.g., local to S3)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $fromDisk = $this->option('from');
        $toDisk = $this->option('to');
        $deleteAfter = $this->option('delete-after');
        $dryRun = $this->option('dry-run');

        // Validate disks exist
        if (! config("filesystems.disks.{$fromDisk}")) {
            $this->error("Source disk '{$fromDisk}' is not configured.");

            return Command::FAILURE;
        }

        if (! config("filesystems.disks.{$toDisk}")) {
            $this->error("Destination disk '{$toDisk}' is not configured.");

            return Command::FAILURE;
        }

        // Get media items on the source disk
        $mediaItems = Media::where('disk', $fromDisk)->get();

        if ($mediaItems->isEmpty()) {
            $this->info("No media found on disk '{$fromDisk}'. Nothing to migrate.");

            return Command::SUCCESS;
        }

        $this->info("Found {$mediaItems->count()} media items on '{$fromDisk}' disk.");

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made.');
            $this->newLine();
            $this->table(
                ['ID', 'Model', 'Collection', 'File Name', 'Size'],
                $mediaItems->map(fn (Media $media) => [
                    $media->id,
                    class_basename($media->model_type),
                    $media->collection_name,
                    $media->file_name,
                    $this->formatBytes($media->size),
                ])->toArray()
            );

            return Command::SUCCESS;
        }

        if (! $this->option('force')) {
            $this->warn("This will migrate {$mediaItems->count()} media items from '{$fromDisk}' to '{$toDisk}'.");
            if ($deleteAfter) {
                $this->warn('Source files will be DELETED after successful migration.');
            }

            if (! $this->confirm('Do you want to continue?')) {
                $this->info('Migration cancelled.');

                return Command::SUCCESS;
            }
        }

        $sourceStorage = Storage::disk($fromDisk);
        $destStorage = Storage::disk($toDisk);

        $successCount = 0;
        $failCount = 0;
        $skippedCount = 0;

        $this->withProgressBar($mediaItems, function (Media $media) use (
            $sourceStorage,
            $destStorage,
            $toDisk,
            $deleteAfter,
            &$successCount,
            &$failCount,
            &$skippedCount
        ) {
            try {
                $sourcePath = $media->getPath();
                $relativePath = $media->getPathRelativeToRoot();

                // Check if source file exists
                if (! $sourceStorage->exists($relativePath)) {
                    $this->newLine();
                    $this->warn("  Skipping media ID {$media->id}: Source file not found at {$relativePath}");
                    $skippedCount++;

                    return;
                }

                // Check if already migrated (file exists on destination)
                if ($destStorage->exists($relativePath)) {
                    $this->newLine();
                    $this->line("  Skipping media ID {$media->id}: Already exists on destination");
                    $skippedCount++;

                    return;
                }

                // Copy the original file
                $fileContents = $sourceStorage->get($relativePath);
                $destStorage->put($relativePath, $fileContents, 'public');

                // Copy all conversions
                $conversionsPath = pathinfo($relativePath, PATHINFO_DIRNAME).'/conversions';
                if ($sourceStorage->exists($conversionsPath)) {
                    $conversionFiles = $sourceStorage->files($conversionsPath);
                    foreach ($conversionFiles as $conversionFile) {
                        $conversionContents = $sourceStorage->get($conversionFile);
                        $destStorage->put($conversionFile, $conversionContents, 'public');
                    }
                }

                // Copy responsive images if they exist
                $responsivePath = pathinfo($relativePath, PATHINFO_DIRNAME).'/responsive-images';
                if ($sourceStorage->exists($responsivePath)) {
                    $responsiveFiles = $sourceStorage->files($responsivePath);
                    foreach ($responsiveFiles as $responsiveFile) {
                        $responsiveContents = $sourceStorage->get($responsiveFile);
                        $destStorage->put($responsiveFile, $responsiveContents, 'public');
                    }
                }

                // Update the media record
                $media->disk = $toDisk;
                $media->conversions_disk = $toDisk;
                $media->save();

                // Delete source files if requested
                if ($deleteAfter) {
                    $sourceStorage->delete($relativePath);
                    if ($sourceStorage->exists($conversionsPath)) {
                        $sourceStorage->deleteDirectory($conversionsPath);
                    }
                    if ($sourceStorage->exists($responsivePath)) {
                        $sourceStorage->deleteDirectory($responsivePath);
                    }
                    // Try to clean up empty parent directory
                    $parentDir = pathinfo($relativePath, PATHINFO_DIRNAME);
                    $remainingFiles = $sourceStorage->allFiles($parentDir);
                    if (empty($remainingFiles)) {
                        $sourceStorage->deleteDirectory($parentDir);
                    }
                }

                $successCount++;
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("  Failed to migrate media ID {$media->id}: {$e->getMessage()}");
                $failCount++;
            }
        });

        $this->newLine(2);
        $this->info('Migration complete!');
        $this->table(
            ['Status', 'Count'],
            [
                ['Successful', $successCount],
                ['Failed', $failCount],
                ['Skipped', $skippedCount],
            ]
        );

        if ($failCount > 0) {
            $this->warn('Some files failed to migrate. Please check the errors above.');

            return Command::FAILURE;
        }

        if ($successCount > 0 && ! $deleteAfter) {
            $this->newLine();
            $this->info('Tip: Run with --delete-after to remove source files after migration.');
        }

        return Command::SUCCESS;
    }

    /**
     * Format bytes into human readable format.
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision).' '.$units[$pow];
    }
}
