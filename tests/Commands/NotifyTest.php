<?php

namespace Kiwilan\Steward\Tests;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Kiwilan\Steward\Services\NotifyService;

beforeEach(function () {
    Config::set('steward.notify.discord', dotenv()['STEWARD_NOTIFY_DISCORD']);
    Config::set('steward.notify.slack', dotenv()['STEWARD_NOTIFY_SLACK']);
});

it('can notify', function (string $application) {
    $data = dotenv();
    $app = strtoupper($application);
    $test = $data["STEWARD_NOTIFY_{$app}"];

    $notify = NotifyService::make()
        ->to(explode(':', $test))
        ->application($application)
        ->message('Notify test')
        ->send()
    ;

    expect($notify->isSuccess())->toBeTrue();
})->with(['discord', 'slack']);

it('can notify from config', function (string $application) {
    $notify = NotifyService::make()
        ->application($application)
        ->message('Notify test from config')
        ->send()
    ;

    expect($notify->isSuccess())->toBeTrue();
})->with(['discord', 'slack']);

it('can notify with command', function (string $application) {
    $data = dotenv();
    $app = strtoupper($application);

    $success = Artisan::call('notify', [
        'message' => 'Notify test from command',
        '--options' => $data["STEWARD_NOTIFY_{$app}"],
    ]);

    expect($success)->toBe(1);
})->with(['discord', 'slack']);
