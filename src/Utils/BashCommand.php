<?php

namespace Kiwilan\Steward\Utils;

use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class BashCommand
{
    public function __construct(
        public string $command,
        public array $args = [],
    ) {
    }

    public function execute(): string|false
    {
        $process = new Process([$this->command, ...$this->args]);
        $process->run();

        if (! $process->isSuccessful()) {
            $cmd = "{$this->command} ".implode(' ', $this->args);
            Log::warning("BashCommand: error {$cmd}", [$process->getErrorOutput()]);

            return false;
        }

        return $process->getOutput();
    }
}
