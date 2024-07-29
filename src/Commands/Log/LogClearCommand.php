<?php

namespace Kiwilan\Steward\Commands\Log;

use Illuminate\Console\Command;
use Kiwilan\Steward\Commands\Commandable;
use Kiwilan\Steward\Jobs\LogClearJob;
use Kiwilan\Steward\Utils\Log\LogClear;

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
                            {--l|level= : clear level log like `DEBUG`, `INFO`, `NOTICE`, `WARNING`, `ERROR`, `CRITICAL`, `ALERT`, `EMERGENCY`}
                            {--j|job : execute as job}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear logs.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->title();
        $logClear = LogClear::make();

        $all = $this->optionBool('all', false);
        $logName = $this->optionArgument('log') ?? null;
        $level = $this->optionArgument('level') ?? null;
        $job = $this->optionBool('job', false);

        if (! $logName && ! $all && ! $level) {
            $all = true;
        }

        if ($job) {
            $this->info('Clearing logs as job...');
            LogClearJob::dispatch($all, $logName, $level);
        } else {
            if ($all) {
                $this->info('Clearing all logs...');
                $logClear->clearAll();
            }

            if ($logName) {
                $this->info("Clearing {$logName} log...");
                $logClear->clearLog($logName);
            }

            if ($level) {
                $this->info("Clearing level {$level} in logs...");
                $logClear->deleteLevels($level);
            }
        }

        return Command::SUCCESS;
    }
}
