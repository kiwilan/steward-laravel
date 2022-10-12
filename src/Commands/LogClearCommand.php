<?php

namespace Kiwilan\Steward\Commands;

use Illuminate\Console\Command;

class LogClearCommand extends CommandSteward
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'log:clear {name? : name of log file}
                            {--a|all : clear all log files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear specific log';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
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

        $all = $this->option('all') ?? false;
        $log_name = $this->argument('name');

        if ($log_name) {
            shell_exec("truncate -s 0 ./storage/logs/{$log_name}.log");
        } elseif ($all) {
            shell_exec('truncate -s 0 ./storage/logs/*.log');
        } else {
            $this->error('You must specify a log name or use --all option');
        }

        return Command::SUCCESS;
    }
}
