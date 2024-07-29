<?php

namespace Kiwilan\Steward\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Kiwilan\Steward\Utils\Log\LogClear;

class LogClearJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public bool $all = false,
        public ?string $logName = null,
        public ?string $level = null,
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $logClear = LogClear::make();

        if ($this->all) {
            $logClear->clearAll();
        }

        if ($this->logName) {
            $logClear->clearLog($this->logName);
        }

        if ($this->level) {
            $logClear->deleteLevels($this->level);
        }
    }
}
