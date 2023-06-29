<?php

namespace Kiwilan\Steward\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ClearFreshCommand extends Commandable
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:fresh';

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
        $this->call('log:clear', [
            '--all' => true,
        ]);

        $this->info('Reloading .env...');
        File::delete(base_path('bootstrap/cache/config.php'));
        $this->call('config:clear');
        $this->call('cache:clear');
        $this->call('view:clear');

        // sudo chown -R $USER:www-data .
        // sudo chgrp -R www-data storage bootstrap/cache
        // sudo chmod -R ug+rwx storage bootstrap/cache
        // git checkout .

        $this->info('Done.');

        return Command::SUCCESS;
    }
}
