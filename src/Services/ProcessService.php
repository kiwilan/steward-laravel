<?php

namespace Kiwilan\Steward\Services;

use Closure;
use Kiwilan\Steward\Utils\Console;

class ProcessService
{
    public static function executionTime(Closure $closure): void
    {
        $startTime = microtime(true);

        $closure();

        $endTime = microtime(true);

        $executionTime = ($endTime - $startTime);
        $executionTime = number_format((float) $executionTime, 2, '.', '');

        $console = Console::make();
        $console->print("Execution time of script = {$executionTime} sec", 'green');
    }
}
