<?php

namespace Kiwilan\Steward\Commands\Log;

use Illuminate\Console\Command;
use Kiwilan\Steward\Commands\Commandable;

class LogClearCommand extends Commandable
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'log:clear
                            {--a|all : clear all log files}
                            {--log= : clear specific log file}
                            {--l|level= : clear level log like `DEBUG`, `INFO`, `NOTICE`, `WARNING`, `ERROR`, `CRITICAL`, `ALERT`, `EMERGENCY`}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear logs.';

    /**
     * Create a new command instance.
     */
    public function __construct(
        public ?string $path = null,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->title();
        $this->path = storage_path('logs/laravel.log');

        $all = $this->option('all') ?: false;
        $log_name = $this->optionArgument('log') ?? null;
        $level = $this->optionArgument('level') ?? null;

        if (! $log_name && ! $all && ! $level) {
            $all = true;
        }

        if ($all) {
            $this->info('Clearing all logs...');
            $this->clearAll();
        }

        if ($log_name) {
            $this->info("Clearing {$log_name} log...");
            $this->clearLog($log_name);
        }

        if ($level) {
            $this->info("Clearing level {$level} in logs...");
            $this->deleteLevels($level);
        }

        return Command::SUCCESS;
    }

    private function clearLog(string $log_name): void
    {
        shell_exec("truncate -s 0 ./storage/logs/{$log_name}.log");
    }

    private function clearAll(): void
    {
        shell_exec('truncate -s 0 ./storage/logs/*.log');
    }

    private function deleteLevels(string $level): void
    {
        $allLogFiles = glob(storage_path('logs/*.log'));

        foreach ($allLogFiles as $logFile) {
            $this->info("Clearing {$logFile} for level {$level} log...");
            $this->deleteLevel($logFile, $level);
        }
    }

    private function deleteLevel(string $path, string $level): void
    {
        $tempFile = storage_path('logs/temp.log');

        $handle = fopen($path, 'r');
        $tempHandle = fopen($tempFile, 'w');

        if ($handle && $tempHandle) {
            while (($line = fgets($handle)) !== false) {
                if (strpos($line, "local.$level") === false) {
                    fwrite($tempHandle, $line);
                }
            }

            fclose($handle);
            fclose($tempHandle);

            // Replace original file with temp file
            rename($tempFile, $path);
        } else {
            // Error handling
            echo 'Error opening file!';
        }
    }
}
