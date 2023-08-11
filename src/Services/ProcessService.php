<?php

namespace Kiwilan\Steward\Services;

use Closure;
use Kiwilan\HttpPool\Utils\PrintConsole;

class ProcessService
{
    public static function executionTime(Closure $closure, bool $printing = true): void
    {
        $startTime = microtime(true);

        $closure();

        $endTime = microtime(true);

        $executionTime = ($endTime - $startTime);
        $executionTime = number_format((float) $executionTime, 2, '.', '');

        $console = PrintConsole::make();
        $console->print("Execution time of script = {$executionTime} sec", 'green');
    }

    public static function memoryPeekFile(Closure $closure, string $path = null, int $limit = 200): void
    {
        $currentMemory = ini_get('memory_limit');
        $filesize = filesize($path);
        $filesizeMB = $filesize / 1048576;
        $limit = intval($filesizeMB + $limit);

        if (intval($limit) > intval($currentMemory)) {
            ini_set('memory_limit', $limit.'M');
        }

        $closure();

        ini_restore('memory_limit');
    }

    public static function memoryPeek(Closure $closure, int $maxMemory = 10, string $unit = 'G'): void
    {
        ini_set('memory_limit', "{$maxMemory}{$unit}");

        $closure();

        ini_restore('memory_limit');
    }
}
