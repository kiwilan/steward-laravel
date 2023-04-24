<?php

use Dotenv\Dotenv;
use Kiwilan\Steward\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

function dotenv(): array
{
    $root = getcwd();
    $dotenv = Dotenv::createMutable($root);

    return $dotenv->load();
}
