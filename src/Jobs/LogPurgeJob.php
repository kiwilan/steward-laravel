<?php

namespace Kiwilan\Steward\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LogPurgeJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $size = 20,
    ) {}

    /**
     * Execute the job.
     */
    public function handle()
    {
        $path = storage_path('logs');
        $logs_files = scandir($path);
        $max_size = $this->size * 1000000;

        foreach ($logs_files as $file) {
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            if ($ext !== 'log') {
                continue;
            }

            if (is_file($path.'/'.$file)) {
                $size = filesize($path.'/'.$file);
                if ($size > $max_size) {
                    unlink($path.'/'.$file);
                }
            }
        }
    }
}
