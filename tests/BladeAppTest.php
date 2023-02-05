<?php

namespace Kiwilan\Steward\Tests;

it('can be render', function () {
    /** @var TestCase $this */
    $renderedView = (string) $this->blade('<x-stw-app dark-mode />');
    expect($renderedView)->toBeString();

    dump($renderedView);
});
