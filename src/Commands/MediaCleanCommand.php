<?php

namespace Kiwilan\Steward\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Kiwilan\Steward\Services\Class\ClassItem;
use Kiwilan\Steward\Services\ClassService;

class MediaCleanCommand extends CommandSteward
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:clean
                            {--A|all : Skip limit with Mediable trait.}
                            {--F|force : Force delete without confirmation.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean all medias, attached to Models with Mediable trait, without database link, useful after delete media from back-office.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->title();

        $files = ClassService::files(app_path('Models'));
        $items = ClassService::make($files);

        $all = $this->option('all') ?? false;
        $force = $this->option('force') ?? false;

        if (! $all) {
            $items = $items->filter(fn (ClassItem $item) => $item->useTrait('Kiwilan\Steward\Traits\Mediable'));
        }
        $mediaPath = public_path('storage');

        $mediaEntries = [];

        foreach ($items as $item) {
            $table = $item->model()->getTable();

            /** Parse all entries in database */
            $rows = DB::table($table)
                ->select('*')
                ->get()
            ;

            // Extract all entries with media
            foreach ($rows as $row) {
                foreach ($row as $entry) {
                    foreach (\Kiwilan\Steward\StewardConfig::mediableExtensions() as $extension) {
                        if (str_contains($entry, ".{$extension}")) {
                            $mediaEntries[] = $entry;
                        }
                    }
                }
            }
        }

        /** Get all medias from $media_path */
        $filesList = File::allFiles($mediaPath);
        $files = [];

        foreach ($filesList as $file) {
            $filePath = $file->getRelativePathname();
            $file_path = str_replace('\\', '/', $filePath);
            $files[] = $file_path;
        }

        $mediaUsed = [];
        $mediaAll = [];
        // Find medias between used and all
        foreach ($files as $file) {
            foreach ($mediaEntries as $media_entry) {
                $path = "{$mediaPath}/{$file}";

                if (str_contains($media_entry, $file)) {
                    $mediaUsed[] = $path;
                } else {
                    $mediaAll[] = $path;
                }
            }
        }
        $mediaAll = array_unique($mediaAll);
        $mediaAll = array_values($mediaAll);

        $toDelete = [];

        // Delete medias which is not used
        foreach ($mediaAll as $value) {
            if (! in_array($value, $mediaUsed)) {
                $this->warn("Media {$value} will be deleted.");
                $toDelete[] = $value;
            }
        }

        if (! empty($toDelete)) {
            if (! $force && $this->confirm('Do you wish to continue?', true)) {
                foreach ($toDelete as $value) {
                    File::delete($value);
                }
            } else {
                foreach ($toDelete as $value) {
                    File::delete($value);
                }
            }
        } else {
            $this->info('No media to delete.');
        }

        return Command::SUCCESS;
    }
}
