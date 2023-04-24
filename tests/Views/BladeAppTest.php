<?php

namespace Kiwilan\Steward\Tests\Views;

it('can be render', function () {
    /** @var TestCase $this */
    $renderedView = (string) $this->blade('<x-stw-app :vite="[]" />');
    expect($renderedView)->toBeString();
});
