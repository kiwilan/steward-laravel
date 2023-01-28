<?php

namespace Kiwilan\Steward\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Kiwilan\Steward\Services\ZiggyTypeService;

class GenerateTypeCommand extends CommandSteward
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:type
                            {type=models : Generate `models` or `ziggy` types.}
                            {--fake-team : Add fake Team to User model.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate TypeScript types for Eloquent models or Ziggy.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->title();
        $type = $this->argument('type');
        $fakeTeam = $this->option('fake-team') ?? false;

        if ($type === 'models') {
            $this->models($fakeTeam);
        }

        if ($type === 'ziggy') {
            $this->ziggy();
        }

        $this->info('Done.');

        return CommandSteward::SUCCESS;
    }

    private function models(bool $fakeTeam)
    {
        Artisan::call('typeable:models', [
            '--fake-team' => $fakeTeam,
        ]);
    }

    private function ziggy()
    {
        $converter = ZiggyTypeService::make();

        $this->info('Generated Ziggy types.');
    }
}
