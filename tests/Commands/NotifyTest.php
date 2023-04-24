<?php

namespace Kiwilan\Steward\Tests;

use Illuminate\Support\Facades\Artisan;

it('can notify', function () {
    $data = dotenv();

    $success = Artisan::call('notify', [
        'message' => 'Notify test',
        '--servers' => $data['TESTING_NOTIFY_SERVERS'],
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
