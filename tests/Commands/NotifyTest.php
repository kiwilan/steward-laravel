<?php

namespace Kiwilan\Steward\Tests;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Kiwilan\Steward\Services\NotifyService;

beforeEach(function () {
    Config::set('steward.notify.discord', dotenv()['STEWARD_NOTIFY_DISCORD']);
});

it('can notify', function () {
    $data = dotenv();
    $test = $data['STEWARD_NOTIFY_DISCORD'];

    $notify = NotifyService::make()
        ->to(explode(':', $test))
        ->message('Notify test')
        ->send()
    ;

    expect($notify->isSuccess())->toBeTrue();
});

it('can notify from config', function () {
    $notify = NotifyService::make()
        ->message('Notify test from config')
        ->send()
    ;

    expect($notify->isSuccess())->toBeTrue();
});

it('can notify with command', function () {
    $data = dotenv();

    $success = Artisan::call('notify', [
        'message' => 'Notify test from command',
        '--options' => $data['STEWARD_NOTIFY_DISCORD'],
    ]);

    expect($success)->toBe(1);
});
