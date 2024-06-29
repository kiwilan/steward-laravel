<?php

namespace Kiwilan\Steward\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Kiwilan\Steward\Settings\GeneralSettings;

class ProcessManifest implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct() {}

    /**
     * Execute the job.
     */
    public function handle()
    {
        Log::info('Generating manifests');

        $settings = app(GeneralSettings::class);
        $this->setBrowserConfig($settings->site_color);
        $this->setSiteWebManifest($settings->site_name, $settings->site_color);
    }

    private function setBrowserConfig(string $color)
    {
        if (! $color) {
            return;
        }

        $config = file_get_contents(__DIR__.'/stubs/browserconfig.xml');
        $config = str_replace('{$color}', $color, $config);

        file_put_contents(public_path('browserconfig.xml'), $config);
    }

    private function setSiteWebManifest(string $name, string $color)
    {
        if (! $name || ! $color) {
            return;
        }

        $manifest = file_get_contents(__DIR__.'/stubs/site.webmanifest');
        $manifest = str_replace('{$name}', $name, $manifest);
        $manifest = str_replace('{$color}', $color, $manifest);

        file_put_contents(public_path('site.webmanifest'), $manifest);
    }
}
