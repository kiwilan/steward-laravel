<?php

namespace Kiwilan\Steward\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Kiwilan\Steward\Services\Class\ClassItem;
use Kiwilan\Steward\Services\ClassService;
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

    /** @var array<string> */
    protected array $mediaDatabaseEntries = [];

    /** @var array<string> */
    protected array $mediaFiles = [];

    /** @var array<string> */
    protected array $mediaAll = [];

    /** @var array<string> */
    protected array $mediaUsed = [];

    protected array $mediaToDelete = [];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->title();

        $this->all = $this->option('all') ?? false;
        $this->force = $this->option('force') ?? false;

        $this->mediaPath = public_path('storage');

        $this->mediaDatabaseEntries = $this->setMediaDatabaseEntries();
        $this->mediaFiles = $this->setMediaFiles();

        $medias = $this->setMediaAllAndUsed();
        $this->mediaAll = $medias['mediaAll'];
        $this->mediaUsed = $medias['mediaUsed'];

        $this->mediaToDelete = $this->setMediaToDelete();
        $this->deleteMedias();

        return Command::SUCCESS;
    }

    private function setMediaDatabaseEntries(): array
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

    private function setMediaFiles(): array
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

    private function setMediaAllAndUsed(): array
    {
        $mediaAll = [];
        $mediaUsed = [];

        // Find medias between used and all
        foreach ($this->mediaFiles as $file) {
            foreach ($this->mediaDatabaseEntries as $media_entry) {
                $this->handleEntry($media_entry, $file, $mediaUsed, $mediaAll);
            }
        }
        $mediaAll = array_unique($mediaAll);
        $mediaAll = array_values($mediaAll);

        $mediaUsed = array_unique($mediaUsed);
        $mediaUsed = array_values($mediaUsed);

        return [
            'mediaAll' => $mediaAll,
            'mediaUsed' => $mediaUsed,
        ];
    }

    private function handleEntry(string $media_entry, string $file, array &$mediaUsed = [], array &$mediaAll = [])
    {
        $json = json_decode($media_entry, true);
        $isArray = is_array($json);

        $path = "{$this->mediaPath}/{$file}";

        if ($isArray) {
            foreach ($json as $entry) {
                $entryIsArray = is_array($entry);

                if ($entryIsArray) {
                    $medias = $this->parseArrayEntry($entry);

                    foreach ($medias as $media) {
                        if (str_contains($media, $file)) {
                            $mediaUsed[] = $path;
                        } else {
                            $mediaAll[] = $path;
                        }
                    }
                } else {
                    if (str_contains($entry, $file)) {
                        $mediaUsed[] = $path;
                    } else {
                        $mediaAll[] = $path;
                    }
                }
            }
        } else {
            if (str_contains($media_entry, $file)) {
                $mediaUsed[] = $path;
            } else {
                $mediaAll[] = $path;
            }
        }
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
     * Check all medias which is not used.
     */
    private function setMediaToDelete(): array
    {
        $mediasToDelete = [];

        foreach ($this->mediaAll as $value) {
            if (! in_array($value, $this->mediaUsed)) {
                $this->warn("Media {$value} will be deleted.");
                $mediasToDelete[] = $value;
            }
        }

        return $mediasToDelete;
    }

    /**
     * Delete medias which is not used
     *
     * @throws RuntimeException
     */
    private function deleteMedias(): void
    {
        if (! empty($this->mediaToDelete)) {
            $continue = $this->force;

            if (! $this->force && $this->confirm('Do you wish to continue?', true)) {
                $continue = true;
            }

            if ($continue) {
                foreach ($this->mediaToDelete as $value) {
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
