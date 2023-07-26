<?php

use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\Testing\TestView;
use Kiwilan\Steward\Tests\TestCase;

// uses(TestCase::class)->in(__DIR__);
uses(TestCase::class, InteractsWithViews::class)->in('.');

function dotenv(): array
{
    $path = __DIR__.'/../';
    $lines = file($path.'.env');
    $dotenv = [];

    foreach ($lines as $line) {
        if (! empty($line)) {
            $data = explode('=', $line);
            $key = $data[0];
            $value = $data[1];

            $key = trim($key);
            $value = trim($value);

            $dotenv[$key] = $value;
        }
    }

    return $dotenv;
}

/**
 * Render the contents of the given Blade template string.
 *
 * @param  \Illuminate\Contracts\Support\Arrayable|array  $data
 * @return \Illuminate\Testing\TestView
 */
function blade(string $template, $data = [])
{
    $tempDirectory = sys_get_temp_dir();

    if (! in_array($tempDirectory, ViewFacade::getFinder()->getPaths())) {
        ViewFacade::addLocation(sys_get_temp_dir());
    }

    $tempFileInfo = pathinfo(tempnam($tempDirectory, 'laravel-blade'));

    $tempFile = $tempFileInfo['dirname'].'/'.$tempFileInfo['filename'].'.blade.php';

    file_put_contents($tempFile, $template);

    return new TestView(view($tempFileInfo['filename'], $data));
}
