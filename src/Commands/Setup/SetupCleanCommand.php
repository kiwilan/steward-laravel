<?php

namespace Kiwilan\Steward\Commands\Setup;

use Illuminate\Console\Command;
use Kiwilan\Steward\Commands\CommandSteward;

class SetupCleanCommand extends CommandSteward
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean a repository to remove logs, reload .env and clear cache.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->title();

        $this->info('Removing logs...');
        $this->call('logs:clear');

        $this->info('Reloading .env...');
        $this->call('config:clear');

        $this->info('Clearing cache...');
        $this->call('cache:clear');

        // rm bootstrap/cache/config.php
        // /usr/bin/php8.1 artisan config:cache
        // /usr/bin/php8.1 artisan config:clear
        // /usr/bin/php8.1 artisan cache:clear

        // sudo chown -R $USER:www-data * ; sudo chmod -R ug+rwx storage bootstrap/cache
        // git checkout .

        $this->info('Done.');

        return Command::SUCCESS;
    }
}
