<?php

use Kiwilan\Steward\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

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
