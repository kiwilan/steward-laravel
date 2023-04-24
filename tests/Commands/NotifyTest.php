<?php

namespace Kiwilan\Steward\Tests;

use Illuminate\Support\Facades\Artisan;
use Kiwilan\Steward\Services\NotifyService;

it('can notify', function () {
    $data = dotenv();

    $success = Artisan::call('notify', [
        'message' => 'Notify test',
        '--inline-servers' => $data['TESTING_NOTIFY_SERVERS'],
    ]);

    expect($success)->toBe(1);
});

it('can notify with sendto option', function () {
    $data = dotenv();
    $test = $data['TESTING_NOTIFY_SENDTO'];

    $success = Artisan::call('notify', [
        'message' => 'Notify test with sendto',
        '--sendto' => $test,
    ]);

    expect($success)->toBe(1);
});

it('can notify with service', function () {
    $data = dotenv();
    $test = $data['TESTING_NOTIFY_SENDTO'];

    $notify = NotifyService::make('Notify test with service', sendto: $test);
    $success = $notify->send();

    expect($success)->toBeTrue();
});
