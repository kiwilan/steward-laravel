<?php

namespace Kiwilan\Steward\Tests;

it('can be render', function () {
    /** @var TestCase $this */
    $renderedView = (string) $this->blade('<x-stw-app :vite="[]" />');
    expect($renderedView)->toBeString();
});
