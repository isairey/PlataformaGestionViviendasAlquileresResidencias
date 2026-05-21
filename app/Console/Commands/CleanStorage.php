<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanStorage extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:clean-storage
        {disk : Storage disk to clean (public or private)}';

    /**
     * The console command description.
     */
    protected $description = 'Completely deletes all files in the specified storage disk. Provide "public" or "private" as disk argument.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $disk = $this->argument('disk');
        $excludedFiles = ['.gitignore'];

        if (! in_array($disk, ['public', 'private'])) {
            $this->error('Invalid disk argument. Allowed values: public, private');

            return self::FAILURE;
        }

        $diskName = $disk === 'private' ? 'local' : 'public';

        $this->info("You are about to delete ALL files and directories from the '{$disk}' storage disk.");
        $this->info('This action cannot be undone.');

        if (! $this->confirm('Do you wish to continue? Type "yes" to confirm.', false)) {
            $this->info('Operation cancelled.');

            return self::SUCCESS;
        }

        $storageDisk = Storage::disk($diskName);

        $files = $storageDisk->allFiles();
        $directories = $storageDisk->allDirectories();

        foreach ($files as $file) {
            if (in_array(basename($file), $excludedFiles)) {
                continue;
            }
            $storageDisk->delete($file);
        }
        foreach ($directories as $dir) {
            $storageDisk->deleteDirectory($dir);
        }

        $this->info("All files and directories from {$disk} storage have been deleted.");

        return self::SUCCESS;
    }
}
