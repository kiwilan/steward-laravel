<?php

namespace Kiwilan\Steward\Commands\Setup;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Kiwilan\Steward\Commands\Commandable;

class SetupInstallCommand extends Commandable
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:install
                            {--p|production : run in production mode}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute main setup commands.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->title();

        $production = $this->optionBool('production', false);

        $this->info('Create .env file...');

        if (! file_exists(base_path('.env'))) {
            copy(base_path('.env.example'), base_path('.env'));
        }

        $this->info('Generating key...');

        Artisan::call('key:generate', [
            '--force' => true,
        ], $this->output);

        if ($production) {
            $this->dotenvProduction();
            $this->info('Production mode enabled.');
        }

        $this->info('Linking storage...');

        Artisan::call('storage:link', [
            '--force' => true,
        ], $this->output);

        $this->info('Migrating database...');

        Artisan::call('migrate', [
            '--force' => true,
        ], $this->output);

        $this->info('Seeding database...');

        Artisan::call('db:seed', [
            '--force' => true,
        ], $this->output);

        $this->info('Done.');

        return Command::SUCCESS;
    }

    private function dotenvProduction(): void
    {
        $env = file_get_contents(base_path('.env'));

        $env = str_replace('APP_ENV=local', 'APP_ENV=production', $env);
        $env = str_replace('APP_DEBUG=true', 'APP_DEBUG=false', $env);

        file_put_contents(base_path('.env'), $env);
    }
}
