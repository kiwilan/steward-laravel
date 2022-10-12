<?php

namespace Kiwilan\Steward\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class StewardPhpCsFixerCommand extends CommandSteward
{
    public $signature = 'steward:php-cs-fixer';

    public $description = 'Install PHP-CS-Fixer';

    public string $path = '';
    public string $stub = '';

    public function handle(): int
    {
        $this->title();

        // TODO
        // Add .php-cs-fixer.cache to .gitignore
        $this->path = base_path('.php-cs-fixer.dist.php');
        $this->stub = 'stubs/.php-cs-fixer.dist.php';

        if (File::exists($this->path)) {
            $this->comment('PHP-CS-Fixer config already exists');
            if ($this->confirm('Do you want to overwrite it?', false)) {
                $this->info('Overwriting...');
                File::delete($this->path);
                $this->make();
            } else {
                $this->info('Skipped.');
            }
        } else {
            $this->make();
        }

        $this->comment('All done');

        return Command::SUCCESS;
    }

    public function make()
    {
        $config = File::get(__DIR__ . "/stubs/{$this->stub}");
        File::put($this->path, $config);
        $this->info('PHP-CS-Fixer config created');
    }
}
