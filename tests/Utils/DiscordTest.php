<?php

namespace Kiwilan\Steward\Tests;

use Kiwilan\Steward\Utils\Discord;

it('can notify', function () {
    $data = dotenv();
    $url = $data['STEWARD_DISCORD'];

    $sended = Discord::make($url)
        ->message('Notify test')
        ->send()
    ;

    expect($sended)->toBeTrue();
});
