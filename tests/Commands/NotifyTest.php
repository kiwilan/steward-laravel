<?php

namespace Kiwilan\Steward\Tests;

use Illuminate\Support\Facades\Artisan;
use Kiwilan\Steward\Services\NotifyService;

it('can notify', function () {
    $data = dotenv();
    $test = $data['STEWARD_NOTIFY_DISCORD_SERVER'];

    $notify = NotifyService::make()
        ->to(options: explode(':', $test))
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

// it('can notify with command', function () {
//     $data = dotenv();

//     $success = Artisan::call('notify', [
//         'message' => 'Notify test from command',
//         '--inline-servers' => $data['TESTING_NOTIFY_SERVERS'],
//     ]);

//     expect($success)->toBe(1);
// });
