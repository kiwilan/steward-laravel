<?php

namespace Kiwilan\Steward\Commands\Filament;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Kiwilan\Steward\Commands\Commandable;

class FilamentConfigCommand extends Commandable
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'steward:filament:config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup filament/filament config with Steward command.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->title();
        $this->newLine();

        $configs = File::allFiles(config_path('/'));
        $exists = false;

        foreach ($configs as $file) {
            if ($file->getFilenameWithoutExtension() === 'filament') {
                $exists = $file;
            }
        }

        if (! $exists) {
            Artisan::call('vendor:publish', [
                '--tag' => 'filament-config',
            ], $this->getOutput());
        }

        $replace = [
            "\Filament\Http\Livewire\Auth\Login::class" => "\App\Filament\Pages\Auth\Login::class",
            "Widgets\FilamentInfoWidget::class" => "Kiwilan\Steward\Filament\Widgets\WelcomeInfoWidget::class",
            "'dark_mode' => false" => "'dark_mode' => true",
            "'should_show_logo' => true" => "'should_show_logo' => false",
            "'is_collapsible_on_desktop' => false" => "'is_collapsible_on_desktop' => true",
        ];

        $config_str = file_get_contents(config_path('filament.php'));

        foreach ($replace as $old => $new) {
            $config_str = str_replace($old, $new, $config_str);
        }

        unlink(config_path('filament.php'));
        file_put_contents(config_path('filament.php'), $config_str);

        // TODO
        // brand and brand-icon
        // charts
        // publish login Filament/Pages/Auth

        return 0;
    }
}
