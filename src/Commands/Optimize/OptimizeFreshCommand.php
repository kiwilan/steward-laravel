<?php

namespace Kiwilan\Steward\Commands\Optimize;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Kiwilan\Steward\Commands\Commandable;

class OptimizeFreshCommand extends Commandable
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'optimize:fresh';

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
        $this->call('config:cache');
        $this->call('config:clear');
        $this->call('cache:clear');
        $this->call('view:clear');

        // sudo chown -R $USER:www-data .
        // sudo chgrp -R www-data storage bootstrap/cache
        // sudo chmod -R ug+rwx storage bootstrap/cache
        // git checkout .

        $this->info('Optimize app...');
        $this->call('optimize:clear');
        $this->call('optimize');
        $this->call('event:cache');

        $this->info('Done.');

        return Command::SUCCESS;
    }
}
