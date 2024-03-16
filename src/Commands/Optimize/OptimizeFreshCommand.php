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

        $this->info('Run `log:clear`...');
        $this->call('log:clear', [
            '--all' => true,
        ]);

        $this->info('Run `view:clear`...');
        $this->call('view:clear');

        $this->info('Run `optimize:clear`...');
        $this->call('optimize:clear');

        $this->info('Run `config:clear`...');
        $this->call('config:clear');

        $this->info('Clearing cache (alternative to `cache:clear`)...');
        $configs = [
            base_path('bootstrap/cache/config.php'),
            base_path('bootstrap/cache/events.php'),
            base_path('bootstrap/cache/packages.php'),
            base_path('bootstrap/cache/routes-v7.php'),
            base_path('bootstrap/cache/services.php'),
        ];

        foreach ($configs as $config) {
            if (File::exists($config)) {
                $this->info('Delete '.$config);
                File::delete($config);
            }
        }

        $this->call('config:cache');

        $this->info('Optimize app...');
        // $this->call('optimize');
        $this->call('config:cache');
        $this->call('route:cache');
        $this->call('event:cache');
        // $this->call('view:cache');

        $this->info('Done.');

        return Command::SUCCESS;
    }
}
