<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanTempStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean-temp-storage
        {storage : Specify storage disk to clean (public or private)}
        {--hours= : Only delete files older than this many hours}
        {--force : Skip confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes all temp files, or only those older than the specified number of hours';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $storage = strtolower($this->argument('storage'));
        $hours = $this->option('hours');
        $force = $this->option('force');
        $excludedFiles = ['.gitignore'];

        if (! in_array($storage, ['public', 'private'])) {
            $this->error('Invalid storage disk. Allowed values: public, private.');

            return self::FAILURE;
        }

        if ($hours !== null && (! is_numeric($hours) || (int) $hours <= 0)) {
            $this->error('Invalid value for --hours. It must be a positive number.');

            return self::FAILURE;
        }

        $diskName = $storage === 'private' ? 'local' : 'public';

        $cutoffTime = $hours ? Carbon::now()->subHours((int) $hours)->timestamp : null;

        // Confirmation message
        $this->info("You are about to delete temporary files from the '{$storage}' storage disk.");

        if ($hours) {
            $this->info("Only files older than {$hours} hours will be deleted.");
        } else {
            $this->info("All files inside the 'temp' folder will be deleted.");
        }

        $this->info('This action cannot be undone.');

        if (! $force) {
            if (! $this->confirm('Do you wish to continue? Type "yes" to confirm.', false)) {
                $this->info('Operation cancelled.');

                return self::SUCCESS;
            }
        }

        $this->info("Using {$storage} storage...");
        $this->info(
            $hours
            ? "Deleting temp files older than {$hours} hours..."
            : "Deleting all files inside 'temp' folder..."
        );

        $deletedCount = 0;
        $totalSize = 0;

        $deletedCount = $this->cleanupDirectory('temp', $cutoffTime, $totalSize, $diskName, $excludedFiles);

        $this->info('Cleanup completed!');
        $this->info("Files deleted: {$deletedCount}");
        $this->info('Space freed: '.$this->formatBytes($totalSize));

        return self::SUCCESS;
    }

    private function cleanupDirectory(string $directory, ?int $cutoffTime, int &$totalSize, string $diskName, $excludedFiles): int
    {
        $disk = Storage::disk($diskName);

        if (! $disk->exists($directory)) {
            $this->warn("Directory '{$directory}' does not exist on the {$diskName} disk, skipping...");

            return 0;
        }

        $files = $disk->allFiles($directory);
        $deletedCount = 0;

        foreach ($files as $file) {
            if (in_array(basename($file), $excludedFiles)) {
                continue;
            }
            $lastModified = $disk->lastModified($file);

            if ($cutoffTime === null || $lastModified < $cutoffTime) {
                $fileSize = $disk->size($file);
                $disk->delete($file);
                $deletedCount++;
                $totalSize += $fileSize;

                // $this->line("Deleted: {$file}");
            }
        }

        return $deletedCount;
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, 2).' '.$units[$pow];
    }
}
