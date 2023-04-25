<?php

use Dotenv\Dotenv;
use Kiwilan\Steward\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

function dotenv(): array
{
    $dotenv = Dotenv::createUnsafeImmutable(__DIR__.'/../');

    return $dotenv->load();
}
