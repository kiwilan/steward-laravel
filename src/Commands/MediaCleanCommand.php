<?php

namespace Kiwilan\Steward\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Kiwilan\Steward\Services\Class\ClassItem;
use Kiwilan\Steward\Services\ClassService;
use Kiwilan\Steward\Services\DirectoryService;
use Kiwilan\Steward\StewardConfig;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Console\Exception\RuntimeException;

class MediaCleanCommand extends Commandable
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

    protected bool $all = false;

    protected bool $force = false;

    protected string $mediaPath = '';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->title();

        $this->all = $this->option('all') ?: false;
        $this->force = $this->option('force') ?: false;

        $this->mediaPath = public_path('storage');

        $dbFiles = $this->setDbFiles();
        $localFiles = $this->setLocalFiles();
        $cachePath = storage_path('app/cache');

        DirectoryService::make()->clear($cachePath);

        $chunkMax = StewardConfig::factoryMaxHandle();
        $chunks = array_chunk($localFiles, $chunkMax);
        $filesToDelete = [];

        foreach ($chunks as $chunk) {
            foreach ($chunk as $file) {
                if (! $this->checkIfExistInDb($file, $dbFiles)) {
                    // $this->error('File not exist in database: '.$file);
                    $filesToDelete[] = "{$this->mediaPath}/{$file}";
                } else {
                    // $this->info('File exist in database: '.$file);
                }
            }

            $this->table(['Files to delete'], array_map(fn ($file) => [$file], $filesToDelete));
            $this->deleteMedias($filesToDelete);
        }

        return Command::SUCCESS;
    }

    /**
     * @param  array<string>  $dbFiles
     */
    private function checkIfExistInDb(string $file, array $dbFiles): bool
    {
        foreach ($dbFiles as $dbFile) {
            $json = json_decode($dbFile, true);
            $isArray = is_array($json);

            if ($isArray) {
                foreach ($json as $entry) {
                    $entryIsArray = is_array($entry);

                    if ($entryIsArray) {
                        $medias = $this->parseArrayEntry($entry);

                        foreach ($medias as $media) {
                            if (str_contains($media, $file)) {
                                return true;
                            }
                        }
                    } else {
                        if (str_contains($entry, $file)) {
                            return true;
                        }
                    }
                }
            } else {
                if (str_contains($dbFile, $file)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function setDbFiles(): array
    {
        $files = ClassService::files(app_path('Models'));
        $items = ClassService::make($files);

        if (! $this->all) {
            $items = $items->filter(fn (ClassItem $item) => $item->useTrait('Kiwilan\Steward\Traits\Mediable'));
        }

        $mediaDatabaseEntries = [];

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
                    if ($this->isLocalImage($entry)) {
                        $mediaDatabaseEntries[] = $entry;
                    }
                }
            }
        }

        return $mediaDatabaseEntries;
    }

    private function isUrl(?string $entry): bool
    {
        if (! $entry) {
            return false;
        }

        return filter_var($entry, FILTER_VALIDATE_URL) !== false;
    }

    private function isLocalImage(?string $entry): bool
    {
        if (! $entry) {
            return false;
        }

        if ($this->isUrl($entry)) {
            return false;
        }

        foreach (\Kiwilan\Steward\StewardConfig::mediableExtensions() as $extension) {
            if (str_contains($entry, ".{$extension}")) {
                return true;
            }
        }

        return false;
    }

    private function setLocalFiles(): array
    {
        /** Get all medias from $media_path */
        $filesList = File::allFiles($this->mediaPath);
        $files = [];

        foreach ($filesList as $file) {
            $filePath = $file->getRelativePathname();
            $file_path = str_replace('\\', '/', $filePath);
            $files[] = $file_path;
        }

        return $files;
    }

    private function parseArrayEntry(array $entry): array
    {
        $riter = new RecursiveArrayIterator($entry);
        $riteriter = new RecursiveIteratorIterator($riter, RecursiveIteratorIterator::LEAVES_ONLY);
        $found = [];

        foreach ($riteriter as $key => $value) {
            $key = $riteriter->getSubIterator($riteriter->getDepth() - 2)->key();

            if ($this->isLocalImage($value)) {
                $found[] = "{$this->mediaPath}/{$value}";
            }
        }

        return $found;
    }

    /**
     * Delete medias which is not used
     *
     * @param  array<string>  $mediaToDelete
     *
     * @throws RuntimeException
     */
    private function deleteMedias(array $mediaToDelete): void
    {
        if (! empty($mediaToDelete)) {
            $continue = $this->force;

            if (! $this->force && $this->confirm('Do you wish to continue?', true)) {
                $continue = true;
            }

            if ($continue) {
                foreach ($mediaToDelete as $value) {
                    File::delete($value);
                }
            } else {
                $this->info('Cancel deletion of medias.');
            }
        } else {
            $this->info('No media to delete.');
        }
    }
}
